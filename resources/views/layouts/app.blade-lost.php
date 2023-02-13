<!DOCTYPE html>
<html lang="en">
	<head>
		<title> Dashboard </title>
		<meta charset="utf-8">
		<meta name="description" content="LineStep Dashboard">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="./css/all.min.css">
		<link rel="stylesheet" type="text/css" href="./css/jquery.mCustomScrollbar.min.css">
		<link rel="stylesheet" type="text/css" href="./css/style.css">
	</head>

	<body>
		<!--HEADER NAVIGATION BAR-->
		<div class="header navbar navbar-light bg-light">
			<div style="font-family: Oswald; color: #14cc32" href="#">
					<img src="./images/logo.png" width="35" height="35" class="d-inline-block align-items-center mr-2" alt="" />
			    	<button class="btn btn-outline-success" type="button" id="sidebarCollapse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						L I N E S T E P
					</button>
			</div>
			<div class="dropdown">
				<button class="btn btn-outline-success align-items-center" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <i class="far fa-user-circle"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
					<a class="dropdown-item" href="#">Account Name</a>
				    <a class="dropdown-item" href="#">My Page</a>
				    <a class="dropdown-item" href="#">LIGET addition request</a>
				</div>
			</div>
		</div>
		<!--SIDEBAR NAVIGATION-->
		<div class="wrapper">
			<nav id="sidebar">
				<ul class="list-unstyled components ml-2 mr-2">
					<div class="text-center m-2" style="font-size: 2rem"> 
					D A S H B O A R D 
					</div>
		            <div class="input-group mb-2">
					  <input type="text" class="form-control" aria-label="Search" aria-describedby="button-addon2">
					  <div class="input-group-append">
					    <button class="btn btn-outline-success" type="button" id="button-addon2">Search</button>
					  </div>
					</div>
					<li>
						<a href="{{ url('top') }}"><i class="fas fa-home mr-2"></i>Dashboard Home</a>	
					</li>
		            <li>
		                <a href="#subMenu1" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-star mr-2"></i>
		                	One to One Talk
		                </a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu1">
		                    <li>
		                        <a href="{{url('friends_list')}}">Friends List</a>
		                    </li>
		                    <li>
		                        <a href="#">Talk List</a>
		                    </li>
		                    <li>
		                        <a href="#">Individual Talk</a>
		                    </li>
		                </ul>
		            </li>
		            <li>
		                <a href="#subMenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-comments mr-2"></i>
		                	Message
		            	</a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu2">
		                    <li>
		                        <a href="#">Scenario Delivery</a>
		                    </li>
		                    <li>
		                        <a href="#">Simultaneous Delivery</a>
		                    </li>
		                    <li>
		                        <a href="#">Automatic Response</a>
		                    </li>
		                    <li>
		                        <a href="#">Template</a>
		                    </li>
		                    <li>
		                        <a href="#">Answer Form</a>
		                    </li>
		                    <li>
		                        <a href="#">Reminder Delivery</a>
		                    </li>
		                    <li>
		                        <a href="#">Setting when adding friends</a>
		                    </li>
		                </ul>
		            </li>
		            <li>
		                <a href="#subMenu3" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-user-friends mr-2"></i>
		                	Friend Attributes
		                </a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu3">
		                    <li>
		                        <a href="#">Tag Management</a>
		                    </li>
		                    <li>
		                        <a href="#">Friend information column management</a>
		                    </li>
		                    <li>
		                        <a href="#">Custom Search Management</a>
		                    </li>
		                </ul>
		            </li>
		            <li>
		                <a href="#subMenu4" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-chart-line mr-2"></i>
		                	Statistics
		                </a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu4">
		                    <li>
		                        <a href="#">URL Click Measurement</a>
		                    </li>
		                    <li>
		                        <a href="#">Conversion</a>
		                    </li>
		                    <li>
		                        <a href="#">Site Script</a>
		                    </li>
		                    <li>
		                    	<a href="#">Cross Analysis</a>
		                    </li>
		                    <li>
		                    	<a href="#">Inflow Route Analysis</a>
		                    </li>
		                </ul>
		            </li>
		            <li>
		                <a href="#subMenu5" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-folder-open mr-2"></i>
		                	Content
		                </a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu5">
		                    <li>
		                        <a href="#">Registration Media List</a>
		                    </li>
		                    <li>
		                        <a href="#">Rich menu</a>
		                    </li>
		                </ul>
		            </li>
		            <li>
		                <a href="#subMenu6" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
		                	<i class="fas fa-cogs mr-2"></i>
		                	LINE @ Setting
		                </a>
		                <ul class="collapse" style="list-style-type: circle" id="subMenu6">
		                    <li>
		                        <a href="#">LINE @ Manager</a>
		                    </li>
		                    <li>
		                        <a href="#">Account Setting</a>
		                    </li>
		                    <li>
		                        <a href="#">Staff Authority Setting</a>
		                    </li>
		                    <li>
		                        <a href="#">Data Migration</a>
		                    </li>
		                </ul>
		            </li>
		        </ul>
		        <div class="m-4">
		        	<div class="mb-3">Support</div>
		        	<button class="btn btn-success btn-sm btn-block" type="button" id="userManual">
				    	User Manual
					</button>
					<button class="btn btn-success btn-sm btn-block" type="button" id="purchasingMaterials">
				    	LINE purchasing teaching materials
					</button>
					<button class="btn btn-success btn-sm btn-block" type="button" id="utilizationBlog">
				    	LINE Utilization blog
					</button>
		        </div>

		    </nav>
		</div>
    <!--CONTENT SECTION-->
    @yield('content')
    <!--JAVASCRIPT-->
	<script type='text/javascript' src="./js/jquery-3.3.1.min.js"></script>
	<script type='text/javascript' src="./js/bootstrap.min.js"></script>
	<script type='text/javascript' src="./js/popper.js"></script>
	<script type='text/javascript' src="./js/jquery.mCustomScrollbar.min.js"></script>
	<script type='text/javascript'>
		$(document).ready(function () {
		    // $('#sidebarCollapse').on('click', function () {
		    //     $('#sidebar').toggleClass('active');
		    // });
		    $("#sidebar").mCustomScrollbar({
		         theme: "minimal"
		    });

		    $('#sidebarCollapse').on('click', function () {
		        // open or close navbar
		        $('#sidebar').toggleClass('active');
		        // close dropdowns
		        $('.collapse.in').toggleClass('in');
		        // and also adjust aria-expanded attributes we use for the open/closed arrows
		        // in our CSS
		        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
		    });
		}); 
	</script>
	</body>
</html>