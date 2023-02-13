@extends('layouts.app')
@section('content')
<div class="container-fluid content-wrapper">
  <div class="row align-items-center">
    <div class="col-sm-12">
      <div class="bg-white rounded p-4 m-2 shadow-lg">
        <div class="row justify-content-end align-items-center">
          <div class="m-2 float-right" style="font-family: Oswald; font-size: 2rem; color: #14cc32">
              {{__("Tag_Management")}}
          </div>
        </div>
        <hr/>
        <div class="row m-2">
          <form method="post" action="{{action('TemplateController@store')}}">
          {{ csrf_field() }}
            <!-- template name-->
            <div class="form-group">
              <label for="templateName">{{__('Template_name')}} <span class="badge badge-danger  mr-2">{{__('Require')}}</span></label>
              <input type="text" class="form-control  form-control-sm" id="templateName"  name="templateName" aria-describedby="templateName" placeholder="{{__('Template_name')}}">
              <small id="templateName" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="tempFolder">{{__('Template_folder')}} <span class="badge badge-secondary mr-2">{{__('Secondary')}}</span></label>
              <select id="tempFolder" name="tempFolder" class="form-control form-control-sm">
                @foreach( $templates as $key=>$template )
                  <option value="{{$template->parent}}">{{$template->parent}}</option>
                  {{++$key}}
                @endforeach
              </select>
            </div>
            @include('tabpanels.distribution_message_registration_tab.dmr_main_tab')
            <!-- template name end -->
            <div class="form-group">
              <button type="button" onclick="submitElemntSaveTemplate(0);" class="btn btn-success" name="save" value="template" id="save_Template">{{__('Save_Template')}}</button>
              <button type="button" onclick="submitElemntSaveTemplate(1);" class="btn btn-primary" name="save"  value="draft" id="save_Draft">{{__('Save_Draft')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
