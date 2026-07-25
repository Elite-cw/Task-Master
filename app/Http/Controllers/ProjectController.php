<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->withCount('tasks')->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(ProjectRequest $request)
    {
        try {
            $project = $request->user()->projects()->create($request->validated());
            return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
        } catch (QueryException $exception) {
            report($exception);
            return back()->withInput()->with('error', 'We could not save the project. Please try again.');
        }
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);
        $status = $request->query('status');
        abort_unless(is_null($status) || in_array($status, Task::STATUSES, true), 404);
        $tasks = $project->tasks()->when($status, fn ($query) => $query->where('status', $status))->latest()->get();
        return view('projects.show', compact('project', 'tasks', 'status'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        try {
            $project->update($request->validated());
            return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
        } catch (QueryException $exception) {
            report($exception);
            return back()->withInput()->with('error', 'We could not update the project. Please try again.');
        }
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (QueryException $exception) {
            report($exception);
            return back()->with('error', 'We could not delete the project. Please try again.');
        }
    }
}
