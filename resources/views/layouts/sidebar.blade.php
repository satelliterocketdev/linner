<nav class="sidebar" data-simplebar>
  <div class="row justify-content-center align-items-stretch h-100">
    <div class="first">
      <ul class="menu-sidebar">
        <li class="pc"><a href="{{ route('dashboard') }}"><img class="logo" src="/img/menu/logo.png" width="80px" /></a></li>
        <li><a class="btn-circle" href="#sidemenu_mail"><img src="/img/menu/mail.png" width="60%"/><p>{{ __("sidebar.mail") }}</p></a></li>
        <li><a class="btn-circle" href="#sidemenu_advance"><img src="/img/menu/advance.png" width="40%"/><p>{{ __("sidebar.advance") }}</p></a></li>
        <li><a class="btn-circle" href="#sidemenu_friend"><img src="/img/menu/friend.png" width="45%"/><p>{{ __("sidebar.friend") }}</p></a></li>
        <li><a class="btn-circle" href="#sidemenu_analysis"><img src="/img/menu/analysys.png" width="45%"/><p>{{ __("sidebar.analysis") }}</p></a></li>
        <li><a class="btn-circle-gray" href="{{url('accountinfo')}}"><p>＠</p></a><p class="p-sidevar-account">{{ __("sidebar.account") }}</p></li>
      </ul>
      <ul class="pc btn-sidebar">
          @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
            <li><a href="{{ url('accounts') }}">{{ __("sidebar.mother_account") }}</a></li>
            <li><a href="{{ url('inqueries') }}">{{ __("sidebar.inqueries") }}</a></li>
            <li><a href="{{ url('settlement') }}">{{ __("sidebar.paymentinformation") }}</a></li>
          @endcan
            <li><a href="{{ route('logout') }}">{{ __("sidebar.logout") }}</a></li>
          @if (App::environment() == 'local')
            <li><a href="{{ url('/line_api_debug') }}">{{ __("sidebar.line_api_debug") }}</a></li>
          @endif
      </ul>
    </div>
    <div class="second">
      <a class="sidemenu-close pc"><i class="fa fa-angle-double-left fa-3x"></i></a>
      <a class="sidemenu-close sp"><i class="fa fa-angle-double-up fa-3x my-2"></i></a>
      <ul id="sidemenu_mail">
        @if (Gate::check(App\Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE))
          <li><a class="btn-square" href="{{url('magazine')}}"><img src="/img/menu/magazine.png" width="45%"/><p>{{ __("sidebar.magazine") }}</p></a></li>
        @endif
        @if (Gate::check(App\Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE))
          <li><a class="btn-square" href="{{route('stepmail.index')}}"><img src="/img/menu/step.png" width="45%"/><p>{{ __("sidebar.stepmail") }}</p></a></li>
        @endif
        @if (Gate::check(App\Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE))
          <li><a class="btn-square" href="{{url('auto_answer_setting')}}"><img src="/img/menu/autoreply.png" width="45%"/><p>{{ __("sidebar.autoreply") }}</p></a></li>
        @endif
      </ul>
      @if(Gate::check(App\Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE))
      <ul id="sidemenu_advance" class="sidemenu_admin">
      @else
      <ul id="sidemenu_advance">
      @endif
        @if(Gate::check(App\Role::ROLE_TAG_MANAGEMENT_AVAILABLE))
          <li><a class="btn-square" href="{{route('tags')}}"><img src="/img/menu/tags.png" width="45%"/><p>{{ __("sidebar.tags") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE))
          <li><a class="btn-square" href="{{ url('template') }}"><img src="/img/menu/template.png" width="45%"/><p>{{ __("sidebar.template") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_RICH_MENU_AVAILABLE))
          <li><a class="btn-square" href="{{url('richmenu')}}"><img src="/img/menu/richmenu.png" width="45%"/><p>{{ __("sidebar.richmenu") }}</p></a></li>
        @endif
      </ul>
      @if(Gate::check(App\Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE))
      <ul id="sidemenu_friend" class="sidemenu_admin">
      @else
      <ul id="sidemenu_friend">
      @endif
        @if(Gate::check(App\Role::ROLE_FRIEND_MAIL_INVITE))
          <li><a class="btn-square" href="{{route('invitation.index')}}"><img src="/img/menu/invite.png" width="45%"/><p>{{ __("sidebar.invite") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE))
          <li><a class="btn-square" href="{{route('followers')}}"><img src="/img/menu/followers.png" width="45%"/><p>{{ __("sidebar.followers") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_FRIEND_TALK_LIST))
          <li><a class="btn-square" href="{{url('talk')}}"><img src="/img/menu/talk.png" width="45%"/><p>{{ __("sidebar.talk") }}</p></a></li>
        @endif
        <!-- <li><a class="btn-square" href="#"><img src="/img/menu/journey.png" width="45%"/><p>{{ __("sidebar.journey") }}</p></a></li> -->
      </ul>
      @if(Gate::check(App\Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE))
      <ul id="sidemenu_analysis" class="sidemenu_admin">
      @else
      <ul id="sidemenu_analysis">
      @endif
        @if(Gate::check(App\Role::ROLE_CONVERSION_AVAILABLE))
          <li><a class="btn-square" href="{{url('conversion')}}"><img src="/img/menu/conversion.png" width="45%"/><p>{{ __("sidebar.conversion") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_URL_CLICK_MEASUREMENT_AVAILABLE))
          <li><a class="btn-square" href="{{url('clickrate')}}"><img src="/img/menu/clickanalysys.png" width="45%"/><p>{{ __("sidebar.clickanalysis") }}</p></a></li>
        @endif
        @if(Gate::check(App\Role::ROLE_SURVEYS_RESULT_AVAILABLE))
            <li><a class="btn-square" href="{{url('survey')}}"><img src="/img/menu/surveys.png" style="width: auto; max-height: 40px; padding-bottom: 7px"/><p>{{ __("sidebar.survey_result") }}</p></a></li>
        @endif
        <!-- <li><a class="btn-square" href="#"><img src="/img/menu/taganalysys.png" width="45%"/><p>{{ __("sidebar.taganalysis") }}</p></a></li> -->
      </ul>
      <ul id="sidemenu_account">
        <li><a class="btn-square" href="#"><p></p></a></li>
        <li><a class="btn-square" href="#"><p></p></a></li>
        <li><a class="btn-square" href="#"><p></p></a></li>
      </ul>
    </div>
  </div>
</nav>
<nav id="sp-header-nav" class="sp clearfix">
    <a href="{{ route('dashboard') }}" class="a-logo"><img class="logo" src="/img/menu/logo.png" /></a>
    <div>
        <ul class="clearfix">
            <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt fa-2x"></i><br>{{ __("sidebar.logout") }}</a></li>
            @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
              <li><a class="menu-owner" href="{{ url('settlement') }}"><i class="fas fa-yen-sign fa-2x"></i><br>{{ __("sidebar.paymentinformation") }}</a><br></li>
              <li><a class="menu-owner" href="{{ url('inqueries') }}"><i class="far fa-envelope fa-2x"></i><br>{{ __("sidebar.inqueries") }}</a><br></li>
              <li><a class="menu-owner" href="{{ url('accounts') }}"><i class="fas fa-users fa-2x"></i><br>{{ __("sidebar.mother_account") }}</a><br></li>
            @endcan
        </ul>
    </div>
</nav>