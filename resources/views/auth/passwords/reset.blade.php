@extends('layouts.auth.master')

@section('title')
    Reset Password
@endsection

@section('content')
    <div class="form-top">
        <div class="form-top-left">
            <h3>Reset Password</h3>
            <p>Silahkan masukkan password baru anda:</p>
        </div>
        <div class="form-top-right">
            <i class="fa fa-lock"></i>
        </div>
    </div>
    <div class="form-bottom">
        <form role="form" action="{{ route('password.request') }}" method="post" class="login-form">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label class="sr-only" for="form-email">email</label>
                <input type="text" name="email" placeholder="Email..." class="form-email form-control" id="form-email">
                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
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

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label class="sr-only" for="form-password-confirm">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password..." class="form-password form-control" id="form-password-confirm">
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
                @endif
            </div>

            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
@endsection