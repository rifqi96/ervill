@extends('layouts.auth.master')

@section('title')
    Reset Password
@endsection

@section('content')
    <div class="form-top">
        <div class="form-top-left">
            <h3>Reset Password</h3>
            <p>Silahkan masukkan email anda untuk mereset password:</p>
        </div>
        <div class="form-top-right">
            <i class="fa fa-lock"></i>
        </div>
    </div>
    <div class="form-bottom">
        <form role="form" action="{{ route('password.email') }}" method="post" class="login-form">
            {{ csrf_field() }}

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label class="sr-only" for="form-username">Username</label>
                <input type="email" name="email" placeholder="Email..." class="form-email form-control" id="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">
                Send Password Reset Link
            </button>
        </form>
    </div>
@endsection