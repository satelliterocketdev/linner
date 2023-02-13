@extends('layouts.app')
@section('content')
    <form action="/line/bot/callback/{{$webhook_token}}" method="post">
        <div class="row">
            <div class="col form-group" style="height: 500px">
                <textarea id="body" name="body" class="form-control" style="height: 450px"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button type="button" onclick="onSubmit()" class="btn btn-success">送信</button>
            </div>
        </div>
    </form>
@endsection

@section('footer-scripts')
<script>
    function onSubmit() {
        axios.post('/line/bot/callback/{{$webhook_token}}',
            JSON.parse($("#body").val())
        ).then(res => {});
    }
</script>
@endsection
