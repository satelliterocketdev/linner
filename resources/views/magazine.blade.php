@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <tutorial></tutorial>
        <div class="bg-white border rounded p-3">

            <div class="row px-1 align-items-center mb-2">
                <div class="col-6 justify-content-between align-items-center">
                    <h2>@{{$t("message.one_time_send")}}</h2>
                </div>
                <div class="col-6 align-items-center">
                    <div class="row justify-content-end align-items-center">
                        @can(App\Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE)
                            <new-magazine v-bind:btnclass="RoundedDark" :type="'New'" :reload-magazines="reloadMagazines" v-model:loading-count="loadingCount"></new-magazine>
                        @endcan
                        <button v-on:click="viewMode = 'list'" v-bind:class="{ 'activeViewStatus' : viewMode == 'list' }" id="button-magazine-listview" class="btn btn-outline-dark mx-1"><i class="fas fa-xs fa-list"></i></button>
                        <button v-on:click="viewMode = 'grid'" v-bind:class="{ 'activeViewStatus' : viewMode == 'grid' }" id="button-magazine-gridview" class="btn btn-outline-dark mx-1"><i class="fas fa-xs fa-th"></i></button>
                    </div>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center mb-3">
                <div>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 2 }" @click="currentFilterIndex = 2">@{{$t("message.show_all")}}</button>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 1 }" @click="currentFilterIndex = 1">@{{$t("message.scheduled_messages")}}</button>
                    <button type="button" class="btn rounded-white m-1 " v-bind:class="{ 'active-filter' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.sent_messages")}}</button>
                </div>
                @can(App\Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE)
                    <button class="btn rounded-red m-1" :disabled="disableDelete" @click="dialogVisible = true">@{{$t('message.delete_magazine')}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                        <p>@{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                @endcan
            </div>
            <div class="row justify-content-self align-items-center px-2 font-size-table">
                @can(App\Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE)
                    <div class="col-1 text-center px-small">
                        <input type="checkbox" :checked="isAllSelected" @click="selectAll" class="m-1">
                    </div>
                @endcan
                <div class="col-3 col-xl-2 text-center px-small" v-if="viewMode == 'list'">
                    <span>@{{$t('message.send_date_time')}}</span>
                </div>
                <div class="col-4 text-center px-small" v-if="viewMode == 'list'">
                    <span>@{{$t('message.message_body')}}</span>
                </div>
                <div class="col-2 col-xl-1 text-center px-small" v-if="viewMode == 'list'">
                    <span>@{{$t('message.send_target')}}</span>
                </div>
                <div class="col-2 col-xl-1 text-center px-small" v-if="viewMode == 'list'">
                    <span>@{{$t('message.number_of_people_sent')}}</span>
                </div>
                <div class="col-0 col-xl-3 pc-large"></div>
            </div>
        </div>
        <div class="justify-content-between align-items-center">
            <div v-if="viewMode == 'grid'" class="row">
                <div class="col-sm-4 col-md-4 p-3" v-for="(data,key) in filterData" :key="key">
                    <gridview-magazines :reload-magazines="reloadMagazines" :data="data" :selected="selected" :can-edit="canEdit" v-model:loading-count="loadingCount"></gridview-magazines>
                </div>
            </div>
            <div v-else-if="viewMode == 'list'">
                <div class="py-1" v-for="(data,key) in filterData" :key="key">
                    <listview-magazines :reload-magazines="reloadMagazines" :data="data" :selected="selected" :can-edit="canEdit" v-model:loading-count="loadingCount"></listview-magazines>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/magazine/new-magazine.js')}}"></script>
<script src="{{asset('js/components/custom-components/messagetarget.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-tags.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-scenario.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-registerdate.js')}}"></script>
<script src="{{asset('js/components/magazine/confirmation-modals/confirmation-test.js')}}"></script>

<!-- Message -->
<script src="{{asset('js/components/magazine/content-panel/contentpanel-createcarousel.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/contentpanel-addsurvey.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/contentpanel-addmap.js')}}"></script>
<script src="{{asset('js/components/custom-components/custom-components/survey-questionnaire.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/custom-component/survey-behavior.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/custom-component/addimage-createcarousel.js')}}"></script>
<script src="{{asset('js/components/magazine/content-panel/custom-component/previously-uploaded.js')}}"></script>

<!--Cards/Lists-->
<script src="{{asset('js/components/magazine/gridview-magazines.js')}}"></script>
<script src="{{asset('js/components/magazine/listview-magazines.js')}}"></script>
<!-- Custom-Components -->
<script src="{{asset('js/components/magazine/custom-components/emoji.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/magazine-preview.js')}}"></script>
{{-- <script src="{{asset('js/components/magazine/custom-components/messageaction.js')}}"></script> --}}
<script src="{{asset('js/components/magazine/custom-components/select-draft.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-selection.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-date-selection.js')}}"></script>

<script src="{{asset('js/components/magazine/message-action/messageaction-tag.js')}}"></script>
<script src="{{asset('js/components/magazine/message-action/messageaction-scenario.js')}}"></script>

<script src="{{asset('js/components/magazine/messageaction.js')}}"></script>

<!-- 使い回す -->
<script src="{{asset('js/components/custom-components/add-content/addcontent-template.js')}}"></script>
<script src="{{asset('js/components/custom-components/addcontent-template-card.js')}}"></script>

<script src="{{asset('js/components/custom-components/content-panel/action-modal.js')}}"></script>
<script src="{{asset('js/components/custom-components/content-panel/contentpanel-createmessage.js')}}"></script>

<script src="{{asset('js/components/custom-components/addcontent-main.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-image.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-video.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-audio.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-other.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-stamp.js')}}"></script>

<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                one_time_send: 'OneTime Send',
                scheduled_messages: "Scheduled Messages",
                sent_messages: "Sent Messages",
                delete_magazine: "Delete magazine",
                send_date_time: "Send date and time",
                message_body: "This statement",
                send_target: "Send target",
                number_of_people_sent: "Number of perple who sent",
                show_all: 'Show all',
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?'
            }
        },
        ja: {
            message: {
                one_time_send: '一斉配信',
                scheduled_messages: "送付予約のみ表示",
                sent_messages: "送付済みのみ表示",
                delete_magazine: "選択したものを削除",
                send_date_time: "送付日時",
                message_body: "本文",
                send_target: "送付対象",
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
            //Parse the data obtained from the controller and output it as [variable name] data
            data: [],
            filterData: [],
            selected: [],
            disableDelete: true,
            RoundedDark: "btn-outline-dark",
            viewMode: 'list',
            isAllSelected: false,
            canEdit: {{ var_export($canEdit) }},
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
            this.reloadMagazines()
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
                this.disableDelete = (this.selected.length <= 0)
            },
            reloadMagazines() {
                this.loadingCount++
                axios.get("magazine/lists")
                .then(response => {
                    this.data = response.data
                    this.filterOption()
                })
                .finally(() => this.loadingCount--)
            },
            deleteMagazine() {
                let self = this
                this.loadingCount++
                axios.delete('magazine/' + this.selected)
                .then(response => {
                    self.selected = []
                    self.reloadMagazines()
                    self.disableDelete = true
                    self.confirmLoading = false
                    self.handleCancel()
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

<style second>
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

.scrollable {
    overflow-y: auto;
    max-height: 200px;
}

.card-item {
    cursor: pointer;
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