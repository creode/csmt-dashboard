@extends('layouts.app')

@section('project_name', 'Add Project')

@section('content')
        <div class="row">
            <div class="col-md-16">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Submit a new project
                    </div>

                    <div class="panel-body">

                        <form action="/project/add" method="post">
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    Please fix the following errors
                                </div>
                            @endif

                            {!! csrf_field() !!}
                            <div class="form-group{{ $errors->has('project_name') ? ' has-error' : '' }}">
                                <label for="project_name">Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name" placeholder="project name" value="{{ old('project_name') }}">
                                @if($errors->has('project_name'))
                                    <span class="help-block">{{ $errors->first('project_name') }}</span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('live_url') ? ' has-error' : '' }}">
                                <label for="live_url">Live tool url</label>
                                <input type="text" class="form-control" id="live_url" name="live_url" placeholder="URL" value="{{ old('live_url') }}">
                                @if($errors->has('live_url'))
                                    <span class="help-block">{{ $errors->first('live_url') }}</span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('test_url') ? ' has-error' : '' }}">
                                <label for="test_url">Test tool url</label>
                                <input type="text" class="form-control" id="test_url" name="test_url" placeholder="URL" value="{{ old('test_url') }}">
                                @if($errors->has('test_url'))
                                    <span class="help-block">{{ $errors->first('test_url') }}</span>
                                @endif
                            </div>


                            <button type="submit" class="btn btn-default">Add Project</button>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
@endsection
