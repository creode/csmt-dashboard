@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h2>Enter your credentials</h2>

         <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            
            <input placeholder="Email address" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif

            <input placeholder="Password" id="password" type="password" class="form-control" name="password" required>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif

            <button type="submit" class="btn btn-primary">Login</button>

            <a class="btn btn-link" href="{{ route('password.request') }}">
                Forgot Your Password?
            </a>
         </form>
    </div>
</div>
@endsection
