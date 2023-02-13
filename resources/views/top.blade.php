
@extends('layouts.app')
@section('content')
	@if (session('oauth'))
		<div class="alert alert-info">
			YOU ARE LOGGED IN USING LINE APP, PLEASE VISIT YOU EMAIL '{{session('email')}}' FOR YOU PASSWORD.
		</div>
	@endif
	<div class="row p-2 justify-content-between align-items-center">
		<div class="pl-4" style="font-size: 2rem; font-family: Oswald; color: white">
			{{__("Top")}}
		</div>
		<div class="pr-4">
			<a class="btn btn-primary btn-sm shadow" href="" role="button">{{__("Open_friends_list")}}</a>
		</div>
	</div>

	<div class="border align-items-center rounded bg-white p-4 m-3">
		<div class="row p-2">
			<i class="fas fa-dot-circle mr-2"></i>Summary
		</div>
		<div class="row align-items-start ">
			<div class="col-sm-9">
				<canvas id="summary" width="200" height="80"></canvas>
			</div>
			<div class="col-sm-3 rounded text-light" style="background-color: rgba(0,0,0,0.44)">
				<div class="row p-2">
					<h3>9,204</h3> 
				</div>
				<div class="row align-items-center p-2">
					<i class="fas fa-eye mr-2"></i> Enrollment
				</div>
				<hr>
				<div class="row p-2">
					<h3>7,500</h3> 
				</div>
				<div class="row align-items-center p-2">
					<i class="fas fa-chart-line mr-2"></i>Enable Users
				</div>
				<hr>
				<div class="row p-2">
					<h3>1,704 / 18%</h3> 
				</div>
				<div class="row align-items-center p-2">
					<i class="far fa-clock mr-2"></i>Block Number / %
				</div>
				<hr>
				<div class="row p-2">
					<button class="btn btn-success btn-block p-2" type="button">VIEW REPORTS</button>
				</div>
			</div>
		</div>
	</div>

	<div class="row justify-content-between align-items-center m-2">
		<div class="col-sm-4 align-items-center border rounded bg-white p-4 m-2">
			<i class="fas fa-dot-circle mr-2"></i>Enrollment
			<div class="row justify-content-center align-items-center p-2">
				<div class="col p-2">
					<canvas id="enrollment" width="100" height="120"></canvas>
				</div>
				<div class="col align-items-center p-2">
					<div class="row justify-content-center">
						52.64%
					</div>
					<div class="row justify-content-center" >
						YEAR
					</div>
					<hr>
					<div class="row justify-content-center">
						16.4%
					</div>
					<div class="row justify-content-center">
						WEEK
					</div>
					<hr>
					<div class="row justify-content-center">
						31.2%
					</div>
					<div class="row justify-content-center">
						MONTH
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3 align-items-center border rounded bg-white p-4 m-2">
			<i class="fas fa-dot-circle mr-2"></i>Subscriber
			<canvas id="subscriber" width="150" height="140"></canvas>
		</div>
		<div class="col-sm-4 align-items-center border rounded bg-white p-4 m-2">
			<i class="fas fa-dot-circle mr-2"></i>New Users
			<canvas id="newUsers" width="150" height="70"></canvas>
			<hr>
			<div class="row justify-content-center">
				<div class="col align-items-center">
					<div class="row justify-content-center">
						6.43%
					</div>
					<div class="row justify-content-center">
						New user growth
					</div>
				</div>
				<div class="col align-items-center">
					<div class="row justify-content-center">
							9.43%
					</div>
					<div class="row justify-content-center">
						Conversion rate
					</div>
				</div>
			</div>
		</div>
	</div>
{{$new_user}}
	<div class="row justify-content-center pl-2 m-2">
		<div class="col align-items-center border rounded bg-white">
			<div class="row align-items-center p-4">
				<i class="fas fa-dot-circle mr-2"></i>Schedule
			</div>
			<div class="row align-items-center justify-content-center p-4">
				<div class="col align-self-center">
					<button class="btn btn-info btn-block p-3" type="button">Feb 25</button>
					<button class="btn btn-light btn-block p-3" type="button">Feb 26</button>
					<button class="btn btn-dark btn-block p-3" type="button">Feb 27</button>
				</div>
				<div class="col align-self-center">
					<button class="btn btn-dark btn-block p-3" type="button">Feb 28</button>
					<button class="btn btn-dark  btn-block p-3" type="button">Mar 1</button>
					<button class="btn btn-light btn-block p-3" type="button">Mar 2</button>
				</div>
			</div>
			<div class="row justify-content-center p-4">
				<button class="btn btn-info btn-block btn-sm p-2" type="button">Edit</button>
			</div>
		</div>
		<div class="col align-items-between">
			<div class="align-items-center border rounded bg-white p-4 ">
				<i class="fas fa-dot-circle mr-2"></i>Click / Close Rate
				<canvas id="clickCloseRate" width="300" height="80"></canvas>
			</div>
			<div class="border align-items-center rounded bg-white p-4 mt-2">
				<div class="row p-2">
					<i class="fas fa-dot-circle mr-2"></i>Rates
				</div>
				<div class="row justify-content-around p-2">
					<div class="col-sm-4">
						<div class="row justify-content-center">
							<h2>64.24%</h2>
						</div>
						<div class="row justify-content-center">
							Presistence rate
						</div>
					</div>
					<div class="col-sm-4">
						<div class="row justify-content-center">
							<h2>53.22%</h2>
						</div>
						<div class="row justify-content-center">
							Video viewing rate
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	var chart = document.getElementById("summary");
	var chart1 = document.getElementById("enrollment");
	var chart2 = document.getElementById("subscriber");
	var chart3 = document.getElementById("newUsers");
	var chart4 = document.getElementById("clickCloseRate");

	var summary = new Chart(chart, {
		type: 'line',
		data: {
			labels: ["Feb 10", "Feb 11", "Feb 12", "Feb 13", "Feb 14", "Feb 15"],
			datasets:[{
				backgroundColor: "rgba(29, 205, 0, 0.6)",
				borderColor: "rgba(29, 205, 0, 1)",
				data: [20, 0, 10, 5, 20, 0]
			},{
				backgroundColor: "rgba(74, 216, 250, 0.6)",
				borderColor: "rgba(74, 216, 250, 1)",
				data: [30, 20, 5, 20, 5, 30]
			}]
		},

		options: {
			scales: {
				yAxes: [{
					display: true
				}]
			}
		}
	});

	
	var enrollment = new Chart(chart1, {
		type: 'doughnut',
		data: {
			// labels: ["Red", "Blue", "Yellow", ],
			datasets: [{
				label: '# of Votes',
				data: [12, 19, 3],
				backgroundColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)',
					'rgba(74, 216, 250, 0.6)'
				],
				borderColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)',
					'rgba(74, 216, 250, 0.6)'
				],
				borderWidth: 1
			}]
		}
	});

	var subscriber = new Chart(chart2, {
		type: 'bar',
		data: {
			labels: ["Red", "Green", "Blue"],
			datasets: [{
				label: '# of Votes',
				data: [12, 19, 27],
				backgroundColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)',
					'rgba(74, 216, 250, 0.6)'
				],
				borderColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)',
					'rgba(74, 216, 250, 0.6)'
				],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero:true
					}
				}]
			}
		}
	});

	var newUsers = new Chart(chart3, {
		type: 'doughnut',
		data: {
			labels: [
						'User',
						'New User'
			],
			datasets: [{
				data: [<?php print($user); ?>, <?php print($new_user); ?>],		
				backgroundColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)'
				],
				borderColor: [
					'rgba(255, 116, 116, 0.6)',
					'rgba(29, 205, 0, 0.6)'
				],
				borderWidth: 1
			}]
		},
		options: {
			rotation: 1 * Math.PI,
			circumference: 1 * Math.PI
		}
	});

	var clickCloseRate = new Chart(chart4, {
		type: 'horizontalBar',
		data: {
			datasets:[{
				backgroundColor: 'rgba(255, 116, 116, 0.6)',
				borderColor: 'rgba(255, 116, 116, 0.6)',
				data: [24]
			},{
				backgroundColor: 'rgba(29, 205, 0, 0.6)',
				borderColor: 'rgba(29, 205, 0, 0.6)',
				data: [78]
			}]
		},

		options: {
			tooltips: {
				mode: 'index',
				intersect: false
			},
			responsive: true,
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
				}
			}
	});

	</script>

@endsection