@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @guest
                        You ain't logged in boyo!
                    @else
                        You are logged in!

                        <ul>
                            <li>
                                <a href="/dashboard">Go to the dashboard</a>
                            </li>
                        </ul>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endsection
