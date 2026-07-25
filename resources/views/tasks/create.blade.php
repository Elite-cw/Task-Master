@extends('layouts.app')
@section('content')<div class="card"><p class="meta"><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a> /</p><h1>New Task</h1><form method="POST" action="{{ route('projects.tasks.store', $project) }}">@csrf @include('tasks.form', ['button' => 'Create task'])</form></div>@endsection
