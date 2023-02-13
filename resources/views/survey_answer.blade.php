<div class="survey_answer" v-cloak>
    <p class="auto_reply">
        {{ $setData['auto_reply'] }}
    </p>
    <p class="behavior">
        @if($setData['behavior'] ==  'url_open')
            <p>以下URLより移動お願いします。</p>
            <a href="{{ $setData['data'] }}" target="_blank">{{ $setData['data'] }}</a>
        @elseif($setData['behavior'] ==  'call_tel')
            <p>以下番号から電話発信お願いします。</p>
            <a href="tel:{{ $setData['data'] }}" target="_blank">{{ $setData['data'] }}</a>
        @elseif($setData['behavior'] ==  'send_mail')
            <p>以下番アドレスにメール送信お願いします。</p>
            <a href="mailto:{{ $setData['data'] }}" target="_blank">{{ $setData['data'] }}</a>
        @endif
    </p>
</div>
<style>
    .survey_answer{
        margin: 15px 5px 0 ;
        text-align: center;
        font-size: 4rem;
    }
    .behavior{
        padding: 0 5px;
    }
</style>