<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name') }}</title>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/simplebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/antd.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/video-js.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
  @yield('css-styles')

  <!-- Scripts -->
  <script>
    var baseUrl = "{{ url('') }}";
  </script>
  @yield('header-scripts')
</head>
<body>

  @include('layouts.sidebar')

  <main id="content" role="main">
    <div class="main-content" data-simplebar>
      @yield('content')
    </div>
  </main>

  <!-- Styles -->
  @yield('footer-styles')

  <!-- Scripts -->
  <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- TODO CDNはやめたがいいかもな -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/ja.js"></script>
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
  <script src="{{ asset('js/messages_' . config('app.locale') . '.min.js') }}"></script>
  <script src="{{ asset('js/video.min.js') }}"></script>
  <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('js/vue-i18n.min.js') }}"></script>
  <script type="text/javascript">
    axios.defaults.baseURL = "{{ url('/') }}";
    axios.defaults.headers.common = {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
  </script>
  <script type="text/javascript">
    // モバイル対応（ヘッダー・フッター固定分余白設定）
    if (window.innerWidth < 768) {
        var hsize = $('.sidebar').outerHeight(true) * 1.5;
        $("#content").css("padding-bottom", hsize + "px");
    }

    // サイドバー　ボタン群固定
    var hsize2 = $('.btn-sidebar').outerHeight(true) + 15;
    $(".menu-sidebar").css("margin-bottom", hsize2 + "px");

  </script>
  <script type="text/javascript">
    {{-- 実行時ルートパス（jsで使用するためグローバルで定義）'例：http://localhost:3000' --}}
    const GLOBAL_APP_ROOT_PATH = "{{ url('/') }}"
  </script>
  @php $user = Auth::user(); @endphp
  @if(property_exists('user','finished_tutorial') and $user->finished_tutorial == 0)
    <script type="text/javascript">
      //　これは安全だと言ったら嘘になります。
      $('a').click(function() { return false; });
    </script>
  @else
    <script type="text/javascript">
      //　これは安全だと言ったら嘘になります。
      $('a').click(function() { return true; });
    </script>
  @endif

  <script src="{{asset('js/components/custom-components/loading.js')}}"></script>
  @yield('footer-scripts')
  <script src="{{ asset('js/lodash.min.js') }}"></script>
  <script src="{{ asset('js/sidebar.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('google.api_key') }}&libraries=places"></script>

</body>
</html>