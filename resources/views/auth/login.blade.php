@extends('auth.main')

@section('content')

<div class="container h-100">

  <div class="row justify-content-center align-items-center h-100">
  <br /><br />    
    <div class="container-fluid">
    <center>
      <!-- <div class="row align-items-center justify-content-center">
        <div id="logo" class="text-center">
          <img src="{{ asset('img/logo.png') }}" style="width: 60px" />
        </div>
      </div> -->
      <div class="login-wrapper row align-items-center justify-content-center p-2">
        <img src="/img/logo_large.png" class="w-100" />
        <h1 class="logo">LINNER</h1>
      </div>

      <br />

      @if(session()->get( 'data' ))
      <div class="login-wrapper alert alert-danger" role="alert">
        {{ session()->get( 'data' ) }}
      </div>
      @endif

      <form class="form-horizontal" method="POST" action="{{ route('login') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <div class="row m-2">
            <label for="email" class="control-label">{{ __("auth.email_address") }}</label>
            <input id="email" type="email" class="form-control" name="email" required autofocus>
            @if ($errors->has('email'))
            <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
            </span>
            @endif
          </div>
        </div>
        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <div class="row m-2">
            <label for="password" class="control-label">{{ __("auth.password") }}</label>
            <input id="password" type="password" class="form-control" name="password" required>
            @if ($errors->has('password'))
            <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
            </span>
            @endif
          </div>
        </div>
        <div class="login-wrapper form-group">
          <div class="row align-items-center justify-content-center m-2">
            <button type="submit" class="btn btn-gradient-primary btn-round btn-sm btn-block">
              {{ __("auth.login") }}
            </button>
          </div>
        </div>
      </form>
      <div class="exlink-wrapper">
        <div>{{-- <div class="exlink"> --}}
            <a class="btn btn-link" href="{{ route('password.request') }}">{{ __("auth.forgot_password") }}</a>
        </div>
      </div>
      <a class="btn btn-link" href="{{action('Auth\RegisterController@showRegistrationForm')}}">{{ __("auth.register_your_account") }}</a>
      </center>
    </div>

  </div>
</div>
@endsection

@section('footer-styles')
<style>
  .login-wrapper {
    max-width: 380px;
    width: 100%;
  }
  body {
    background-color: white;
  }
  h1.logo {
    color: #073B4C;
    font-size:5em;
    font-family: Arial;
    font-weight: bold;
    letter-spacing: 0.1em;
  }
  label {
    color: #609cfa;
    font-size: 0.7em;
    font-weight: bold;
  }
  .btn-block {
    width:150px;
  }
  .exlink-wrapper {
    position: relative;
  }
  .exlink {
    position:absolute;
    right:0;
    top:-50px;
  }
  a {
    font-weight: bold;
  }
</style>
@endsection