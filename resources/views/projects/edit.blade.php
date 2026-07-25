@extends('layouts.app')
@section('content')<div class="card"><h1>Edit Project</h1><form method="POST" action="{{ route('projects.update', $project) }}">@csrf @method('PUT') @include('projects.form', ['button' => 'Save changes'])</form></div>@endsection
