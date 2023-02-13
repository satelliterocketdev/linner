@extends('auth.main')
@section('content')
    <div id="error">
        <div class="bg-white border rounded p-3">
            <p>
                403 Forbidden
                <br>
                @{{ $t('message.error_message') }}
            </p>
            <p>
                <a class="btn mx-1 mb-1 btn-info small-text font-size-table" href="{{ route('login') }}">ログイン画面に戻る</a>
            </p>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        const messages = {
            en: {
                message: {
                    error_message:'You are not allowed access.',
                }
            },
            ja: {
                message: {
                    error_message:'アクセスは許可されていません。',
                }
            }
        }
        Vue.config.devtools = true;
        const i18n = new VueI18n({
            locale: '{{config('app.locale')}}', // locale form config/app.php 
            messages, // set locale messages
        })
        var error = new Vue({
            i18n,
            el: '#error',
            data() {
                return {}
            },
            methods: {},
            beforeMount() {}
        })
    </script>
@endsection
@section('footer-styles')
<style></style>
@endsection