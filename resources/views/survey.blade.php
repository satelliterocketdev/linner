@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-sm-3 justify-content-between align-items-center">
                    <h2>@{{$t("message.survey_result")}}</h2>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center mb-3">
                <div>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.show_all")}}</button>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 1 }" @click="currentFilterIndex = 1">@{{$t("message.only_magazine")}}</button>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 2 }" @click="currentFilterIndex = 2">@{{$t("message.only_scenario")}}</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2 text-left">
                    <span>@{{$t("message.delivery")}}</span>
                </div>
                <div class="col-sm-8 text-left">
                    <span>@{{$t("message.question")}}</span>
                </div>
                <div class="col-sm-1 text-left">
                    <span>@{{$t("message.respondents")}}</span>
                </div>
                <div class="col-sm-1 text-left"></div>
            </div>
        </div>

        <div class="row m-1 border rounded shadow bg-white col-12" v-for="(data, key) in filterData">
            <listview-survey :data="data"></listview-survey>
        </div>
        <a-pagination
            :current="currentPage"
            :total="total"
            :page-size="pageSize"
            @change="change">
        </a-pagination>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/survey/listview-survey.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                survey_result: 'Survey Result',
                delivery: 'Delivery',
                question: 'Question',
                respondents: 'Respondents',
                show_all:'Show All',
                only_magazine:'Only Magazine',
                only_scenario:'Only Scenario',
            }
        },
        ja: {
            message: {
                survey_result: 'アンケート結果',
                delivery: '配信',
                question: '質問内容',
                respondents: '回答者数',
                show_all:'すべて表示',
                only_magazine:'一斉配信のみ表示',
                only_scenario:'シナリオ配信のみ表示',
            }
        }
    }
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el: '#app',
        data: {
            loadingCount: 0,
            currentFilterIndex: 0,
            data: [],
            filterData: [],
            filterDataTmp: [],
            currentPage: 1,
            total: 1,
            lastPage: 1,
            pageSize: 10
        },
        watch: {
            currentFilterIndex: function () {
                this.filterOption()
            }
        },
        beforeMount() {
            this.reloadSurveys()
        },
        methods: {
            filterOption() {

                if(this.currentFilterIndex == 0){
                    this.filterDataTmp = this.data
                } else if(this.currentFilterIndex == 1) {
                    this.filterDataTmp = this.data.filter(function(survey){
                        return (survey.type_delivery == 'magazines')
                    })
                } else if(this.currentFilterIndex == 2) {
                    this.filterDataTmp = this.data.filter(function(survey){
                        return (survey.type_delivery == 'scenarios')
                    })
                }

                this.currentPage = 1 //1ページ目に戻る
                let offset = (this.currentPage * this.pageSize) - this.pageSize
                let until = offset + this.pageSize
                if (until > this.filterDataTmp.length) {
                    until = this.filterDataTmp.length
                }
                this.filterData = this.filterDataTmp.slice(offset, until)
                this.total = this.filterDataTmp.length
                this.lastPage = Math.ceil(this.filterDataTmp.length / this.pageSize)

            },
            reloadSurveys() {
                let self = this
                this.loadingCount++
                axios.get("survey/lists")
                .then(function (response){
                    self.data = response.data
                    self.filterOption()
                    self.preparePagination(self.filterData)
                })
                .finally(() => this.loadingCount--)
            },
            change(page) {
                if (page >= 1 && page <= this.lastPage) {
                    this.currentPage = page
                    this.preparePagination(page);
                }
            },
            preparePagination() {
                let offset = (this.currentPage * this.pageSize) - this.pageSize
                let until = offset + this.pageSize
                if (until > this.data.length) {
                    until = this.data.length
                }
                this.filterData = this.data.slice(offset, until)
                this.total = this.data.length
                this.lastPage = Math.ceil(this.data.length / this.pageSize)
            }
        }
    });
</script>

<style>

</style>
@endsection