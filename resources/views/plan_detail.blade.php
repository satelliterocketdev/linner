
@extends('auth.main')

@section('content')
<div id="plan_detail" class="container h-100">
  <loading :visible="loadingCount > 0"></loading>
  <div class="row justify-content-center align-items-center h-100">
    <div class="container-fluid">
      <div class="tab_box">
        <ul class="nav nav-tabs">
          <li v-for="(plan,i) in plans"  v-if="plan.type != 'trial'"  class="nav-item">
            <a :href="'#'+plan.type" class="nav-link" :class="[ plan.type == params.select ? 'active':'' , plan.type ]" data-toggle="tab">@{{ plan.name }}</a>
          </li>
        </ul>
        <div class="tab-content">
            <div v-for="plan in plans" :id="plan.type" class="tab-pane" :class="[ plan.type == params.select ? 'active':'' ,plan.type ]">
                <div v-if="plan.type != 'trial'">
                    <h2>@{{ plan.name }}</h2>
                    <p>@{{ plan.description2 }}</p>
                    <ul class="detail_plan_card">
                    <li v-for="detail_plan in detail_plans" v-if="plan.type == detail_plan.type && account_count <= detail_plan.account_count" class="detail_card">
                        <h3>@{{ detail_plan.level.toUpperCase() }}</h3>
                        <ul>
                            <li class="per_month">@{{ $t('message.per_month') }}</li>
                            <li v-if="detail_plan.price != null">@{{ detail_plan.price|number_format }}@{{ $t('message.yen_per_month') }}</li>
                            <li v-else=>@{{ $t('message.ask') }}</li>
                        </ul>
                        <ul>
                            <li>@{{ $t('message.delivery_limit') }}</li>
                            <li v-if="detail_plan.delivery_count != null">@{{ detail_plan.delivery_count|number_format }}@{{ $t('message.delivery_unit') }}<span v-if="detail_plan.type == 'corporation'" >〜</span></li>
                            <li v-else=>@{{ $t('message.delivery_no_limit') }}</li>
                        </ul>
                        <ul>
                            <li>@{{ $t('message.all_functions_open') }}</li>
                            <li>@{{ $t('message.number_of_accounts') }} @{{ detail_plan.account_count|number_format }}<span v-if="detail_plan.account_count != 1" >〜</span></li>
                        </ul>
                        <form v-if="detail_plan.price != null" class="form-horizontal" method="POST" :action="form_action">
                            {{ csrf_field() }}
                            <input type="hidden" name="plan_id" :value="detail_plan.id">
                            <button type="submit" class="btn rounded-red m-1" @click="conrifmAlert(detail_plan.id,$event)">@{{ $t('message.select_this_plan') }}</button>
                        </form>
                        <a v-else target="_blank" href="#" class="btn rounded-red m-1">@{{ $t('message.select_this_plan') }}</a>
                        <!-- TODO::フォームに飛ばす お問い合わせ or Googleフォームの想定  -->
                    </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="revers_btn"><a href="../plan">戻る</a></div>
      </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')

<script>
  const messages = {
    en: {
      message: {
        lets_start:'Let\'s start!',
        select_this_plan: 'Select this plan',
        select_plan: 'Select Plan',
        go_to_payment_method: 'Go to payment method',
        per_month:'Per month',
        ask:'ASK',
        start_catch_phrase: 'Monthly free',
        business_catch_phrase: 'Work use',
        expert_catch_phrase: 'Corporation use',
        details: 'Details',
        plan:'Plan',
        yen_per_month:'yen / month',
        delivery_limit:'Delivery limit',
        delivery_unit:'Delivery',
        delivery_no_limit:'Delivery No Limit',
        all_functions_open:'All functions open',
        number_of_accounts:'Number of accounts',
        getting_data: "Getting Data",
        fetch_fail: "Fail to fetch data"
      }
    },
    ja: {
      message: {
        lets_start:'さあ、はじめましょう！',
        select_this_plan: 'このプランを選ぶ',
        select_plan: 'プランを選ぶ',
        go_to_payment_method: '決済ページへ',
        per_month:'月額',
        ask:'ASK',
        start_catch_phrase: '月額無料',
        business_catch_phrase: '仕事でのご利用向け',
        expert_catch_phrase: '法人での利用向け',
        details: '詳細を見る',
        plan:'プラン',
        yen_per_month:'円 / 月',
        delivery_limit:'配信上限',
        delivery_unit:'通',
        delivery_no_limit:'通数無制限',
        all_functions_open:'全機能開放',
        number_of_accounts:'アカウント数',
        getting_data: "データの取得",
        fetch_fail: "データの取得に失敗しました。"
      }
    }
  }
  Vue.config.devtools = true;
  const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', // locale form config/app.php 
      messages, // set locale messages
  });

  Vue.filter('number_format', function (value) {
    if (! value) { return false; }
    return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
  });

  var plan = new Vue({
    i18n,
    el: '#plan_detail',
    data() {
      return {
        loadingCount: 0,
        currentIndex: 0,
        plans: [],
        detail_plans: [],
        current_plan:'',
        account_count:'',
        form_action: '',
        params: [],
        referer:'',
        defaltClass:'card-body rounded d-flex flex-column justify-content-between',
      }
    },
    methods: {
      openNotificationWithIcon(type, message, desc) {
        this.$notification[type]({
          message: message,
          description: desc,
        });
      },
      getData() {
        this.loadingCount++
        axios.get('./list').then((res) => {
          this.plans = res.data;
        }).catch(e => {
            this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
            console.error(e);
        })
        .finally(() => this.loadingCount--)
      },
      getDetailData() {
        this.loadingCount++
        axios.get('./detail_list').then((res) => {
          this.detail_plans = res.data;
        }).catch(e => {
            this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
            console.error(e);
        })
        .finally(() => this.loadingCount--)
      },
      geyMyData() {
        this.loadingCount++
        axios.get('./my_data').then((res) => {
          this.current_plan  = res.data.plan_id;
          if(this.current_plan == null){
            this.form_action = location.protocol + '//' + location.host +'/plan/register';
          }else{
            this.form_action = location.protocol + '//' + location.host +'/plan/update';
          }
          this.account_count = res.data.account_count;
        }).catch(e => {
          this.openNotificationWithIcon("error","Getting Plans","Plans fail to fetch");
          console.error(e);
        })
        .finally(() => this.loadingCount--)
      },
      getParam(name, url){ /* urlパラメーター取得 */
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        this.params[name] = decodeURIComponent(results[2].replace(/\+/g, " "));
      },
      conrifmAlert(plan_id,event){
        if(this.current_plan != null){
            if(plan_id != this.current_plan ){
                res = confirm("プランを変更しますか？");
            }else{
                res = confirm("現在のプランと同じでよろしいですか？");
            }
            if(!res){
                event.preventDefault()
            }
        }
      }
    },
    beforeMount() {
      this.getData();
      this.getDetailData();
      this.geyMyData();
      this.getParam('select');
    }
  })
</script>
@endsection

@section('footer-styles')
<style>
  .trial {
    background-color: #00C4FF; /* For browsers that do not support gradients */
    background-image: linear-gradient(to bottom right, #00C4FF, #00A4FF, #005EFF);
  }
  .personal {
    background-color: #F63447; /* For browsers that do not support gradients */
    background-image: linear-gradient(to bottom right, #0081FF, #00BBEB, #01FDCB);
  }
  .corporation {
    background-color: #20ccaa; /* For browsers that do not support gradients */
    background-image: linear-gradient(to bottom right, #FF6A84, #7653CA, #0134FF);
  }
  .gradientbutton {
    background-color: #4ED8DA; /* For browsers that do not support gradients */
    background-image: linear-gradient(to right, #4ED8DA, #3CBEE3, #168AF5);
  }
  .tab_box{
    width: 700px;
    margin: 0 auto;
  }
  .nav-tabs,.nav-link{
    border: none !important;
  }
  .nav-item{
    text-align: center;
  }
  .nav-item a{
    font-size: 1.3rem;
  }
  .nav-item{
    width:50%;
  }
  a.active{
    color: #fff !important;
  }
  .tab-pane{
    padding: 15px;
    text-align: center;
  }
  .tab-pane h2{
    color:#fff;
  }
  .detail_plan_card{
    display: flex;
    flex-wrap: wrap;
    justify-content:space-around;
  }
  .detail_plan_card .detail_card{
    color: #000;
    list-style: none;
    background: #fff;
    width: 30%;
    padding: 15px;
  }
  .detail_plan_card .detail_card ul{
    margin: 0 0 15px;
  }
  .detail_plan_card .detail_card li{
    list-style-type: none;
  }
  .revers_btn{
    text-align: center;
    margin: 10px 0;
  }
  .per_month{
    font-weight: bold;
    font-size: 0.7rem;
  }
  .rounded-red{
    color: #fff !important;
  }
  body {
    background-color: black;
    color: white;
  }

  @media (max-width: 767px) {
    .tab_box {
        width: 100%;
    }

    .detail_plan_card .detail_card h3 {
        font-size: 1.6rem;
        word-break: break-all;
    }
  }

  @media (max-width: 576px) {
    .detail_plan_card .detail_card {
        width: 100%;
        margin-bottom: 10px;
    }
  }
</style>
@endsection