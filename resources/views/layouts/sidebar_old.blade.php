<nav id="sidebar" data-simplebar>
  <div class="sidebar-header justify-content-center p-2" id="user-photo">
    <div id="user-photo" class="d-flex justify-content-center">
      <!-- <i class="fas fa-4x fa-user-circle p-4"></i> -->
        <div id="logo" class="my-4">
          <h1 style="color: white">LINNER</h1>
        </div>
    </div>
    <div class="d-flex flex-column justify-content-center">
      <a href="#userOptions" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-center"></i>User Name</a>
      <ul class="collapse dropdown-useraccount mb-0" id="userOptions">
        <li>
          <a class="dropdown-item" href="#">{{ __("My_page") }}</a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}">
            {{ __("Logout") }}
          </a>
        </li>
      </ul>
    </div>
  </div>
  <hr>
  <ul class="list-unstyled components ml-2 mr-2">
    <li>
      <a href="{{ route('dashboard') }}"><i class="fas fa-fw fa-home mr-2"></i>{{__('sidebar.dashboard')}}</a>
    </li>
    <hr>
    <label><i class="fas fa-fw fa-star mr-2"></i>{{__('sidebar.main_menu')}}</label>
    <li>
      <a href="{{route('stepmail.index')}}"><i class="far fa-fw fa-envelope mr-2"></i>{{(__('sidebar.stepmail'))}}</a>
    </li>
    <li>
      <a href="{{url('magazine')}}"><i class="far fa-fw fa-newspaper mr-2"></i>{{__('sidebar.magazine')}}</a>
    </li>
    <li>
      <a href="{{url('setting')}}"><i class="fas fa-fw fa-cog mr-2"></i>{{__('sidebar.setting')}}</a>
    </li>
    <hr>
    <label><i class="fas fa-fw fa-comments mr-2"></i>{{__('sidebar.individual_support')}}</label>
    <li>
      <a href="{{route('followers')}}"><i class="fas fa-users mr-2"></i>{{__('sidebar.followers')}}</a>
    </li>
    <li>
      <a href="{{url('#')}}"><i class="fas fa-fw fa-flag mr-2"></i>{{__('sidebar.timeline')}}</a>
    </li>
    <hr>
    <label><i class="fas fa-fw fa-user-friends mr-2"></i>{{__('sidebar.contents')}}</label>
    <li>
      <a href="{{ route('template') }}"><i class="fas fa-fw fa-stamp mr-2"></i>{{__('sidebar.template')}}</a>
    </li>
    <li>
      <a href="{{url('conversion')}}"><i class="fas fa-fw fa-level-down-alt mr-2"></i>{{__('sidebar.conversion')}}</a>
    </li>
    <li>
      <a href="{{url('crossanalysis')}}"><i class="fas fa-chart-bar mr-2"></i>{{__('sidebar.cross_analysis')}}</a>
    </li>
    <li>
      <a href="{{url('#')}}"><i class="far fa-fw fa-calendar-alt mr-2"></i>{{__('sidebar.autoreply')}}</a>
    </li>
    <li>
      <a href="{{url('transmittedmedia')}}"><i class="far fa-fw fa-clock mr-2"></i>{{__('sidebar.transmitted_media')}}</a>
    </li>
    <li>
      <a href="{{route('tags')}}"><i class="fas fa-fw fa-tags mr-2"></i>{{__('sidebar.tags')}}</a>
    </li>
    <li>
      <a href="{{url('#')}}"><i class="fas fa-fw fa-plus mr-2"></i>{{__('sidebar.add_info')}}</a>
    </li>
    <li>
      <a href="{{url('error')}}"><i class="fas fa-fw fa-exclamation-circle mr-2"></i>{{__('sidebar.error_contents')}}</a>
    </li>
    <hr>
    <label><i class="fas fa-fw fa-folder-open mr-2"></i>{{__('sidebar.my_account')}}</label>
    <li>
      <a href="{{url('accountinfo')}}"><i class="far fa-user-circle mr-2"></i>{{__('sidebar.account_info')}}</a>
    </li>
    <li>
      <a href="#"><i class="fas fa-fw fa-question mr-2"></i>{{__('sidebar.help')}}</a>
    </li>
    <li>
      <a href="{{url('#')}}"><i class="fas fa-fw fa-book mr-2"></i>{{__('sidebar.user_manual')}}</a>
    </li>
  </ul>
</nav>