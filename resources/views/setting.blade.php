@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <div class="bg-white shadow border rounded p-3">
            <div class="row justify-content-between align-items-center px-3" style="font-size:12px">
                <div>
                    <h2>Auto Response</h2>
                    <span> - Friends while adding automatic message</span>
                </div>
                <autoreply-new></autoreply-new>
            </div>
            <div class="row p-2 justify-content-between align-items-center btn-group-toggle" data-toggle="buttons">
                <div>
                    <button class="btn rounded-cyan m-1">Display only during deliver</button>
                    <button class="btn rounded-green m-1">Display only when stopped</button>
                </div>
                <button class="btn rounded-red m-1" >Delete Selected</button>
            </div>
            <div class="px-4">
                <div class="row align-items-center">
                <a-checkbox class="mx-2"></a-checkbox>
                <span>Check to send automail to friends who were registered before joing this system</span>
                </div>
                <div class="row align-items-center">
                <a-checkbox class="mx-2"></a-checkbox>
                <span>Check to send automail to friends who have unblocked this account</span>
                </div>
                <div class="row align-items-center">
                <a-checkbox class="mx-2"></a-checkbox>
                <span>Check to make it possible to send multiple automails to one user</span>
                </div>
            </div>
            <div class="row px-2">
                <a-checkbox></a-checkbox>
            </div>
        </div>
        <div id="container-1" class="d-flex justify-content between my-4">
            <div class="col-sm-4">
                <autoreply-card></autoreply-card>
            </div>
        </div>

        <div class="bg-white shadow border rounded p-3">
            <div class="row justify-content-between align-items-center px-3" style="font-size:12px">
                <div>
                    <h2>Auto Response</h2>
                    <span> - auto response message</span>
                </div>
                <autoreply-new></autoreply-new>
            </div>
            <div class="row p-2 justify-content-between align-items-center btn-group-toggle" data-toggle="buttons">
                <div>
                    <button class="btn rounded-cyan m-1">Display only during deliver</button>
                    <button class="btn rounded-green m-1">Display only when stopped</button>
                </div>
                <button class="btn rounded-red m-1" >Delete Selected</button>
            </div>
            <div class="px-2" >
                <div class="d-flex justify-content-start align-items-center py-1">
                    <span> Automatic response mail sending</span>
                    <select class="mx-2 custom-select" style="width: 50%">
                        <option>5 minutes after the message arrived</option>
                        <option></option>
                    </select>
                </div>
                <div class="d-flex justify-content-start align-items-center py-1">
                    <span> After the automatic reply mail is delivered once</span>
                    <select class="mx-2 custom-select" style="width: 50%">
                        <option>1 hour</option>
                        <option></option>
                    </select>
                    <span> do not send</span>
                </div>
                <div class="row px-2">
                    <a-checkbox></a-checkbox>
                </div>

            </div>
        </div>
        <div id="container-2" class="d-flex justify-content between my-4">
            <div class="col-sm-4">
                <autoreply-card></autoreply-card>
            </div>
        </div>


        <div class="footer">
            <div class="d-flex justify-content-end align-items-center">
                <button class="btn-"></button>
                <button></button>
                <button></button>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/setting/custom-components/autoreply-card.js')}}"></script>
<script src="{{asset('js/components/setting/custom-components/autoreply-friendmessagetarget.js')}}"></script>
<script src="{{asset('js/components/setting/autoreply-new.js')}}"></script>

<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-test.js')}}"></script>
<script>
    var app = new Vue({
        el: '#app',
    });
</script>

<style>
.day-option {
    border: solid 2px blue;
    background-color: #5494f9;
    color: white;
}
</style>
@endsection