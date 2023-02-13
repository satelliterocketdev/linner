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
      <p>{{ __('auth.email_sent') }}​</p>
      <p>{{ __('auth.email_sent_alert') }}​</p>
      <p>{{ __('auth.contact_if') }}​</p>
      <br>
      <a href="/login">戻る</a>
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