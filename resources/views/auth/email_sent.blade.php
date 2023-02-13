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
      @if(session()->get('status'))
      <div class="alert alert-danger" role="alert">
        {{ __(session()->get('status')) }}
      </div>
      @endif
      <div class="login-wrapper row align-items-center justify-content-center p-2 mx-auto mb-3">
        <img src="/img/logo_large.png" class="w-100" />
        <h1 class="logo">LINNER</h1>
      </div>

      @if(session()->get( 'data' ))
      <div class="login-wrapper alert alert-danger" role="alert">
        {{ session()->get( 'data' ) }}
      </div>
      @endif
      <p>{{ Lang::get('register.phrase_one') }}​</p>
      <p>{{ Lang::get('register.phrase_two') }}​​</p>
      <p>{{ Lang::get('register.phrase_three') }}​​</p>
      <br>
      <p class="bg-light p-3">{{ Lang::get('register.email') }}​​</p>
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