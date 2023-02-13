@extends('layouts.app')

@section('content')
    <div id="app">
        <div class="bg-white shadow border rounded p-3">
            <div class="d-flex justify-content-between align-items-center px-3" style="font-size:12px">
                <div>
                    <span style="font-size: 26px">Transmitted Media</span>
                </div>
            </div>
            <div class="d-flex py-2 justify-content-center align-items-end">
                    <addcontent-image :data="data" :wysiwyg="wysiwyg" :content="content"></addcontent-image>
                    <addcontent-video></addcontent-video>
                    <addcontent-audio></addcontent-audio>
                    <addcontent-other></addcontent-other>
            </div>
            <div class="d-flex py-2 justify-content-center align-items-end" style="font-size: 18px;">
                    <span class="mr-3">Filter results:</span>
                    <a-checkbox style="font-size: 18px;">Image</a-checkbox>
                    <a-checkbox style="font-size: 18px;">Audio</a-checkbox>
                    <a-checkbox style="font-size: 18px;">Video</a-checkbox>
                    <a-checkbox style="font-size: 18px;">Other</a-checkbox>
            </div>
        </div>
        <div class="footer bg-white shadow border rounded p-3 my-2">
            <div class="d-flex justify-content-end align-items-center px-3" style="font-size:12px">
                <a-checkbox style="font-size: 18px;">Check all media</a-checkbox>
                <button class="btn btn-danger mx-1">Hide all checked media</button>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/stepmail/add-content/addcontent-image.js')}}"></script>
<script src="{{asset('js/components/stepmail/add-content/addcontent-audio.js')}}"></script>
<script src="{{asset('js/components/stepmail/add-content/addcontent-video.js')}}"></script>
<script src="{{asset('js/components/stepmail/add-content/addcontent-other.js')}}"></script>
<script src="{{asset('js/components/stepmail/add-content/addcontent-template.js')}}"></script>
<script src="{{asset('js/components/stepmail/add-content/addcontent-stamp.js')}}"></script>
<script>
    var app = new Vue({
        el: '#app',
        data() {
            return {
                data: '',
                wysiwyg: '',
                content: {
                    image: null
                },
            }   
        }
    });
</script>
@endsection
