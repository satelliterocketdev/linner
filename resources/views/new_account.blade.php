@extends('auth.main')

@section('content')
<div id="line_request" class="container h-100">
  <loading :visible="loadingCount > 0"></loading>
  <div class="row justify-content-center align-items-center h-100">
    @if (session('error'))
      <div class="container mt-2">
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      </div>
    @endif
    <div class="container-fluid">
      <div class="row align-items-center justify-content-center p-2">
        <div class="card text-center" style="width: 50rem;">
          <div class="card-body">
          <h2>@{{ $t('message.input_line_information') }}</h2>
            <p class="card-text">
              <button type="button" @click.prevent="change_step(1)" class="btn btn-light rounded-circle p-0 border" style="width:2rem;height:2rem;">1</button>
              <button type="button" @click.prevent="change_step(2)" class="btn btn-light rounded-circle p-0 border" style="width:2rem;height:2rem;">2</button>
            </p>
            
            <!-- カルーセル start -->
            <!-- https://qiita.com/HikaMashiro/items/667aa5eee521f6bbd8ef -->
            <carousel v-bind:per-page=1 class="carousel">
              <template v-for="n of 10" :key="n">
                <slide><img :src="'img/line_accounts/flow_'+ n +'.jpg'" /></slide>
              </template>
            </carousel>
            <!-- カルーセル end -->

            <p v-show="step === 1" class="card-text">@{{ $t('message.hint2') }}</p>
            <p v-show="step === 2" class="card-text">@{{ $t('message.hint1') }}</p>
            <form class="form-horizontal" data-toggle="validator" method="POST" action="{{ route('newaccount') }}" novalidate>
                {{ csrf_field() }}
              <div v-show="step === 1">
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Webhook URL</label>
                  <div class="col-sm-8">
                    <input name="webhook_url" type="text" :value="webhookurl" maxlength="33" class="form-control" readonly>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <small class="form-text text-muted">@{{ $t('message.hint3') }}</small>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <label class="col-form-label">@{{ $t('message.hint4') }}</label>
                  </div>
                </div>
                <div class="row align-items-center justify-content-center p-2">
                  <div class="form-group">
                    <button @click.prevent="change_step(2)" id="gradientbutton" class="btn btn-round btn-sm btn-block text-white">
                      @{{ $t('message.next') }}
                    </button>
                  </div>
                </div>
              </div>
              <div v-show="step === 2">
                {!! csrf_field() !!}
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">@{{ $t('message.name') }}</label>
                  <div class="col-sm-8">
                    <input name="name" type="text" maxlength="33" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Basic ID</label>
                  <div class="col-sm-8">
                    <input name="basic_id" type="text" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Channel ID</label>
                  <div class="col-sm-8">
                    <input name="channel_id" type="text" maxlength="11" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">User ID</label>
                  <div class="col-sm-8">
                    <input name="user_id" type="text" maxlength="33" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">@{{ $t('message.access_token') }}</label>
                  <div class="col-sm-8">
                    <input name="access_token" type="text" maxlength="172" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Channel Secret</label>
                  <div class="col-sm-8">
                    <input name="channel_secret" type="text" maxlength="32" class="form-control">
                  </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">@{{ $t('message.follow_link') }}</label>
                    <div class="col-sm-8">
                      <input name="follow_link" type="text" class="form-control">
                    </div>
                  </div>
                <div class="row align-items-center justify-content-center p-2">
                  <button type="submit" class="btn btn-info m-1">@{{ $t('message.next') }}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="row align-items-center justify-content-center p-2">
        <button type="button" class="btn btn-link text-white">@{{ $t('message.previous') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer-scripts')
<script src="https://cdn.rawgit.com/SSENSE/vue-carousel/6823411d/dist/vue-carousel.min.js"></script>
<script>
  const messages = {
    en: {
      message: {
        input_line_information: 'Enter your LINE account information',
        hint1: 'Establish LINE official account, messaging from settings',
        hint2: 'Please paste in the messassing API image of the LINE official account.',
        access_token: 'Access Token',
        hint3: 'For LINE official account response settings, set “Response mode” to Bot, “Webhook” to on, and “greeting message” to off.',
        hint4: 'Please change the monthly plan of the LINE official account according to your plan.',
        next: 'Next',
        previous: 'Back',
        name: 'Account Name',
        follow_link: 'Line Follow Link'
      }
    },
    ja: {
      message: {
        input_line_information: 'LINEアカウント情報を入力してください',
        hint1: 'LINE公式アカウントを開設し、設定からmessaging APIを設定し、LINE Developers内で確認してください。',
        hint2: 'LINE公式アカウントのmessassing API画像に貼り付けてください。',
        access_token: 'アクセストークン',
        hint3: 'LINE公式アカウント応答設定は、「応答モード」をBotに、「Webhook」をオンに、「挨拶メッセージ」をオフにしてください。',
        hint4: 'ご利用のプランに応じて、LINE公式アカウントの月額プラン変更をお願いします。',
        next: '次へ',
        previous: '戻る',
        name: 'アカウント名',
        follow_link: 'Lineフォローリンク'
      }
    }
  }

  // Create VueI18n instance with options
  const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', // locale form config/app.php 
      messages, // set locale messages
  })

  var line_request = new Vue({
    i18n,
    el: '#line_request',
    data() {
      return {
        loadingCount: 0,
        step:1,
        webhookurl: {!! json_encode($webhookurl) !!}
      }
    },
    methods: {
      change_step(step) {
        this.step = step;
      }
    },
    components: {
        'carousel': VueCarousel.Carousel,
        'slide': VueCarousel.Slide
    },
  })
</script>
@endsection

@section('footer-styles')
<style>
    /* カルーセル用 */
    .VueCarousel-slide {
        color:#FFF;
        background:#59ecff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right:1px solid #FFF;
        box-sizing:border-box;
        font-size:12px;
    }
    .carousel img{
        width: 100%;
    }
    </style>
@endsection