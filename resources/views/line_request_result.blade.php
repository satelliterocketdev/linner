@extends('layouts.app')

@section('content')
<div id="newaccountcreated" class="bg-white rounded py-3">
   <div class="container">
    <div class="row">
      <div class="col">
        <div class="panel panel-default">
          <div class="panel-heading"><h3>@{{ $t('message.line_account_created') }}</h3></div>
          <div class="panel-body">
            <p>@{{ $t('message.line_account_finished') }}</p>
            <p>@{{ $t('message.hint1') }}</p>
          </div>
          <div class="col text-center">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">@{{ $t('message.use_linner') }}</a>
          </div>
        </div>
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
        line_account_created: 'LINE account registration completed',
        line_account_finished: 'Your LINE account registration is complete.',
        hint1: 'You can start using LINNER from the following.',
        use_linner: 'Use LINNER'
      }
    },
    ja: {
      message: {
        line_account_created: 'LINEアカウント登録完了',
        line_account_finished: 'LINEアカウント登録が完了いたしました。',
        hint1: '以下からLINNERのご利用を開始できます。​',
        use_linner: 'はじめる'
      }
    }
  }

  // Create VueI18n instance with options
  const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', // locale form config/app.php 
      messages, // set locale messages
  })

  var newaccountcreated = new Vue({
    i18n,
    el: '#newaccountcreated'
  })
</script>
@endsection


@section('footer-styles')
<style>
  a.btn{
    color: #fff !important;
  }
</style>
@endsection