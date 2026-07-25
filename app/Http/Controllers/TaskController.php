<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;

class TaskController extends Controller
{
    private function ensureProjectAccess(Project $project): void
    {
        $this->authorize('view', $project);
    }

    private function ensureTaskBelongsToProject(Project $project, Task $task): void
    {
        abort_unless($task->project_id === $project->id, 404);
        $this->authorize('view', $task);
    }

    public function create(Project $project)
    {
        $this->ensureProjectAccess($project);
        return view('tasks.create', compact('project'));
    }

    public function store(TaskRequest $request, Project $project)
    {
        $this->ensureProjectAccess($project);
        try {
            $project->tasks()->create($request->validated());
            return redirect()->route('projects.show', $project)->with('success', 'Task created successfully.');
        } catch (QueryException $exception) {
            report($exception);
            return back()->withInput()->with('error', 'We could not save the task. Please try again.');
        }
    }

    public function edit(Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProject($project, $task);
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(TaskRequest $request, Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProject($project, $task);
        try {
            $wasDone = $task->status === 'Done';
            $task->update($request->validated());
            $message = (! $wasDone && $task->status === 'Done')
                ? 'Task updated and marked Done. Great work!'
                : 'Task updated successfully.';
            return redirect()->route('projects.show', $project)->with('success', $message);
        } catch (QueryException $exception) {
            report($exception);
            return back()->withInput()->with('error', 'We could not update the task. Please try again.');
        }
    }

    public function complete(Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProject($project, $task);

        if ($task->status === 'Done') {
            return redirect()->route('projects.show', $project)->with('success', 'This task is already marked as Done.');
        }

        try {
            $task->update(['status' => 'Done']);
            return redirect()->route('projects.show', $project)->with('success', 'Task marked as Done. Nice work!');
        } catch (QueryException $exception) {
            report($exception);
            return back()->with('error', 'We could not mark the task as Done. Please try again.');
        }
    }

    public function destroy(Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProject($project, $task);
        try {
            $task->delete();
            return redirect()->route('projects.show', $project)->with('success', 'Task deleted successfully.');
        } catch (QueryException $exception) {
            report($exception);
            return back()->with('error', 'We could not delete the task. Please try again.');
        }
    }
}
