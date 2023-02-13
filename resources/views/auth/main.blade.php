<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'LoopStep') }}</title>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/simplebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/antd.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/video-js.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.min.css') }}">
  @yield('css-styles')

  <!-- Scripts -->
  @yield('header-scripts')
</head>

<body>

  <main id="auth-content" role="main" class="h-100">
    @if (session('alert') || isset($alert))
      <div class="container mt-2">
        <div class="alert alert-info">
          {{ session('alert') }}
        </div>
      </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="container mt-2">
            <div class="alert alert-info">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        </div>
    @endif

        @yield('content')
  </main>

  <!-- Styles -->
  @yield('footer-styles')
  <style>
    html,
    body {
      height: 100%;
      overflow-y: auto;
    }
  </style>

  <!-- Scripts -->
  <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  @if(App::environment() !== 'production')
      <script src="{{ asset('js/vue.js') }}"></script>
  @else
      <script src="{{ asset('js/vue.min.js') }}"></script>
  @endif
  <script src="{{ asset('js/antd.min.js') }}"></script>
  <script src="{{ asset('js/axios.min.js') }}"></script>
  <script src="{{ asset('js/simplebar.js') }}"></script>
  <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/video.min.js') }}"></script>
  <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('js/vue-i18n.min.js') }}"></script>
  @yield('footer-scripts')
</body>

</html>