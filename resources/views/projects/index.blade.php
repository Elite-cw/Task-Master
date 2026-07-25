@extends('layouts.app')
@section('content')
<div class="page-head"><div><p class="meta">Your workspace</p><h1>My Projects</h1><p class="meta">Plan, prioritize, and keep every task in view.</p></div><a class="btn" href="{{ route('projects.create') }}">+ New project</a></div>
@forelse($projects as $project)
<article class="card"><h2><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h2><p class="meta">{{ $project->description ?: 'No description added yet.' }}</p><p class="meta" style="margin-top:16px;font-size:.82rem;font-weight:650">{{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }}</p><div class="actions"><a class="btn small" href="{{ route('projects.show', $project) }}">View project</a><a class="btn small secondary" href="{{ route('projects.edit', $project) }}">Edit</a></div></article>
@empty <div class="card empty">No projects yet. Create your first project to get started.</div>
@endforelse
@endsection
