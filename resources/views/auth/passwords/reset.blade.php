@extends('auth.main')

@section('content')

<div class="container h-100">

  <div class="row justify-content-center align-items-center h-100">
    <div class="container-fluid text-center">

      <!-- <div class="row align-items-center justify-content-center">
        <div id="logo" class="text-center">
          <img src="{{ asset('img/logo.png') }}" style="width: 60px" />
        </div>
      </div> -->
      <?php if (session()->get('status')) { ?>
      <div class="alert alert-success">
        {{ session()->get('status') }}
      </div>
      <?php } ?>
      <div class="login-wrapper row align-items-center justify-content-center p-2 mx-auto mb-3">
        <img src="/img/logo_large.png" class="w-100" />
        <h1 class="logo">LINNER</h1>
      </div>

      @if(session()->get('status'))
      <div class="alert alert-danger" role="alert">
        {{ __(session()->get('status')) }}
      </div>
      @endif

      <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}
        <input type="hidden" name="email" value="{{$email}}">
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('password') ? ' has-error' : '' }} mx-auto">
          <div class="row">

          <label for="password" class="control-label">{{__('auth.input_reset_password')}}</label>
              <input id="password" type="password" class="form-control" name="password" required>
  
              @if ($errors->has('password'))
              <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
              @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} mx-auto">
          <div class="row">
          <label for="password-confirm" class="control-label">{{__('auth.input_reset_password_confirm')}}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

            @if ($errors->has('password_confirmation'))
            <span class="help-block">
              <strong>{{ $errors->first('password_confirmation') }}</strong>
            </span>
            @endif
          </div>
        </div>

        <div class="login-wrapper align-items-center justify-content-center form-group mx-auto">
          <button type="submit" class="btn btn-primary mx-auto">
            {{__('auth.reset_password')}}
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