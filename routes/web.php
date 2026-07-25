<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/projects');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/email/verify', fn () => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('projects.index')->with('success', 'Email verified successfully.');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    try {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'A fresh verification link has been sent.');
    } catch (\Throwable $exception) {
        report($exception);
        return back()->with('error', 'Email delivery is unavailable. Use the local demo code below while developing.');
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::post('/email/verify-demo', function (Illuminate\Http\Request $request) {
    abort_unless(app()->environment('local'), 404);
    $request->validate(['code' => ['required', 'in:000000']]);
    $request->user()->markEmailAsVerified();
    return redirect()->route('projects.index')->with('success', 'Demo email verification completed.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.demo');

Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge', [AuthController::class, 'showMfaChallenge'])->name('mfa.challenge');
    Route::post('/two-factor-challenge', [AuthController::class, 'verifyMfa'])->name('mfa.verify');
    Route::post('/two-factor-challenge/resend', [AuthController::class, 'resendMfa'])->name('mfa.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)->except(['index', 'show']);
    Route::patch('projects/{project}/tasks/{task}/complete', [TaskController::class, 'complete'])->name('projects.tasks.complete');
});
