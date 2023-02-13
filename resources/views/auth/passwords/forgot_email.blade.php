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
      <div class="login-wrapper row align-items-center justify-content-center p-2 mx-auto mb-3">
        <img src="/img/logo_large.png" class="w-100" />
        <h1 class="logo">LINNER</h1>
      </div>

      @if(session()->get( 'data' ))
      <div class="login-wrapper alert alert-danger" role="alert">
        {{ session()->get( 'data' ) }}
      </div>
      @endif

      <form class="form-horizontal" method="POST">
        {{ csrf_field() }}

        <div class="row justify-content-center mb-3 mx-2">
          <span>{{ __("auth.forgot_email_explain") }}</span>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
        <div class="row mx-2">
          <label for="member_number" class="control-label">{{ __("auth.member_number") }}</label>
            <input id="member_number" type="name" class="form-control" name="member_number" value="{{ old('member_number') }}" required>
            @if ($errors->has('member_number'))
              <span class="help-block">
                <strong>{{ $errors->first('member_number') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
        <div class="row mx-2">
          <label for="name" class="control-label">{{ __("auth.name") }}</label>
            <input id="name" type="name" class="form-control" name="name" value="{{ old('name') }}" required>
            @if ($errors->has('name'))
              <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
        <div class="row mx-2">
          <label for="phone" class="control-label">{{ __("auth.phone") }}</label>
            <input id="phone" type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
            @if ($errors->has('phone'))
              <span class="help-block">
                <strong>{{ $errors->first('phone') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
        <div class="row mx-2">
          <label for="birthdate" class="control-label">{{ __("auth.birthdate") }}</label>
            <input id="birthdate" type="date" class="form-control" name="birthdate" value="{{ old('birthdate') }}" required>
            @if ($errors->has('birthdate'))
              <span class="help-block">
                <strong>{{ $errors->first('birthdate') }}</strong>
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