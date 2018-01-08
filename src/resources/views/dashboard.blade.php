@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-16">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Project Dashboard - <a href="/add-project">Add a new project</a>
                </div>

                <div class="panel-body">
                    <table id="project-dashboard-table">
                        <tr>
                            <th>Project</th>
                            <th>Live URL</th>
                            <th>Info</th>
                            <th>Test URL</th>
                            <th>Info</th>
                            <th>Actions</th>
                        </tr>
                        @each('dashboard.row', $projects, 'project')
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
