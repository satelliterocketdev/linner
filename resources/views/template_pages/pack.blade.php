@extends('layouts.app')
@section('content')
<div class="container-fluid content-wrapper">
  <div class="row align-items-center">
    <div class="col-sm-12">
      <div class="bg-white rounded p-4 m-2 shadow-lg">
        <div class="row justify-content-end align-items-center">
          <div class="m-2 float-right" style="font-family: Oswald; font-size: 2rem; color: #14cc32">
            {{__("Create_Message_Pack")}}
          </div>
        </div>
        <hr/>
        <div class="row">
          <!-- template name-->
          <div class="col-md-12">
            <div class=" form-group row">
              <label for="templateName" class="col-sm-4">{{__('Template_name')}}<span class="badge badge-danger mr-2">{{__('Require')}}</span></label>
              <input type="text" class="form-control form-control-sm col-sm-6" id="templateName" name="templateName" aria-describedby="templateName" placeholder="{{__('Template_name')}}">
            </div>
            <div class="form-group row">
              <label for="tempFolder" class="col-sm-4">>{{__('Template_folder')}}<span class="badge badge-danger mr-2">{{__('Require')}}</span></label>
              <select id="tempFolder" name="tempFolder" class="form-control form-control-sm col-sm-6">
                @foreach( $templates as $key=>$template )
                  <option value="{{$template->parent}}">{{$template->parent}}</option>
                  {{++$key}}
                @endforeach
              </select>
            </div>
          </div> 
        </div>
        <!-- Button -->
        <div class="row p-2">
          <button class="btn btn-block btn-warning shadow" type="button" onclick="submitElemntSaveTemplate(0);" >{{__("Registration")}}</button>
          <a class="btn btn-block btn-outline-success shadow" href="{{url()->previous()}}" role="button">戻る</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
