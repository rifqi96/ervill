@extends('layouts.auth.master')

@section('title')
    Login Page
@endsection

@section('content')
    <div class="form-top">
        <div class="form-top-left">
            <h3>Please login first</h3>
            <p>Enter your username and password to log on:</p>
        </div>
        <div class="form-top-right">
            <i class="fa fa-lock"></i>
        </div>
    </div>
    <div class="form-bottom">
        <form role="form" action="{{ route('login') }}" method="post" class="login-form">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <label class="sr-only" for="form-username">Username</label>
                <input type="text" name="username" placeholder="Username..." class="form-username form-control" id="form-username">
                @if ($errors->has('username'))
                    <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label class="sr-only" for="form-password">Password</label>
                <input type="password" name="password" placeholder="Password..." class="form-password form-control" id="form-password">
                @if ($errors->has('password'))
                    <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
            <button type="submit" class="btn">Sign in!</button>
        </form>
    </div>
@endsection