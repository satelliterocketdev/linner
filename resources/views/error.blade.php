@extends('layouts.app')

@section('content')
    <div id="app">
        <div class="bg-white border rounded p-3">
            <div class="row justify-content-between align-items-center px-3" style="font-size:12px">
                <h2>Error</h2>
            </div>
            <div class="row justify-content-between align-items-center">
            <button class="btn rounded-cyan m-1">Resend Messages</button>
                <button class="btn rounded-red m-1">ConfirmationMessage_Delete</button>
            </div>
            <div class="row justify-content-between align-items-center" style="font-size:12px">
                <div class="col-sm-1">
                    <a-checkbox></a-checkbox>
                </div>
                <div class="col-sm-2 text-center">
                    <span>Send date</span>
                </div>
                <div class="col-sm-4 text-center">
                    <span>Preview</span>
                </div>
                <div class="col-sm-1 text-center">
                    <span>Target</span>
                </div>
                <div class="col-sm-4"></div>
            </div>  
        </div>
        <div class="justify-content-center align-items-center">
            <error-entrylist></error-entrylist>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/error/custom-components/error-entrylist.js')}}"></script>
<script>
    var app = new Vue({
        el: '#app',
    });
</script>
@endsection