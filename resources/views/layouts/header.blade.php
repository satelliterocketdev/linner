<div class="navbar navbar-expand-lg navbar-light nav-fill w-100 hidden-xs">
  <div class="container-fluid justify-content-between">
    <div class="navbar-brand" style="font-size: 12px">
      <div class="input-group">
        <!-- <div class="input-group-prepend"> -->
          <button type="button" class="btn btn-outline-dark sidebarCollapse">
            <i class="fas fa-align-left"></i>
          </button>
        <!-- </div> -->
        <!-- <input type="text" class="header-search-input form-control shadow-sm" placeholder="Search" aria-label="SearchBar">
        <div class="input-group-append">
          <button class="btn btn-outline-dark shadow-sm" type="button"><i class="fas fa-search"></i></button>
        </div> -->
      </div>
    </div>

    <!-- <div class="logo-style font-weight-bold px-3 py-1">
      {{ config('app.name') }}
    </div> -->
    <form class="form-inline mr-2">
      <div class="row justify-content-center align-items-center">
        <!-- <i class="fas fa-2x fa-user-circle mx-2"></i>
        <div class="mx-4">  Project Name</div> -->
        <button class="btn btn-light" type="button" data-toggle="modal" data-target="#sampleModal"><i
            class="fas fa-bell"></i></button>
        <div class="dropdown m-1" style="font-family: Oswald; color: #14cc32">
          <button class="btn btn-light" type="button" id="dropdownMenuButton" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cogs"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">{{__("User_Manual")}}</a>
            <a class="dropdown-item" href="#">{{__("Change_Log")}}</a>
            <a class="dropdown-item" href="#">{{__("Contact_at_LINE")}}</a>
            <a class="dropdown-item" href="#">{{__("LINE_purchasing_teaching_materials")}}</a>
            <a class="dropdown-item" href="#">{{__("LINE_utilization_blog")}}</a>
          </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
        </form>
      </div>
    </form>
  </div>
</div>

<div class="navbar navbar-expand-lg navbar-light visible-xs nav-fill w-100 p-2">
  <div class="container-fluid justify-content-between">
    <div class="logo-style font-weight-bold px-3 py-1">
      {{ config('app.name') }}
    </div>
    <div>
      <button class="btn btn-outline-dark m-1" type="button" id="dropdownMenuButton" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-cogs"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="#">{{__("User_Manual")}}</a>
        <a class="dropdown-item" href="#">{{__("Change_Log")}}</a>
        <a class="dropdown-item" href="#">{{__("Contact_at_LINE")}}</a>
        <a class="dropdown-item" href="#">{{__("LINE_purchasing_teaching_materials")}}</a>
        <a class="dropdown-item" href="#">{{__("LINE_utilization_blog")}}</a>
      </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      {{ csrf_field() }}
    </form>
    <div class="input-group p-2">
      <div class="input-group-prepend">
        <button type="button" class="btn btn-outline-dark sidebarCollapse">
          <i class="fas fa-align-left"></i>
        </button>
      </div>
      <input type="text" class="header-search-input form-control" placeholder="Search" aria-label="SearchBar">
      <div class="input-group-append">
        <button class="btn btn-outline-dark" type="button"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </div>
</div>