@extends('layouts.app-mother')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <tutorial></tutorial>
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-8 justify-content-between align-items-center">
                    <h2>@{{$t("message.all_account_delivery")}}</h2>
                </div>
                <div class="col-4">
                    <div class="text-right">
                        <new-magazine-many v-bind:btnclass="RoundedDark" :type="'New'" :reload-magazines="reloadDeliveries" :accounts-list="accountsList" v-model:loading-count="loadingCount"></new-magazine-many>
                    </div>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center">
                <div class="col-md-7">
                    <button type="button" class="btn rounded-white my-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 2 }"  @click="currentFilterIndex = 2">@{{$t("message.show_all")}}</button>
                    <button type="button" class="btn rounded-white mx-2 my-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 1 }"  @click="currentFilterIndex = 1">@{{$t("message.scheduled_messages")}}</button>
                    <button type="button" class="btn rounded-white my-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.sent_messages")}}</button>
                </div>
                <div class="col-md-5">
                    <div class="row mx-0 mb-3 justify-content-end">
                        <button class="btn rounded-grey my-1 mr-2" :disabled="!selected.length" @click="cancelSchedules">@{{$t('message.cancel_reservation')}}</button>
                        <div class="text-right">
                            <button class="btn rounded-red my-1" :disabled="!selected.length" @click="dialogVisible = true">@{{$t('message.delete_selected')}}</button>
                            <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                                <p>@{{ $t('message.confirm_text') }}</p>
                            </a-modal>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-self align-items-center font-size-table">
                <div class="col-1 px-small text-center">
                    <input type="checkbox" :checked="isAllSelected" @click="selectAll" class="m-1">
                </div>
                <div class="col-2 px-small text-center">
                    <span>@{{$t('message.target_account')}}</span>
                </div>
                <div class="col-2 col-xl-2 px-small text-center" @click="sortBy('schedule_at')" :class="sortedClass('schedule_at')">
                    <span>@{{$t('message.send_date_and_time')}}</span>
                </div>
                <div class="col-2 px-small text-center">
                    <span>@{{$t('message.text')}}</span>
                </div>
                <div class="col-2 col-xl-1 col-lg-1 px-small text-center">
                    <span>@{{$t('message.sending_target')}}</span>
                </div>
                <div class="col-2 col-xl-1 px-small text-center">
                    <span>@{{$t('message.number_of_people_sent')}}</span>
                </div>
                <div class="col-1 col-xl-3"></div>
            </div>
        </div>
        <div class="justify-content-between align-items-center">
            <div class="py-1" v-for="(data,key) in filterData" :key="key">
            <div class="py-1">
                <listview-magazines-many :reload-magazines="reloadDeliveries" :data="data" :selected="selected" :can-edit="canEdit"></listview-magazines-many>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<!--Main Pages-->
<script src="{{asset('js/components/magazine/listview-magazines-many.js')}}"></script>

<!-- -manyのつくファイルは新規登録時のみ使用 -->
<script src="{{asset('js/components/magazine/new-magazine.js')}}"></script>
<script src="{{asset('js/components/magazine/new-magazine-many.js')}}"></script>
<script src="{{asset('js/components/custom-components/messagetarget.js')}}"></script>
<script src="{{asset('js/components/custom-components/messagetarget-many.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-tags.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-tags-many.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-scenario.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-scenario-many.js')}}"></script>

<script src="{{asset('js/components/custom-components/message-target/messagetarget-registerdate.js')}}"></script>

<script src="{{asset('js/components/custom-components/content-panel/action-modal.js')}}"></script>
<script src="{{asset('js/components/custom-components/content-panel/contentpanel-createmessage.js')}}"></script>

<script src="{{asset('js/components/custom-components/message-action/messageaction-tag-many.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-action/messageaction-scenario-many.js')}}"></script>

<script src="{{asset('js/components/custom-components/messageaction-many.js')}}"></script>

<script src="{{asset('js/components/custom-components/add-content/addcontent-template.js')}}"></script>
<script src="{{asset('js/components/custom-components/addcontent-template-card.js')}}"></script>

<script src="{{asset('js/components/custom-components/addcontent-main.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-image.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-video.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-audio.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-other.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-stamp.js')}}"></script>

<script src="{{asset('js/components/magazine/custom-components/emoji.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/magazine-preview.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/select-draft.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-selection.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-date-selection.js')}}"></script>

<script src="{{asset('js/components/custom-components/custom-components/survey-questionnaire.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/custom-component/survey-behavior.js')}}"></script>

<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script src="{{asset('js/components/magazine/confirmation-modals/confirmation-test.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                all_account_delivery: 'All Account Delivery',
                new: "New",
                scheduled_messages: "Scheduled Messages",
                sent_messages: "Sent Messages",
                delete_selected: "Delete Selected",
                cancel_reservation: "Cancel Reservation",
                target_account: "Target Account",
                send_date_and_time: "Send Date And Time",
                text: "Text",
                sending_target: "Sending Target",
                number_of_people_sent: "Number Of Perple Who Sent",
                show_all: 'Show all',
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?'
            }
        },
        ja: {
            message: {
                all_account_delivery: '全アカウント配信',
                new: "新規",
                scheduled_messages: "送付予約のみ表示",
                sent_messages: "送付済みのみ表示",
                delete_selected: "選択したものを削除",
                cancel_reservation: "予約キャンセル",
                target_account: "対象アカウント",
                send_date_and_time: "送付日時",
                text: "本文",
                sending_target: "送付対象",
                number_of_people_sent: "到達人数",
                show_all: '全て表示',
                confirm_title: '確認',
                confirm_text: '本当に実行していいですか？'
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
            data: [],
            filterData: [],
            selected: [],
            RoundedDark: "btn-outline-dark",
            isAllSelected: false,
            sort: {
                key: '', // ソートキー
                isAsc: false // 昇順ならtrue,降順ならfalse
            },
            canEdit: false,
            accountsList: [],
            dialogVisible: false,
            confirmLoading: false,
            tutorial: {{ var_export(Auth::user()->finished_tutorial) }},
            currentFilterIndex: 2
        },
        watch: {
            currentFilterIndex: function () {
                this.filterOption()
            }
        },
        beforeMount() {
            this.reloadDeliveries()
        },
        methods: {
            filterOption() {
                if(this.currentFilterIndex == 2){
                    this.filterData = this.data
                } else if(this.currentFilterIndex == 1) {
                    this.filterData = this.data.filter(function(magazine){
                        return (magazine.schedule_at != null)
                    })
                } else if(this.currentFilterIndex == 0) {
                    this.filterData = this.data.filter(function(magazine){
                        return (magazine.schedule_at == null)
                    })
                }
            },
            sortBy: function(key) {
                this.sort.isAsc = this.sort.key === key ? !this.sort.isAsc : false;
                this.sort.key = key;
            },
            sortedClass: function(key) {
                return this.sort.key === key ? `sorted ${this.sort.isAsc ? 'asc' : 'desc' }` : '';
            },
            selectAll () {
                this.selected = []
                if (this.isAllSelected) {
                    this.isAllSelected = false
                } else {
                    for (var data in this.filterData) {
                        this.selected.push(this.filterData[data].id)
                    }
                    this.isAllSelected = true
                }
            },
            reloadDeliveries() {
                this.loadingCount++
                axios.get("deliveries/list")
                .then(response => {
                    this.data = response.data.magazines
                    this.canEdit = response.data.canEdit
                    this.accountsList = response.data.accountsList
                    this.filterOption()
                })
                .finally(() => this.loadingCount--)
            },
            deleteMagazine() {
                let self = this
                this.loadingCount++
                axios.delete('deliveries/' + this.selected)
                .then(response => {
                    self.selected = []
                    self.reloadDeliveries()
                    self.confirmLoading = false
                    self.handleCancel()
                })
                .finally(() => this.loadingCount--)
            },
            cancelSchedules() {
                let self = this
                this.loadingCount++
                axios.post('deliveries/cancel_schedules/' + this.selected)
                .then(response => {
                    self.selected = []
                    self.reloadDeliveries()
                })
                .finally(() => this.loadingCount--)
            },
            handleOk() {
                this.confirmLoading = true
                this.deleteMagazine()
            },
            handleCancel() {
                this.dialogVisible = false
            }
        }
    });
</script>
@endsection
@section('css-styles')
    <style>
        .activeViewStatus {
            border: solid 2px black;
            background-color: #e6e8ed;
        }
        .question-item {
            cursor: pointer;
        }
        .question-item:hover {
            background-color: #e7f9fd;
        }
        .text-omitted p {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }
        th.sorted.desc::after{
            display: inline-block;
            content: '▼';
        }
        th.sorted.asc::after{
            display: inline-block;
            content: '▲';
        }

        @media (max-width: 767px) {
            .justify-content-end {
                justify-content: flex-start !important;
            }
        }
        input[type=radio] {
        display: none; /* ラジオボタンを非表示にする */
            font-size:0.7rem;
        }

        input[type=radio]:checked {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }

        .action_list{
            cursor: pointer;
        }

        .actions_btn{
            font-size:0.5rem;
        }

        .selectAction{
            background: #EEE;
        }

        .card-body input[type="radio"]:checked + label {
        background-color: #5a6268;
        border-color: #545b62;
        color: #fff;
        }

        .radio_select{
            background: #333;
            color: #fff !important;
        }
</style>
@endsection