@extends('layouts.app')

@section('content')
<div id="dashboard" class="container-fluid" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <div class="row justify-content-center align-items-stretch">
    <div class="flex-item col-sm-12">
      <div class="summary border rounded bg-white">
        <div class="col-sm-9 p-flex">
          <h3 class="summary">{{__("dashboard.summary")}}</h3>
          <ul class="nav summary-navs">
            <li class="nav-item">
              <a class="nav-link active" href="#" data-category="day">{{__("dashboard.day")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="weak">{{__("dashboard.week")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="month">{{__("dashboard.month")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="year">{{__("dashboard.year")}}</a>
            </li>
          </ul>
          <canvas id="summary" width="200" height="80"></canvas>
        </div>
        <div class="col-sm-3 stats">
          <h3 class="stats">{{__("dashboard.stats")}}</h3>
          <div>
            <h4 class="enrollment">{{__("dashboard.enrollment")}}</h4><h3 class="enrollment">@{{ summaryData.totalRegisteredFollowers }}</h3>
          </div>
          <hr>
          <div>
            <h4 class="enable_users">{{__("dashboard.enable_users")}}</h4><h3 class="enable_users">@{{ summaryData.totalRegisteredFollowers - blockedUsers.totalBlockedUsers }}</h3>
          </div>
          <hr>
          <div>
            <h4 class="active_users">{{__("dashboard.active_users")}}</h4><h3 class="active_users">@{{ activityCount.totalActivities ? activityCount.totalActivities : 0 }}</h3>
          </div>
          <hr>
          <div>
            <h4 class="block_number w-100">{{__("dashboard.block_number")}}</h4><br /><h3 class="block_number w-100">@{{ blockedUsers.totalBlockedUsers }} / @{{ percentage(blockedUsers.totalBlockedUsers, summaryData.totalRegisteredFollowers) }}%</h3>
          </div>
          <hr>
        </div>
      </div>
    </div>

      <div class="flex-item col-sm-12">
        <div class="p-flex border rounded bg-white">
          <h3>{{__("dashboard.click_analysis")}}</h3>
          <div style="display:flex;">
          <ul class="nav click-analysis-navs" style="margin-right: auto;">
          <li class="nav-item">
              <a class="nav-link active" href="#" data-category="day">{{__("dashboard.day")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="weak">{{__("dashboard.week")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="month">{{__("dashboard.month")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="year">{{__("dashboard.year")}}</a>
            </li>
          </ul>
          <span style="color:#2dc2ff;margin-right:20px;">{{__("dashboard.number_deliveries")}}</span>
          <span style="color:#b0ffd3;margin-right:20px;">{{__("dashboard.clicks")}}</span>
          <span style="color:#1240f0;margin-right:20px;">{{__("dashboard.uniques")}}</span>
          </div>
          <canvas id="clickAnalysis" width="300" height="80"></canvas>
        </div>
      </div>

      <div class="flex-item col-sm-4 pr-0 pr-sm-2">
        <div class="p-flex border rounded bg-white container-deliverable">
          <h3>{{__("dashboard.deliverable")}}</h3>
          <div class="col p-0">
            <div class="doughnut">
              <canvas id="deliverable" width="140" height="140"></canvas>
              <div class="indoughnut">
                <template v-if="isAdmin">
                    <p style="font-size:1.5em;font-weight:bold;line-height: 1em;" class="mt-2">@{{ plan.level }}</p>
                    <p style="font-size:0.5em;font-weight:bold;">PLAN</p>
                </template>
                <p style="font-size:0.4em;color:#aaaaaa;">{{__("dashboard.remaining_number")}}</p>
                <p style="font-size:0.9em;">@{{ plan.remaining_deliveries.toLocaleString() }}</p>
                <hr />
                <p style="font-size:0.9em;">@{{ plan.delivery_count.toLocaleString() }}</p>
                <div v-if="isAdmin" class="upgradecolor"><button class="btn btn-primary btn-sm"><a href="/plan">{{__('dashboard.upgrade')}}</a></button></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="flex-item col-sm-8">
        <div class="p-flex border rounded bg-white">
          <div style="display:flex;">
          <h3 style="margin-right: auto;">{{__("dashboard.subscribers")}}</h3>
          <ul class="nav subscribers-navs">
            <li class="nav-item">
              <a class="nav-link active" href="#" data-category="day">{{__("dashboard.day")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="weak">{{__("dashboard.week")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="month">{{__("dashboard.month")}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-category="year">{{__("dashboard.year")}}</a>
            </li>
          </ul>
          </div>
          <canvas id="subscribers" width="240" height="80"></canvas>
        </div>
      </div>

      <!-- 送信エラーmodal -->
      <a-modal :visible="fail_modal_visible" :centered="true" :footer="null" v-model="fail_modal_visible" width="50%" >
        <form method="post">
          <div class="row p-2 mb-3 divider">
            <div class="col d-flex justify-content-center">
              <h2>{{__("dashboard.send_fail")}}</h2>
            </div>
          </div>
          <div class="row p-2 mb-3 divider">
            <div class="col d-flex justify-content-center">
              <h3>{{__("dashboard.send_fail_details")}}</h3>
            </div>
          </div>
          <div class="row p-2">
            <div class="col d-flex justify-content-around">
              <button type="button" class="btn btn-light border" @click="closeFailModal">OK</button>
              <div class="upgradecolor"><button class="btn btn-primary btn-sm"><a href="/plan">{{__('dashboard.upgrade')}}</a></button></div>
            </div>
          </div>
        </form>
      </a-modal>

      <!-- 30%を切った時のmodal -->
      <a-modal :visible="first_modal" :centered="true" :footer="null" v-model="first_modal" width="50%" >
        <form method="post">
          <div class="row p-2 mb-3 divider">
            <div class="col d-flex justify-content-center">
              <h3>{{__("dashboard.first_upgrade_details")}}</h3>
            </div>
          </div>
          <div class="row p-2">
            <div class="col d-flex justify-content-around">
              <button type="button" class="btn btn-light border" @click="closeFirstModal">OK</button>
              <div class="upgradecolor"><button class="btn btn-primary btn-sm"><a href="/plan">{{__('dashboard.upgrade')}}</a></button></div>
            </div>
          </div>
          <div class="row">
            <div class="col d-flex justify-content-center">
              <button class="btn btn-link">{{__("dashboard.never_show_again")}}</button>
            </div>
          </div>
        </form>
      </a-modal>

      <!-- 10%を切った時のmodal -->
      <a-modal :visible="second_modal" :centered="true" :footer="null" v-model="second_modal" width="50%" >
        <form method="post">
          <div class="row p-2 mb-3 divider">
            <div class="col d-flex justify-content-center">
              <h2>おっと！</h2>
            </div>
          </div>
          <div class="row p-2 mb-3 divider">
            <div class="col d-flex justify-content-center">
              <h3>{{__("dashboard.second_upgrade_details")}}</h3>
            </div>
          </div>
          <div class="row col d-flex justify-content-center"><span>{{__("dashboard.remaining_number")}}</span></div>
          <div class="row col d-flex justify-content-center"><h2>@{{ plan.remaining_deliveries }}</h2></div>
          <div class="row col d-flex justify-content-center"><hr style="width: 60px;"></div>
          <div class="row col d-flex justify-content-center"><h2>@{{ plan.delivery_count }}</h2></div>
          <div class="upgradecolor row col d-flex justify-content-center mb-3"><button class="btn btn-primary btn-sm"><a href="/plan">{{__('dashboard.upgrade')}}</a></button></div>
          <!-- <div class="row col d-flex justify-content-center p-2 mb-3"><span>{{__("dashboard.upgrade_and_get_more")}}</span></div> -->
          <div class="row col d-flex justify-content-center"><button type="button" class="btn btn-light border" @click="closeSecondModal">{{__("dashboard.never_mind")}}</button></div>
        </form>
      </a-modal>

      <!-- tutorial -->
      <tutorial></tutorial>

    </div>
  </div>
</div>

@endsection

@section('footer-scripts')
<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script>
  const messages = {
    i18n: {
      messages: {
        en: { 
          message: {
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
          }
        },
        ja: { 
          message: {
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
          }
        }
      }
    }
  }
  const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', // locale form config/app.php
    messages, // set locale messages
  })
  var dashboard = new Vue({
    i18n,
    el: '#dashboard',
    data() {
      return {
        loadingCount: 0,
        plan: {
            remaining_deliveries: 0,
            delivery_count: 0
        },
        summaryData: {
            totalRegisteredFollowers: 0
        },
        sentDeliveries: {},
        blockedUsers: {
            totalBlockedUsers: 0
        },
        activityCount: {},
        isAdmin:'',
        fail_modal_visible: false,
        first_modal: false,
        second_modal: false,
        tutorial: {{ var_export(Auth::user()->finished_tutorial) }}
      }
    },
    methods: {
      closeFailModal() {
          this.fail_modal_visible = false
      },
      closeFirstModal() {
          this.first_modal = false
      },
      closeSecondModal() {
          this.second_modal = false
      },
      showFailModal() {
        this.fail_modal_visible = true
      },
      showFirstModal() {
        this.first_modal = true
      },
      showSecondModal() {
        this.second_modal = true
      },
      calculatePercentage() {
        var result = this.percentage(this.plan.remaining_deliveries, this.plan.delivery_count)

        if(this.isAdmin){
            if (result > 0) {
                if (result > 10) {
                    if (result < 30) {
                    this.showFirstModal();
                    }
                } else {
                    this.showSecondModal();
                }
            } else {
                this.showFailModal();
            }
        }

      },
      percentage(partialValue, totalValue) {
        let n = ((100 * partialValue) / totalValue)
        // NaN判定
        if (n !== n) {
            return 0
        }
        return n.toFixed(2);
      },
      getData() {
        this.loadingCount++
        axios.get('/list').then((res) => {
          this.plan = res.data.plan
          this.summaryData = res.data.summaryData
          this.sentDeliveries = res.data.sentDeliveries
          this.isAdmin = res.data.isAdmin
          this.blockedUsers = res.data.blockedUsers
          this.activityCount = res.data.activityCount

          var chart1 = document.getElementById("summary");
          var chart2 = document.getElementById("clickAnalysis");
          var chart3 = document.getElementById("deliverable");
          var chart4 = document.getElementById("subscribers");
          var summary = null;
          var clickAnalysis = null;
          var deliverable = null;
          var subscribers = null;

          function summarySet(labels, data) {
            if (summary) {
              summary.destroy();
            }
            summary = new Chart(chart1, {
              type: 'line',
              data: {
                labels: labels,
                datasets: [{
                  label: '登録人数',
                  fill:false,
                  borderColor: "#20ccaa",
                  data: data[0]
                }, {
                  label: '有効登録人数',
                  fill:false,
                  borderColor: "#44aaFF",
                  data: data[1]
                }, {
                  label: 'アクティブ数',
                  fill:false,
                  borderColor: "#FF8888",
                  data: data[2]
                }, {
                  label: 'ブロック数/割合',
                  fill:false,
                  borderColor: "#000000",
                  data: data[3]
                }]
              },
              options: {
                legend: {display: false}
              }
            });
          }

          // 登録人数
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

          // ブロック数
          var blockDays = []
          var blockedFollowersPerDay = []
          for(let i = 0; i < this.blockedUsers.dayData.length; i++) {
            let date = new Date(this.blockedUsers.dayData[i].day.date)
            blockDays.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            blockedFollowersPerDay.push(this.blockedUsers.dayData[i].blockedUsers)
          }

          var blockWeeks = []
          var blockedFollowersPerWeek = [];
          for(let i = 0; i < this.blockedUsers.weekData.length; i++) {
            let date = new Date(this.blockedUsers.weekData[i].week.date)
            blockWeeks.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            blockedFollowersPerWeek.push(this.blockedUsers.weekData[i].blockedUsers)
          }

          var blockMonths = []
          var blockedFollowersPerMonth = [];
          for(let i = 0; i < this.blockedUsers.monthData.length; i++) {
            let date = new Date(this.blockedUsers.monthData[i].month.date)
            blockMonths.push(date.toISOString().slice(0,7).replace(/-/g, '/')) 
            blockedFollowersPerMonth.push(this.blockedUsers.monthData[i].blockedUsers)
          }

          var blockYears = []
          var blockedFollowersPerYear = [];
          for(let i = 0; i < this.blockedUsers.yearData.length; i++) {
            let date = new Date(this.blockedUsers.yearData[i].year.date)
            blockYears.push(date.toISOString().slice(0,4).replace(/-/g, '/')) 
            blockedFollowersPerYear.push(this.blockedUsers.yearData[i].blockedUsers)
          }

          // 有効登録ユーザー
          var availableUsersPerDay = []
          for (let i = 0; i < this.summaryData.dayData.length; i++) {
            let registeredFollowers = this.summaryData.dayData[i].registeredFollowers
            let blockedUsers = this.blockedUsers.dayData[i].blockedUsers
            let difference = (registeredFollowers - blockedUsers) > 0 ? registeredFollowers - blockedUsers : 0
            availableUsersPerDay.push(difference)
          }

          var availableUsersPerWeek = []
          for (let i = 0; i < this.summaryData.weekData.length; i++) {
            let registeredFollowers = this.summaryData.weekData[i].registeredFollowers
            let blockedUsers = this.blockedUsers.weekData[i].blockedUsers
            let difference = (registeredFollowers - blockedUsers) > 0 ? registeredFollowers - blockedUsers : 0
            availableUsersPerWeek.push(difference)
          }

          var availableUsersPerMonth = []
          for (let i = 0; i < this.summaryData.monthData.length; i++) {
            let registeredFollowers = this.summaryData.monthData[i].registeredFollowers
            let blockedUsers = this.blockedUsers.monthData[i].blockedUsers
            let difference = (registeredFollowers - blockedUsers) > 0 ? registeredFollowers - blockedUsers : 0
            availableUsersPerMonth.push(difference)
          }

          var availableUsersPerYear = []
          for (let i = 0; i < this.summaryData.yearData.length; i++) {
            let registeredFollowers = this.summaryData.yearData[i].registeredFollowers
            let blockedUsers = this.blockedUsers.yearData[i].blockedUsers
            let difference = (registeredFollowers - blockedUsers) > 0 ? registeredFollowers - blockedUsers : 0
            availableUsersPerYear.push(difference)
          }

          //　アクティブ数
          var activeDays = []
          var activesPerDay = []
          for(let i = 0; i < this.activityCount.dayData.length; i++) {
            let date = new Date(this.activityCount.dayData[i].day.date)
            activeDays.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            activesPerDay.push(this.activityCount.dayData[i].sourceUsers)
          }

          var activeWeeks = []
          var activesPerWeek = [];
          for(let i = 0; i < this.activityCount.weekData.length; i++) {
            let date = new Date(this.activityCount.weekData[i].week.date)
            activeWeeks.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            activesPerWeek.push(this.activityCount.weekData[i].sourceUsers)
          }

          var activeMonths = []
          var activesPerMonth = [];
          for(let i = 0; i < this.activityCount.monthData.length; i++) {
            let date = new Date(this.activityCount.monthData[i].month.date)
            activeMonths.push(date.toISOString().slice(0,7).replace(/-/g, '/')) 
            activesPerMonth.push(this.activityCount.monthData[i].sourceUsers)
          }

          var activeYears = []
          var activesPerYear = [];
          for(let i = 0; i < this.activityCount.yearData.length; i++) {
            let date = new Date(this.activityCount.yearData[i].year.date)
            activeYears.push(date.toISOString().slice(0,4).replace(/-/g, '/')) 
            activesPerYear.push(this.activityCount.yearData[i].sourceUsers)
          }

          summarySet( days, [registeredFollowersPerDay, availableUsersPerDay, activesPerDay, blockedFollowersPerDay]);

          var chart2gradient1 = chart2.getContext("2d").createLinearGradient(0, 0, 0, $('#clickAnalysis').width());
          chart2gradient1.addColorStop(0, "#1240f0ff");
          chart2gradient1.addColorStop(1, "#6fc8ffaa");
          var chart2gradient2 = chart2.getContext("2d").createLinearGradient(0, 0, 0, $('#clickAnalysis').width());
          chart2gradient2.addColorStop(0, "#b0ffd3ff");
          chart2gradient2.addColorStop(1, "#35ffa0aa");
          var chart2gradient3 = chart2.getContext("2d").createLinearGradient(0, 0, 0, $('#clickAnalysis').width());
          chart2gradient3.addColorStop(0, "#2dc2ff55");
          chart2gradient3.addColorStop(1, "#2dc2ff00");
          var chart2gradient3b = chart2.getContext("2d").createLinearGradient(0, 0, 0, $('#clickAnalysis').width());
          chart2gradient3b.addColorStop(0, "#2dc2ff77");
          chart2gradient3b.addColorStop(1, "#2dc2ff00");

          function clickAnalysisSet(labels, data) {
            if (clickAnalysis) {
              clickAnalysis.destroy();
            }
            clickAnalysis = new Chart(chart2, {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  label: 'ユニーク数',
                  data: data[0],
                  backgroundColor: chart2gradient1,
                  borderWidth: 0
                }, {
                  label: 'クリック数',
                  data: data[1],
                  backgroundColor: chart2gradient2,
                  borderWidth: 0
                }, {
                  type: 'line',
                  label: '配信数',
                  data: data[2],
                  borderColor: "#FFFFFF00",
                  backgroundColor: chart2gradient3b
                }]
              },
              options: {
                legend: {display: false},
                scales: {
                  xAxes: [{
                    stacked: true,
                    categoryPercentage: 0.23
                  }],
                  yAxes: [{
                    ticks: {
                      beginAtZero: false
                    }
                  }]
                }
              }
            });
          }

          var deliveriesDays = []
          var sentDeliveriesPerDay = []
          for(let i = 0; i < this.sentDeliveries.dayData.length; i++) {
            let date = new Date(this.sentDeliveries.dayData[i].day.date)
            deliveriesDays.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            sentDeliveriesPerDay.push(this.sentDeliveries.dayData[i].sentDeliveries)
          }

          var deliveriesWeeks = []
          var sentDeliveriesPerWeek = [];
          for(let i = 0; i < this.sentDeliveries.weekData.length; i++) {
            let date = new Date(this.sentDeliveries.weekData[i].week.date)
            deliveriesWeeks.push(date.toISOString().slice(0,10).replace(/-/g, '/')) 
            sentDeliveriesPerWeek.push(this.sentDeliveries.weekData[i].sentDeliveries)
          }

          var deliveriesMonths = []
          var sentDeliveriesPerMonth = [];
          for(let i = 0; i < this.sentDeliveries.monthData.length; i++) {
            let date = new Date(this.sentDeliveries.monthData[i].month.date)
            deliveriesMonths.push(date.toISOString().slice(0,7).replace(/-/g, '/')) 
            sentDeliveriesPerMonth.push(this.sentDeliveries.monthData[i].sentDeliveries)
          }

          var deliveriesYears = []
          var sentDeliveriesPerYear = [];
          for(let i = 0; i < this.sentDeliveries.yearData.length; i++) {
            let date = new Date(this.sentDeliveries.yearData[i].year.date)
            deliveriesYears.push(date.toISOString().slice(0,4).replace(/-/g, '/')) 
            sentDeliveriesPerYear.push(this.sentDeliveries.yearData[i].sentDeliveries)
          }

          clickAnalysisSet( days, [[1, 2, 1, 3, 4, 1, 2], [3, 4, 4, 7, 1, 6, 5], sentDeliveriesPerDay]);

          var chart3gradient= chart3.getContext("2d").createLinearGradient(0, 0, $('#deliverable').width(), 0);
          chart3gradient.addColorStop(0, "#20aaf0aa");
          chart3gradient.addColorStop(0.7, "#f09090aa");

          deliverable = new Chart(chart3, {
            type: 'doughnut',
            data: {
              labels: ["配信数", "残通数"],
              datasets: [{
                data: [this.plan.remaining_deliveries, this.plan.delivery_count - this.plan.remaining_deliveries],
                backgroundColor: [
                  chart3gradient,
                  'rgba(99,99,99,0.5)'
                ],
                borderColor: [
                  chart3gradient,
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

          var chart4gradient= chart4.getContext("2d").createLinearGradient(0, 0, 0, $('#subscribers').width());
          chart4gradient.addColorStop(0, "#4bdeb9ff");
          chart4gradient.addColorStop(1, "#30a7ceff");

          function subscribersSet(labels, data) {
            if (subscribers) {
              subscribers.destroy();
            }
            subscribers = new Chart(chart4, {
              type: 'bar',
              data: {
                xLabels: labels,
                yLabels: ['0%', '10%', '20%', '30%'],
                datasets: [{
                  label: '登録者数',
                  data: data,
                  backgroundColor: chart4gradient,
                  borderColor: chart4gradient,
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
          subscribersSet(["2019/7/10", "7/11", "7/12", "7/13", "7/14", "7/15", "7/16"], [10, 12, 27, 15, 9.5, 13, 4]);
          
          $('.summary-navs a.nav-link').click(function(){
            $('.summary-navs a.nav-link').removeClass('active');
            $(this).addClass('active');

            var category = $(this).data('category');
            if (category == 'day') {
              summarySet( days, [registeredFollowersPerDay, availableUsersPerDay, activesPerDay, blockedFollowersPerDay]);
            } else if (category == 'weak') {
              summarySet( weeks, [registeredFollowersPerWeek, availableUsersPerWeek, activesPerWeek, blockedFollowersPerWeek]);
            } else if (category == 'month') {
              summarySet( months, [registeredFollowersPerMonth, availableUsersPerMonth, activesPerMonth, blockedFollowersPerMonth]);
            } else if (category == 'year') {
              summarySet( years, [registeredFollowersPerYear, availableUsersPerYear, activesPerYear, blockedFollowersPerYear]);
            }
            return false;
          });
          $('.click-analysis-navs a.nav-link').click(function(){
            $('.click-analysis-navs a.nav-link').removeClass('active');
            $(this).addClass('active');
            var category = $(this).data('category');
            if (category == 'day') {
              clickAnalysisSet( deliveriesDays, [[1, 2, 1, 3, 4, 1, 2], [3, 4, 4, 7, 1, 6, 5], sentDeliveriesPerDay]);
            } else if (category == 'weak') {
              clickAnalysisSet( deliveriesWeeks, [[12,17,12,11,18,19,12], [3,14,17,14,15,10,17], sentDeliveriesPerWeek]);
            } else if (category == 'month') {
              clickAnalysisSet( deliveriesMonths, [[15,10,14,17,10,15,2], [17,10,14,08,13,14,23], sentDeliveriesPerMonth]);
            } else if (category == 'year') {
              clickAnalysisSet( deliveriesYears, [[26,20,24,20,21,21,3], [25,27,56,21,25,21,25], sentDeliveriesPerYear]);
            }
            return false;
          });
          $('.subscribers-navs a.nav-link').click(function(){
            $('.subscribers-navs a.nav-link').removeClass('active');
            $(this).addClass('active');
            var category = $(this).data('category');
            if (category == 'day') {
              subscribersSet(["2019/7/10", "7/11", "7/12", "7/13", "7/14", "7/15", "7/16"], [10, 12, 27, 15, 9.5, 13, 4]);
            } else if (category == 'weak') {
              subscribersSet(["2019/6/2", "6/9", "6/16", "6/23", "6/30", "7/7", "7/14"], [26,14,20,21,27,15,22]);
            } else if (category == 'month') {
              subscribersSet(["2019/2", "2019/3", "2019/4", "2019/5", "2019/6", "2019/7"], [25,22,30,20,13,12]);
            } else if (category == 'year') {
              subscribersSet(["2015", "2016", "2017", "2018", "2019"], [21,28,30,25,27]);
            }
            return false;
          });

          this.calculatePercentage();
        }).catch(e => {
            this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
          console.error(e);
        })
        .finally(() => this.loadingCount--)
      }
    },
    beforeMount() {
      this.getData()
    }
  })
</script>
<script type='text/javascript' src="{{ asset('js/chart.bundle.js') }}"></script>
<script type='text/javascript' src="{{ asset('js/chart.js') }}"></script>

@endsection

@section('footer-styles')
<style>
  div.flex-item {
    margin-bottom: 15px;
    padding-right: 0;
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
  .nav-link {
    display: block;
    padding: 0 .5rem .1rem .5rem;
    margin: 0 .1rem .5rem .1rem;
  }
  .nav-link.active {
    border-bottom: 2px blue solid;
    color: blue;
  }

  
  #summary {
    /* max-height: 250px; */
    margin: auto;
  }
  #clickAnalysis {
    /* max-height: 230px; */
    margin: auto;
  }
  #deliverable {
    max-width: 230px;
    max-height: 230px;
    margin: auto;
  }
  #subscribers {
    /* max-height: 230px; */
    margin: auto;
  }

  .upgradecolor {
    display: inline-block;            
  }

   .upgradecolor .btn-primary {
        display: inline-block;
        text-align: center;
        padding: 10px;
        background: linear-gradient(60deg, #ff60dd 0%, #aa40ff 100%);
        box-shadow: 1px 1px 4px rgba(0,0,0,.3);
        transition: .4s;
        border: 0;
        font-size: 0.7rem;
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