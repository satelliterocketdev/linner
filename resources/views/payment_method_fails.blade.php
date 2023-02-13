@extends('auth.main')

@section('content')
    <div id="paymentmethod">
        <div class="row">
            <div class="col text-center mt-5">
                不正な決済情報が連携されました。
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
    var paymentmethod = new Vue({
        el: '#paymentmethod',
    });
</script>
@endsection