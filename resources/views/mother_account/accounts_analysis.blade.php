@extends('layouts.app-mother')

@section('content')
<div id="account_analysis" class="container-fluid" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <tutorial></tutorial>
  <div class="row justify-content-center align-items-stretch">
    <div class="flex-item col-sm-12 px-0 px-sm-2">
      <div class="summary border rounded bg-white p-3">
        <div class="col-sm-9 px-0">
          <h2 class="summary">{{__("dashboard.account_analysis")}}</h2>
        </div>
      </div>
    </div>

    <div class="col-sm-7 px-0 px-sm-2">
    <!-- 登録者数・率 -->
      <div class="flex-item">
        <div class="p-flex border rounded bg-white p-3">
          <h3>{{__("dashboard.subscribers_rate")}}</h3>
          <div class="my-3" style="display:flex;">
            <button class="change-btn btn btn-info px-2 px-sm-5 mr-3 active" data-category="monthly">@{{ $t('message.monthly') }}</button>
            <button class="change-btn btn btn-info px-2 px-sm-5" data-category="accrual">@{{ $t('message.accumulation') }}</button>
          </div>
          <canvas id="subscribersRate" width="300" height="80"></canvas>
        </div>
      </div>

      <div class="row mx-0">
        <!-- 配信可能通数 -->
        <div class="flex-item col-md-6 px-0 pr-md-2">
          <div class="p-flex border rounded bg-white p-3 container-deliverable">
            <h3>{{__("dashboard.deliverable")}}</h3>
            <div class="col p-0">
              <div class="doughnut">
                <canvas id="deliverable" width="140" height="140"></canvas>
                <div class="indoughnut">
                  <p style="font-size:1.5em;font-weight:bold;line-height: 1em;" class="mt-2">@{{plan.level}}</p>
                  <p style="font-size:0.5em;font-weight:bold;">PLAN</p>
                  <p style="font-size:0.4em;color:#aaaaaa;">@{{ $t('message.remaining_number') }}</p>
                  <p style="font-size:0.9em;">@{{plan.deliveries_left.toLocaleString()}}</p>
                  <hr />
                  <p style="font-size:0.9em;">@{{plan.delivery_count.toLocaleString()}}</p>
                  <div class="upgradecolor"><button class="btn btn-primary btn-sm px-2 py-1"><a href="/plan">{{__('dashboard.upgrade')}}</a></button></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 登録者数推移 -->
        <div class="flex-item col-md-6 px-0">
          <div class="p-flex border rounded bg-white p-3">
              <h3 style="margin-right: auto;">{{__("dashboard.subscribers")}}</h3>
              <ul class="nav subscribers-navs">
                <li class="nav-item">
                  <a class="nav-link active" href="#" data-category="day">@{{ $t('message.day') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#" data-category="week">@{{ $t('message.week') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#" data-category="month">@{{ $t('message.month') }}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#" data-category="year">@{{ $t('message.year') }}</a>
                </li>
              </ul>
            <canvas id="subscribers" width="240" height="100%"></canvas>
          </div>
        </div>
      </div>
    </div>

      <!-- クリック成約数・率 -->
      <div class="flex-item col-sm-5 px-0 px-sm-2">
        <div class="p-flex border rounded bg-white p-3">
          <h3>{{__("dashboard.click_contract")}}</h3>
          <div style="display:flex;">
          <ul class="nav click-contract-navs" style="margin-right: auto;">
            <li class="nav-item">
              <a class="nav-link active" href="#" data-category="day">@{{ $t('message.day') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="week">@{{ $t('message.week') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="month">@{{ $t('message.month') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="year">@{{ $t('message.year') }}</a>
            </li>
          </ul>
          </div>
          <canvas id="clickContract" class="clickContract" width="300" height="50"></canvas>
          <canvas id="clickContract2" class="clickContract" width="300" height="50"></canvas>
          <canvas id="clickContract3" class="clickContract" width="300" height="50"></canvas>
          <canvas id="clickContract4" class="clickContract" width="300" height="50"></canvas>
          <canvas id="clickContract5" class="clickContract" width="300" height="50"></canvas>
          <canvas id="clickContract6" class="clickContract" width="300" height="50"></canvas>
        </div>
      </div>
      
      
    </div>
  </div>
</div>

@endsection

@section('footer-scripts')
<script type='text/javascript' src="{{ asset('js/chart.bundle.js') }}"></script>
<script type='text/javascript' src="{{ asset('js/chart.js') }}"></script>

<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script>
  const messages = {
    en: {
      message: {
        no_registered_users: 'Number of registered users / rate',
        monthly: 'Monthly',
        accumulation: 'Accumulation',
        no_deliverables: 'Number of deliverables',
        no_deliveries: 'Number of deliveries',
        remaining_number: 'Remaining number',
        no_registered_users_data: 'Number of registered users',
        click_contracts: 'Click contracts / rate',
        day: 'Day',
        week: 'Week',
        month: 'Month',
        year: 'Year',
      }
    },
    ja: {
      message: {
        no_registered_users: '登録者数/率',
        monthly: '月間',
        accumulation: '累積',
        no_deliverables: '配信可能通数',
        no_deliveries: '配信数',
        remaining_number: '残通数',
        no_registered_users_data: '登録者数推移',
        click_contracts: 'クリック成約数/率',
        day: '日',
        week: '週',
        month: '月',
        year: '年'
      }
    }
  }
  const i18n = new VueI18n({
    locale: '{{config('app.locale')}}',
    messages,
  })
  var account_analysis = new Vue({
    i18n,
    el: '#account_analysis',
    data() {
      return {
        loadingCount: 0,
        accounts: [],
        plan: {
            deliveries_left: 0,
            delivery_count: 0
        },
        summaryData: {},
        tutorial: {{ var_export(Auth::user()->finished_tutorial) }}
      }
    },
    methods: {
      percentage(partialValue, totalValue) {
        return (100 * partialValue) / totalValue;
      },
      getData() {
        this.loadingCount++
        axios.get('/accounts_analysisList').then((res) => {
          this.accounts = res.data.accounts
          this.plan = res.data.plan
          this.summaryData = res.data.summaryData

          console.log(res.data.summaryData)
          var chart1 = document.getElementById("subscribersRate");
          var chart2 = document.getElementById("deliverable");
          var chart3 = document.getElementById("subscribers");
          var chart4 = document.getElementById("clickContract");
          var chart5 = document.getElementById("clickContract2");
          var chart6 = document.getElementById("clickContract3");
          var chart7 = document.getElementById("clickContract4");
          var chart8 = document.getElementById("clickContract5");
          var chart9 = document.getElementById("clickContract6");
          var subscribersRate = null;
          var deliverable = null;
          var subscribers = null;
          var clickContract = null;
          var clickContract2 = null;
          var clickContract3 = null;
          var clickContract4 = null;
          var clickContract5 = null;
          var clickContract6 = null;

          // 登録者数・率
          var chart1gradient= chart1.getContext("2d").createLinearGradient(0, 0, 0, $('#subscribersRate').width());
          chart1gradient.addColorStop(0, "#4bdeb9ff");
          chart1gradient.addColorStop(1, "#30a7ceff");

          function subscribersRateSet(labels, data) {
            if (subscribersRate) {
              subscribersRate.destroy();
            }
            subscribersRate = new Chart(chart1, {
              type: 'bar',
              data: {
                xLabels: labels,
                yLabels: ['0%', '10%', '20%', '30%'],
                datasets: [{
                  label: '登録者数',
                  data: data,
                  backgroundColor: chart1gradient,
                  borderColor: chart1gradient,
                  borderWidth: 0
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.39
                  }],
                  yAxes: [{
                    ticks: {
                      beginAtZero: true
                    }
                  }]
                }
              }
            });
          }
          let accounts = _.map(this.accounts, 'name')
          let accountsMonthlyFollowers = _.map(this.accounts, 'monthly_followers')
          let accountsFollowers = _.map(this.accounts, 'all_followers_count')
          subscribersRateSet(accounts, accountsMonthlyFollowers);

          $('.change-btn').click(function(){
            $('.change-btn').removeClass('active');
            $(this).addClass('active');

            var category = $(this).data('category');
            if (category == 'monthly') {
              subscribersRateSet(accounts, accountsMonthlyFollowers);
            } else if (category == 'accrual') {
              subscribersRateSet(accounts, accountsFollowers);
            }
            return false;
          });

          // 配信可能通数
          var chart2gradient= chart2.getContext("2d").createLinearGradient(0, 0, $('#deliverable').width(), 0);
          chart2gradient.addColorStop(0, "#20aaf0aa");
          chart2gradient.addColorStop(0.7, "#f09090aa");

          deliverable = new Chart(chart2, {
            type: 'doughnut',
            data: {
              labels: ["配信数", "残通数"],
              datasets: [{
                data: [this.plan.deliveries_left, this.plan.delivery_count - this.plan.deliveries_left],
                backgroundColor: [
                  chart2gradient,
                  'rgba(99,99,99,0.5)'
                ],
                borderColor: [
                  chart2gradient,
                  'rgba(99,99,99,0.5)'
                ],
                borderWidth: 0,
                hoverBorderWidth: 0,
                borderAlign: "inner"
              }]
            },
            options: {
              cutoutPercentage: 80
            }
          });

          // 登録者数推移
          var days = []
          var registeredFollowersPerDay = []
          for(let i = 0; i < this.summaryData.dayData.length; i++) {
            let date = new Date(this.summaryData.dayData[i].day.date)
            days.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            registeredFollowersPerDay.push(this.summaryData.dayData[i].registeredFollowers)
          }

          var weeks = []
          var registeredFollowersPerWeek = [];
          for(let i = 0; i < this.summaryData.weekData.length; i++) {
            let date = new Date(this.summaryData.weekData[i].week.date)
            weeks.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            registeredFollowersPerWeek.push(this.summaryData.weekData[i].registeredFollowers)
          }

          var months = []
          var registeredFollowersPerMonth = [];
          for(let i = 0; i < this.summaryData.monthData.length; i++) {
            let date = new Date(this.summaryData.monthData[i].month.date)
            months.push(date.toISOString().slice(0,7).replace(/-/g, '/')) 
            registeredFollowersPerMonth.push(this.summaryData.monthData[i].registeredFollowers)
          }

          var years = []
          var registeredFollowersPerYear = [];
          for(let i = 0; i < this.summaryData.yearData.length; i++) {
            let date = new Date(this.summaryData.yearData[i].year.date)
            years.push(date.toISOString().slice(0,4).replace(/-/g, '/')) 
            registeredFollowersPerYear.push(this.summaryData.yearData[i].registeredFollowers)
          }

          var chart3gradient= chart3.getContext("2d").createLinearGradient(0, 0, 0, $('#subscribers').width());
          chart3gradient.addColorStop(0, "#4bdeb9ff");
          chart3gradient.addColorStop(1, "#ffff");

          function subscribersSet(labels, data) {
            if (subscribers) {
              subscribers.destroy();
            }
            subscribers = new Chart(chart3, {
              type: 'line',
              data: {
                xLabels: labels,
                yLabels: ['0', '30,000', '60,000', '100,000'],
                datasets: [{
                  label: '登録者数',
                  data: data,
                  backgroundColor: chart3gradient,
                  borderColor: chart3gradient,
                  borderWidth: 0
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.39
                  }],
                  yAxes: [{
                    ticks: {
                      beginAtZero: true
                    }
                  }]
                }
              }
            });
          }
          subscribersSet(days, registeredFollowersPerDay);

          // クリック成約数・率
          var chart4gradient1 = chart4.getContext("2d").createLinearGradient(0, 0, 0, $('.clickContract').width());
          chart4gradient1.addColorStop(0, "#30a7ceff");
          var chart4gradient2 = chart4.getContext("2d").createLinearGradient(0, 0, 0, $('.clickContract').width());
          chart4gradient2.addColorStop(0, "#4bdeb9ff");

          function clickContractSet(labels, data) {
            if (clickContract) {
              clickContract.destroy();
            }
            clickContract = new Chart(chart4, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContractSet( ["Account1"], [[54067/20], [28090/70]]);

          function clickContract2Set(labels, data) {
            if (clickContract2) {
              clickContract2.destroy();
            }
            clickContract2 = new Chart(chart5, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContract2Set( ["Account2"], [[54067/20], [28090/70]]);

          function clickContract3Set(labels, data) {
            if (clickContract3) {
              clickContract3.destroy();
            }
            clickContract3 = new Chart(chart6, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContract3Set( ["Account3"], [[54067/20], [28090/70]]);

          function clickContract4Set(labels, data) {
            if (clickContract4) {
              clickContract4.destroy();
            }
            clickContract4 = new Chart(chart7, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContract4Set( ["Account4"], [[54067/20], [28090/70]]);

          function clickContract5Set(labels, data) {
            if (clickContract5) {
              clickContract5.destroy();
            }
            clickContract5 = new Chart(chart8, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContract5Set( ["Account5"], [[54067/20], [28090/70]]);

          function clickContract6Set(labels, data) {
            if (clickContract6) {
              clickContract6.destroy();
            }
            clickContract6 = new Chart(chart9, {
              type: 'horizontalBar',
              data: {
                labels: labels,
                datasets: [{
                  label: '成約',
                  data: data[0],
                  backgroundColor: chart4gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart4gradient2,
                  borderWidth: 0,
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    categoryPercentage: 0.23,
                  }],
                  yAxes: [{
                    display: true,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                    ticks: {
                      beginAtZero: false,
                    }
                  }]
                },
              }
            });
          }
          clickContract6Set( ["Account6"], [[54067/20], [28090/70]]);

          $('.click-contract-navs a.nav-link').click(function(){
            $('.click-contract-navs a.nav-link').removeClass('active');
            $(this).addClass('active');
            var category = $(this).data('category');
            if (category == 'day') {
              clickContractSet( ["Account1"], [[54067/20], [28090/70]]),
              clickContract2Set( ["Account2"], [[54067/20], [28090/70]]),
              clickContract3Set( ["Account3"], [[54067/20], [28090/70]]),
              clickContract4Set( ["Account4"], [[54067/20], [28090/70]]),
              clickContract5Set( ["Account5"], [[54067/20], [28090/70]]),
              clickContract6Set( ["Account6"], [[54067/20], [28090/70]]);
            } else if (category == 'week') {
              clickContractSet( ["Account1"], [[54067/20], [28090/70]]),
              clickContract2Set( ["Account2"], [[54067/20], [28090/70]]),
              clickContract3Set( ["Account3"], [[54067/20], [28090/70]]),
              clickContract4Set( ["Account4"], [[54067/20], [28090/70]]),
              clickContract5Set( ["Account5"], [[54067/20], [28090/70]]),
              clickContract6Set( ["Account6"], [[54067/20], [28090/70]]);
            } else if (category == 'month') {
              clickContractSet( ["Account1"], [[54067/20], [28090/70]]),
              clickContract2Set( ["Account2"], [[54067/20], [28090/70]]),
              clickContract3Set( ["Account3"], [[54067/20], [28090/70]]),
              clickContract4Set( ["Account4"], [[54067/20], [28090/70]]),
              clickContract5Set( ["Account5"], [[54067/20], [28090/70]]),
              clickContract6Set( ["Account6"], [[54067/20], [28090/70]]);
            } else if (category == 'year') {
              clickContractSet( ["Account1"], [[54067/20], [28090/70]]),
              clickContract2Set( ["Account2"], [[54067/20], [28090/70]]),
              clickContract3Set( ["Account3"], [[54067/20], [28090/70]]),
              clickContract4Set( ["Account4"], [[54067/20], [28090/70]]),
              clickContract5Set( ["Account5"], [[54067/20], [28090/70]]),
              clickContract6Set( ["Account6"], [[54067/20], [28090/70]]);
            }
            return false;
          });
          $('.subscribers-navs a.nav-link').click(function(){
            $('.subscribers-navs a.nav-link').removeClass('active');
            $(this).addClass('active');
            var category = $(this).data('category');
            if (category == 'day') {
              subscribersSet(days, registeredFollowersPerDay);
            } else if (category == 'week') {
              subscribersSet(weeks, registeredFollowersPerWeek);
            } else if (category == 'month') {
              subscribersSet(months, registeredFollowersPerMonth);
            } else if (category == 'year') {
              subscribersSet(years, registeredFollowersPerYear);
            }
            return false;
          });
        })
        .finally(() => this.loadingCount--)
      }
    },
    beforeMount() {
      this.getData();
    }
  })
</script>
@endsection

@section('footer-styles')
<style>
  div.flex-item {
    margin-bottom: 15px;
  }
  div.summary {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
  }
  div.rounded {
    height: 100%;
    box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  }
  div.p-flex {
    padding: 6px 15px;
  }
  h3 {
    font-size: 1.3em;
  }
  .stats {
    background-color: #f0f0f0;
  }
  h3.stats{
    color: #777777;
    margin: 1.5em 0;
  }
  h3.enrollment, h4.enrollment, h3.enable_users, h4.enable_users, h3.active_users, h4.active_users, h3.block_number, h4.block_number {
    display: inline-block;
    color: #888888;
  }
  h4.enrollment, h4.enable_users, h4.active_users, h4.block_number {
    font-size: 1em;
    width:49%;
    padding-left: 1em;
    margin:0;
  }
  h3.enrollment, h3.enable_users, h3.active_users, h3.block_number {
    font-size: 1.8em;
    text-align: right;
    width: 49%;
    padding-right: 1em;
    margin:0;
  }
  h3.enrollment {
    color: #20ccaa;
  }
  h3.enable_users {
    color: #44aaFF;
  }
  h3.active_users {
    color: #FF8888;
  }
  h3.block_number {
    color: #000000;
  }
  .doughnut {
    position: relative;
  }
  .indoughnut {
    position: absolute;
    top: 25%;
    left: 0;
    right: 0;
    margin: 0 auto;
    width: 100%;
    text-align: center;
  }
  .indoughnut p, .indoughnut hr {
    padding:0;
    margin:0;
    color:black;
  }
  .indoughnut hr {
    max-width: 50%;
    margin: 0 auto;
  }

  .indoughnut .btn {
    font-size:0.5em;
    padding: 3px;
    margin:0;
    background-image: linear-gradient(60deg, #ff60dd 0%, #aa40ff 100%);
    transition: .4s;
    border: none;
  }
  .nav-link {
    display: block;
    padding: 0 .5rem .1rem .5rem;
    margin: 0 .1rem .5rem .1rem;
  }
  .nav-link.active {
    border-bottom: 2px #4bdeb9ff solid;
    color: #4bdeb9ff;
  }

  
  #summary {
    max-height: 250px;
    margin: auto;
  }
  .clickContract {
    max-height: 230px;
    margin: auto;
  }
  #deliverable {
    max-width: 230px;
    max-height: 230px;
    margin: auto;
  }
  #subscribers {
    max-height: 230px;
    margin: auto;
  }

  .container-deliverable {
      padding-bottom: 1rem !important;
  }
  @media (max-width: 992px) {
    .container-deliverable {
        padding-bottom: 30px !important;
    }
  }
  @media (max-width: 767px) {
    .container-deliverable {
        padding-bottom: 1rem !important;
    }
  }
</style>
@endsection