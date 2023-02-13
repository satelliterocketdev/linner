<nav class="sidebar" style="background-color: #000;" data-simplebar>
  <div class="row justify-content-center align-items-stretch h-100">
    <div class="mother-bar">
      <ul class="menu-sidebar">
        <li class="pc"><a href="{{ route('dashboard') }}"><img class="logo" src="/img/logo-black.png" width="80px" /></a></li>
        <li><a class="btn-circle-gradation" style="background: linear-gradient(to bottom, #3107FF, #3EC6FF);" href="{{route('accounts.index')}}"><img src="/img/menu/mother_menu/accounts_list_icon.png" width="40%"/></a><p class="text-white">{{ __("sidebar.accounts_list") }}</p></li>
        <li><a class="btn-circle-gradation" style="background: linear-gradient(to bottom, #FF3634, #FFB05B);" href="{{route('accounts_analysis.index')}}"><img src="/img/menu/mother_menu/account_analysys_icon.png" width="35%"/></a><p class="text-white">{{ __("sidebar.accounts_analysis") }}</p></li>
        <li><a class="btn-circle-gradation" style="background: linear-gradient(to bottom, #FF0247, #FF5494);" href="{{route('friends.index')}}"><img src="/img/menu/mother_menu/friends_list_icon.png" width="50%"/></a><p class="text-white">{{ __("sidebar.all_friends_list") }}</p></li>
        <li><a class="btn-circle-gradation" style="background: linear-gradient(to bottom, #00FCB6, #00C454);" href="{{route('deliveries.index')}}"><img src="/img/menu/mother_menu/accounts_stream_icon.png" width="45%"/></a><p class="text-white">{{ __("sidebar.all_accounts_stream") }}</p></li>
      </ul>
      <ul class="pc btn-sidebar w-100">
          <li><a href="{{ route('dashboard') }}">{{ __("sidebar.return") }}</a></li>
          <li><a href="{{ route('logout') }}">{{ __("sidebar.logout") }}</a></li>
      </ul>
    </div>
  </div>
</nav>
<nav id="sp-header-nav" class="sp clearfix">
    <a href="{{ route('dashboard') }}" class="a-logo"><img class="logo" src="/img/logo-black.png" /></a>
    <div>
        <ul class="clearfix">
            <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt fa-2x"></i><br>{{ __("sidebar.logout") }}</a></li>
        </ul>
    </div>
</nav>