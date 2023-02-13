@extends('layouts.app')

@section('content')
    <div id="app">
        <div class="bg-white border rounded p-3">
            <div class="row justify-content-between align-items-center px-3" style="font-size:12px">
                <span style="font-size: 26px">Cross Analysis</span>
                <button type="button" class="btn btn-outline-dark mx-1" >New</button>
            </div>
            <div class="row justify-content-between align-items-center m-2">
                <select class="btn btn-outline-dark mx-1" style="color: black; background-color: white; font-size: 12px" >
                    <option selected>Filter Content</option>
                    <option>Tag</option>
                    <option>Scenario</option>
                    <option>Survey</option>
                    <option>Conversion</option>
                    <option>Click Rate</option>
                    <option>Register Date</option>
                    <option>Source</option>
                </select>
                <button type="button" class="btn rounded-red mx-1">ConfirmationMessage_Delete</button>
            </div>
            <div class="row justify-content-between align-items-center m-2" style="font-size:12px">
                <a-checkbox></a-checkbox>
                <select class="borderless-input custom-select m-1" style="width: 25%">
                    <option selected>Title</option>
                    <option>Option 2</option>
                </select>
                <span>Status</span>
                <span>Actions</span>
                <span>Amount of People</span>
                <span>Show Details</span>
            </div>  
        </div>
        <div id="content-container">
            <crossanalysis-new></crossanalysis-new>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-delete.js')}}"></script>
<script src="{{asset('js/components/crossanalysis/crossanalysis-new.js')}}"></script>
<script>
    var app = new Vue({ 
        el: '#app',
    });
</script>
@endsection