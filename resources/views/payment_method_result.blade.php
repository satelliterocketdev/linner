@extends('auth.main')

@section('content')
    <div id="paymentmethod">
        <div class="row">
            <div class="col text-center mt-5">
               @if($request_type == 'register')
                    <p>@{{ $t('message.register_complete') }}</p>
                    <p><a class="btn btn-primary" href="{{route('newaccount')}}">@{{ $t('message.line_account') }}</a> @{{ $t('message.apply') }}<p>
               @elseif($request_type == 'update')
                    <p>@{{ $t('message.update_complete') }}</a>
                    <p><a href="{{url('')}}">@{{ $t('message.go_dashboard') }}</a></a>
                @elseif($request_type == 'redo')
                <p>@{{ $t('message.redo_complete') }}</a>
                <p><a href="{{url('')}}">@{{ $t('message.go_dashboard') }}</a></a>
               @endif
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
  const messages = {
        en: {
            message: {
                register_complete:'Settlement is complete.',
                update_complete  :'Settlement for plan change is complete.',
                line_account     : 'Line@ account application',
                apply            :'apply.',
                go_dashboard     :'Go to Dashboard.'
            }
        },
        ja: {
            message: {
                register_complete:'決済が完了しました。',
                update_complete  :'プラン変更のための決済が完了しました。',
                redo_complete    :'再決済が完了しました。',
                line_account     : 'Line@アカウント申請',
                apply            :'を行ってください。',
                go_dashboard     :'ダッシュボードへ戻る。'
            }
        }
    }
    const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', // locale form config/app.php 
      messages, // set locale messages
    })
    var paymentmethod = new Vue({
        i18n,
        el: '#paymentmethod',
        data() {
            return {
                currentIndex: 0,
                plans: [],
            }
        },
    })
</script>
@endsection