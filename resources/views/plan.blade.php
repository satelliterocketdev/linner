@extends('auth.main')

@section('content')
<div id="plan" class="container h-100" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <div class="row justify-content-center align-items-center h-100">
    <br /><br />
    <div class="container-fluid">
      
      <div class="row align-items-center justify-content-center p-2">
        <h1 class="logo text-white text-center">@{{ $t('message.lets_start') }}</h1>
      </div>
      <div class="row align-items-center justify-content-center p-2">
        <h2 class="logo text-white">@{{ $t('message.select_plan') }}</h2>
      </div>

      <div class="row align-items-center justify-content-center p-2">
        <div v-for="plan in plans" class="card-deck" style="margin:0 5px;">
          <!-- v-for start -->
          <div v-if="plan.type != 'trial'" class="card text-center border-0">
            <div v-bind:class="[plan.type,defaltClass]">
                <h1 class="card-title text-white">@{{plan.name}}</h1>
                <p>@{{plan.description1}}</p>
                <a :href="'/plan/detail?select=' + plan.type" class="btn btn-light">@{{ $t('message.details') }}</a>
            </div>
          </div>
          <!-- v-for end -->
        </div>
      </div>
      <form v-if="plans.length !== 0 && account_count <= plans[0].plan_count" class="form-horizontal" method="POST" :action="form_action">
        {{ csrf_field() }}
        <input type="hidden" name="plan_id" value="1">
        <div class="trial_button_wrap">
            <button type="submit" class="btn trial_button" @click="confirmAlert(1,$event)">@{{ $t('message.free_plan') }}</button>
        </div>
     </form>

      </div>
    </div>
  </div>
</div>
@endsection

@section('footer-scripts')
<!-- modal -->

<script>
  const messages = {
        en: {
            message: {
                lets_start:'Let\'s start!',
                select_this_plan: 'Select this plan',
                select_plan: 'Select Plan',
                go_to_payment_method: 'Go to payment method',
                start_catch_phrase: 'Monthly free',
                details: 'Details',
                plan:'Plan',
                yen_per_month:'yen / month',
                free_plan: 'Free Plan',
                getting_data: "Getting Data",
                fetch_fail: "Fail to fetch data"
            }
        },
        ja: {
            message: {
              lets_start:'さあ、はじめましょう！',
              select_this_plan: 'このプランを選択する',
              select_plan: 'プランを選ぶ',
              go_to_payment_method: '決済ページへ',
              start_catch_phrase: '月額無料',
              details: '詳細を見る',
              plan:'プラン',
              yen_per_month:'円 / 月',
              free_plan: '無料のフリープランはこちらから。',
              getting_data: "データの取得",
              fetch_fail: "データの取得に失敗しました。"
            }
        }
    }
  Vue.config.devtools = true;
  const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', // locale form config/app.php 
      messages, // set locale messages
  })

  var plan = new Vue({
    i18n,
    el: '#plan',
    data() {
      return {
        loadingCount: 0,
        currentIndex: 0,
        plans: [],
        current_plan:'',
        account_count:'',
        form_action: '',
        defaltClass:'card-body rounded d-flex flex-column justify-content-between'
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
        axios.get('plan/list').then((res) => {
            console.log(this.$t())
          this.plans = res.data;
          console.log(this.plans);
        }).catch(e => {
          this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'));
          console.error(e);
        })
        .finally(() => this.loadingCount--)
      },
      geyMyData() {
        this.loadingCount++
        axios.get('plan/my_data').then((res) => {
          this.current_plan  = res.data.plan_id;
          if(this.current_plan == null){
            this.form_action = location.protocol + '//' + location.host +'/plan/register';
          }else{
            this.form_action = location.protocol + '//' + location.host +'/plan/update';
          }
          this.account_count = res.data.account_count;
        }).catch(e => {
          this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
          console.error(e);
        })
        .finally(() => this.loadingCount--)
      },
      confirmAlert(plan_id,event){
        if(this.current_plan != null){
            if(plan_id != this.current_plan ){
                res = confirm("フリープランに変更しますか？");
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
      this.geyMyData();
    }
  })
</script>
@endsection

@section('footer-styles')
<style>
  .personal {
    background-color: #F63447; /* For browsers that do not support gradients */
    background-image: linear-gradient(to bottom right, #0081FF, #00BBEB, #01FDCB);
  }
  .corporation {
    background-color: #20ccaa; /* For browsers that do not support gradients */
    background-image: linear-gradient(to bottom right, #FF6A84, #7653CA, #0134FF);
  }
  .trial_button_wrap{
      text-align: center;
  }
  .trial_button {
      color: #0134FF;
      margin: 0 auto ;
      
  }
  .trial_button:hover {
      color: #0134FF;
  }
  .gradientbutton {
    background-color: #4ED8DA; /* For browsers that do not support gradients */
    background-image: linear-gradient(to right, #4ED8DA, #3CBEE3, #168AF5);
  }
  .card-body h1{
    margin: 25px 0 0;
  }
  .recommended{
    position: absolute;
    left: 0;
    right: 0;
    margin: auto;
    font-weight: bold;
    color: yellow;
  }
  .free_plan{
    text-align: center;
    margin: 10px 0 0;
    color: #0134FF;
  }
  .trial_button_wrap{
      text-align: center;
  }
  .trial_button {
      color: #0134FF;
      margin: 0 auto ;
      
  }
  .trial_button:hover {
      color: #0134FF;
  }

  body {
    background-color: black;
    color: white;
  }

  .card {
    width: 15rem;
    height: 20rem;
  }

  @media (max-width: 767px) {
    .card {
        width: 100%;
        height: auto;
    }
  }
</style>
@endsection