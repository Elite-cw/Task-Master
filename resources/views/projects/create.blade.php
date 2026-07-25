@extends('layouts.app')
@section('content')<div class="card"><h1>New Project</h1><form method="POST" action="{{ route('projects.store') }}">@csrf @include('projects.form', ['button' => 'Create project'])</form></div>@endsection
