@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div class="bg-white rounded py-3">
            <div class="d-flex align-items-center justify-content-between px-3 mb-3">
                <div class="col-8 col-sm-6 px-0"> 
                    <h2>@{{$t("message.inqueries")}}</h2>
                </div>
                <div class="col-4 col-sm-6 px-0 text-md-right">
                    <div class="justify-content-end align-items-center">
                        <new-inqueries v-bind:btnclass="RoundedDark" :reload-inqueries="reloadInqueries"></new-inqueries>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-between align-items-center">
            <h5 style="margin-top:10px;">@{{$t("message.list_of_inquiries")}}</h5>
            <div  v-for="(data,key) in filterData" :kye="key">
                <listview-inqueries :reload-inqueries="reloadInqueries" :data="data"></listview-inqueries>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<!--Main Pages-->
<script src="{{asset('js/components/inqueries/detail-inqueries.js')}}"></script>
<script src="{{asset('js/components/inqueries/new-inqueries.js')}}"></script>
<script src="{{asset('js/components/inqueries/listview-inqueries.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                inqueries: 'inqueries',
                list_of_inquiries: 'list of inquiries',
            }
        },
        ja: {
            message: {
                inqueries: 'お問い合わせ',
                list_of_inquiries: 'お問い合わせ一覧',
            }
        }
    }
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el:"#app",
        data: {
            loadingCount: 0,
            visible: false,
            nice: 'asdasd',
            data: [],
            filterData: [],
            RoundedDark: "btn-outline-dark",
            BtnSuccess: "btn-success",
        },
        beforeMount() {
            this.reloadInqueries()
        },
        methods: {
            showModal: function () {
                this.visible = true;
            },
            handleOk(e) {
                this.visible = false;
            },
            reloadInqueries() {
                this.loadingCount++
                axios.get("inqueries/lists")
                .then(response => (this.data = response.data, this.filterData = this.data))
                .finally(() => this.loadingCount--)
            },
        }
    })
</script>
@endsection
@section('css-styles')
    <style>
        .btn-success {
            background-color: #1dcd00;
            border-color: #22e200;
        }

        .btn-info {
            background-color: #4AD8FA;
            border-color: #6bdff9;
        }

        .btn-danger {
            background-color: #FF7474;
            border-color: #fc8d8d;
        }

        .btn-secondary {
            background-color: #9b9b9b;
            border-color: #c6c6c6;
        }
    </style>
@endsection