@extends('auth.main')

@section('content')
    <div id="paymentmethod">
        <div class="row">
            <div class="col text-center mt-5">「決済する」をクリックし、お支払いを行なってください。</div>
        </div>
        <div class="row">
            <div class="col text-center text-danger">※外部サイトへ遷移します。</div>
        </div>

        <form method="POST" action="{{ $form_action }}">
            <input type="hidden" name="trading_id" value="{{ $trading_id }}" />
            <input type="hidden" name="payment_type" value="{{ $payment_type }}" />
            <input type="hidden" name="fix_params" value="{{$fix_params}}" />
            <input type="hidden" name="id" value="{{$id}}" />
            <input type="hidden" name="hc" value="{{ $hc }}" /> <!-- 改ざん防止用のハッシュ値 -->
            <input type="hidden" name="seq_merchant_id" value="{{ $seq_merchant_id }}" />
            <input type="hidden" name="finish_disable" value="{{ $finish_disable }}" />
            {{-- NOTE: merchant_name を連携すると、パラメータエラーになってしまう --}}
            {{-- <input type="hidden" name="merchant_name" value="{{ $merchant_name }}" />--}}
            {{-- <input type="hidden" name="payment_detail" value="決済内容" />--}}
            {{-- <input type="hidden" name="payment_detail_kana" value="ｹｯｻｲﾅｲﾖｳｶﾅ" />--}}
            {{-- <input type="hidden" name="banner_url" value="http://123.234.123.200/common/header_logo.gif" />--}}
            {{-- <input type="hidden" name="free_memo" value="自由メモ" />--}}
            <input type="hidden" name="return_url" value="{{ url('/paymentmethod/result/'.$id.'/'.$trading_id) }}" />
            <input type="hidden" name="payment_class" value="{{$payment_class}}">
            <input type="hidden" name="use_card_conf_number" value="{{$use_card_conf_number}}">
            <input type="hidden" name="stock_card_mode" value="{{$stock_card_mode}}">
            <input type="hidden" name="customer_id" value="{{$customer_id}}">
            <input type="hidden" name="threedsecure_ryaku" value="{{$threedsecure_ryaku}}">
            <input type="hidden" name="sales_flg" value="{{$sales_flg}}">
            <input type="hidden" name="appendix" value="{{$appendix}}">
            <input type="hidden" name="payment_term_day" value="{{$payment_term_day}}">
            <input type="hidden" name="payment_term_min" value="{{$payment_term_min}}">
            {{-- <input type="hidden" name="customer_family_name" value="ペイジェント" />--}}
            {{-- <input type="hidden" name="customer_name" value="太郎" />--}}
            {{-- <input type="hidden" name="customer_family_name_kana" value="ﾍﾟｲｼﾞｪﾝﾄ" />--}}
            {{-- <input type="hidden" name="customer_name_kana" value="ﾀﾛｳ" />--}}
            <div class="row">
                <div class="col text-center mt-3">
                    <input type="submit" class="btn btn-success" value="決済する" />
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer-scripts')
<script>
    var paymentmethod = new Vue({
        el: '#paymentmethod',
    });
</script>
@endsection