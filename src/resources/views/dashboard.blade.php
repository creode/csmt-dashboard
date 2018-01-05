@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
                <div class="title m-b-md">
                    Creode Dashboard
                </div>

                <div class="links">
                    <a href="/add-project">Add a new project</a>
                </div>

                <ul>
                    @each('dashboard.row', $projects, 'project')
                </ul>
@endsection
