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

                            <hr/>

                            <div class="form-group{{ $errors->has('live_url') ? ' has-error' : '' }}">
                                <label for="live_url">Live tool url</label>
                                <input type="text" class="form-control" id="live_url" name="live_url" placeholder="URL" value="{{ old('live_url') }}">
                                @if($errors->has('live_url'))
                                    <span class="help-block">{{ $errors->first('live_url') }}</span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('live_credentials_user') ? ' has-error' : '' }}">
                                <label for="live_credentials_user">Live tool auth user (leave blank to generate)</label>
                                <input type="text" class="form-control" id="live_credentials_user" name="live_credentials_user" placeholder="user" value="{{ old('live_credentials_user') }}">
                                @if($errors->has('live_credentials_user'))
                                    <span class="help-block">{{ $errors->first('live_credentials_user') }}</span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('live_credentials_pass') ? ' has-error' : '' }}">
                                <label for="live_credentials_pass">Live tool auth pass</label>
                                <input type="password" class="form-control" id="live_credentials_pass" name="live_credentials_pass" placeholder="pass" value="{{ old('live_credentials_pass') }}">
                                @if($errors->has('live_credentials_pass'))
                                    <span class="help-block">{{ $errors->first('live_credentials_pass') }}</span>
                                @endif
                            </div>

                            <hr/>

                            <div class="form-group{{ $errors->has('test_url') ? ' has-error' : '' }}">
                                <label for="test_url">Test tool url</label>
                                <input type="text" class="form-control" id="test_url" name="test_url" placeholder="URL" value="{{ old('test_url') }}">
                                @if($errors->has('test_url'))
                                    <span class="help-block">{{ $errors->first('test_url') }}</span>
                                @endif
                            </div>
                            
                            <div class="form-group{{ $errors->has('test_credentials_user') ? ' has-error' : '' }}">
                                <label for="test_credentials_user">Test tool auth user (leave blank to generate)</label>
                                <input type="text" class="form-control" id="test_credentials_user" name="test_credentials_user" placeholder="user" value="{{ old('test_credentials_user') }}">
                                @if($errors->has('test_credentials_user'))
                                    <span class="help-block">{{ $errors->first('test_credentials_user') }}</span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('test_credentials_pass') ? ' has-error' : '' }}">
                                <label for="test_credentials_pass">Test tool auth pass</label>
                                <input type="password" class="form-control" id="test_credentials_pass" name="test_credentials_pass" placeholder="pass" value="{{ old('test_credentials_pass') }}">
                                @if($errors->has('test_credentials_pass'))
                                    <span class="help-block">{{ $errors->first('test_credentials_pass') }}</span>
                                @endif
                            </div>


                            <button type="submit" class="btn btn-default">Add Project</button>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
@endsection
