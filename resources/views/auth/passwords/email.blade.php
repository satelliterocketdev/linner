@extends('auth.main')

@section('content')
<div class="container h-100">
  @if(session()->get('status'))
    <div class="container mt-2">
      <div class="alert alert-success" role="alert">
        {{ __(session()->get('status')) }}
      </div>
    </div>
  @endif
  <div class="row justify-content-center align-items-center h-100">
    <div class="container-fluid text-center">
      <!-- <div class="row align-items-center justify-content-center">
        <div id="logo" class="text-center">
          <img src="{{ asset('img/logo.png') }}" style="width: 60px" />
        </div>
      </div> -->
      <div class="login-wrapper row align-items-center justify-content-center p-2 mx-auto mb-3">
        <img src="/img/logo_large.png" class="w-100" />
        <h1 class="logo">LINNER</h1>
      </div>

      @if(session()->get( 'data' ))
      <div class="login-wrapper alert alert-danger" role="alert">
        {{ session()->get( 'data' ) }}
      </div>
      @endif

      <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <div class="row justify-content-center mb-3 mx-2">
          <span>{{ __("auth.reset_explain") }}</span>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('name') ? ' has-error' : '' }}  mx-auto">
        <div class="row mx-2">
          <label for="name" class="control-label">{{ __("auth.account_id") }}</label>
            <input id="name" type="name" class="form-control" name="name" value="{{ old('name') }}" required>
            @if ($errors->has('name'))
              <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('email') ? ' has-error' : '' }}  mx-auto">
        <div class="row mx-2">
          <label for="email" class="control-label">{{ __("auth.email_address") }}</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            @if ($errors->has('email'))
              <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
          <button type="submit" class="btn btn-primary mx-auto">
            {{ __("auth.send_password_reset_link") }}
          </button>
        </div>
      </form>
      <a class="btn btn-link" href="{{ route('forgot.email') }}">{{ __("auth.forgot_email") }}</a>
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