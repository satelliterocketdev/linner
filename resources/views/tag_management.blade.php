@extends('layouts.app')
@section('content')
<!-- <div class="container-fluid content-wrapper"> -->
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="bg-white rounded p-4 m-2 shadow-lg">
                <div class="row justify-content-end align-items-center">
                        <div class="m-2 float-right" style="font-family: Oswald; font-size: 2rem; color: #14cc32">
                            {{__("Tag_Management")}}
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-success m-2" type="button" id="manualDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-book"></i></button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="manualDropdownButton">
                                <a class="dropdown-item" href="#">How to find friends</a>
                                <a class="dropdown-item" href="#">How to manipulate your friends's scenario manually</a>
                                <a class="dropdown-item" href="#">How to select multiple friends and broadcast or change settings</a>
                                <a class="dropdown-item" href="#">Storage and management of custom search conditions</a>
                                <a class="dropdown-item" href="#">Delete messages registered in the scenario</a>
                                <a class="dropdown-item" href="#">How to import / export friend information to CSV</a>
                            </div>
                        </div>
                </div>
                <div class="row m-2">
                    {{__("Tag_intro")}}
                </div>
                <hr>
                <div id="dragableContanier"><table-item-dragable :tags="{{$tags}}" flag="tag" urltoget="tag_management" :actionbuttons="{{$tableButtons}}" :tableheaders="{{$tableHEaderArrs}}" ></table-item-dragable></div>
            </div>
        </div>
    </div>
<!-- </div> -->
@endsection