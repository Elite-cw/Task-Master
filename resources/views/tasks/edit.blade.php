@extends('layouts.app')
@section('content')<div class="card"><p class="meta"><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a> /</p><h1>Edit Task</h1><form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}">@csrf @method('PUT') @include('tasks.form', ['button' => 'Save changes'])</form></div>@endsection
