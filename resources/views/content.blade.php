@extends('layouts.app')
@section('content')
<div id="content_layout">
  @if($content == 'followers')
      <followers-layout urltoget="{{$url}}" followers="{{$followers}}" status="{{$status}}"></followers-layout>
  @elseif($content == 'tag_management')
      <tag-layout urltoget="{{$url}}" followers="{{$followers}}" user="{{$user}}" tags="{{$tags}}"></tag-layout>
  @elseif($content == 'templates')
      <template-layout name="template" urltoget="{{$url}}"></template-layout>
  @elseif($content == 'transmittedmedia')
  {{$content}}
    <transmittedmedia-layout urltoget="transmittedmedia"></transmittedmedia-layout>
  @elseif($content == 'conversion')
    <conversion-layout urltoget="conversion"></conversion-layout>
  @endif
</div>
@endsection