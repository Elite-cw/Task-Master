<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\LoginCodeMail;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password']),
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        try {
            event(new Registered($user));
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('verification.notice')->with('success', 'Check your email and verify your address before accessing your workspace.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::validate($credentials)) {
            return back()->withErrors(['email' => 'The supplied credentials do not match our records.'])->onlyInput('email');
        }
        $user = User::where('email', $credentials['email'])->firstOrFail();
        if (! $user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('verification.notice')->with('error', 'Verify your email address before signing in.');
        }
        try {
            $this->sendTwoFactorCode($user);
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['email' => 'We could not send your security code. Please try again shortly.'])->onlyInput('email');
        }
        $request->session()->put(['mfa_user_id' => $user->id, 'mfa_remember' => $request->boolean('remember')]);
        return redirect()->route('mfa.challenge')->with('success', 'A six-digit security code has been sent to your verified email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function showMfaChallenge(Request $request)
    {
        abort_unless($request->session()->has('mfa_user_id'), 403);
        return view('auth.mfa');
    }

    public function verifyMfa(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $user = User::find($request->session()->get('mfa_user_id'));
        if (! $user || ! $user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            $request->session()->forget(['mfa_user_id', 'mfa_remember']);
            return redirect()->route('login')->with('error', 'Your security code expired. Please sign in again.');
        }
        if (! Hash::check($request->input('code'), $user->two_factor_code)) {
            return back()->withErrors(['code' => 'That security code is not valid.']);
        }
        $user->forceFill(['two_factor_code' => null, 'two_factor_expires_at' => null])->save();
        Auth::login($user, (bool) $request->session()->pull('mfa_remember'));
        $request->session()->forget('mfa_user_id');
        $request->session()->regenerate();
        return redirect()->intended(route('projects.index'));
    }

    public function resendMfa(Request $request)
    {
        $user = User::find($request->session()->get('mfa_user_id'));
        abort_unless($user, 403);
        try {
            $this->sendTwoFactorCode($user);
            return back()->with('success', 'A new security code has been sent.');
        } catch (\Throwable $exception) {
            report($exception);
            return back()->with('error', 'We could not resend your security code.');
        }
    }

    private function sendTwoFactorCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();
        Mail::to($user->email)->send(new LoginCodeMail($code));
    }
}
