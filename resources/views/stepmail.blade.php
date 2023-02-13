@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <tutorial :state="tutorial_state"></tutorial>
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-6 justify-content-between align-items-center"> 
                    <h2>@{{$t("message.stepmail")}}</h2>
                </div>
                <div class="col-6 align-items-center">
                    <div class="row justify-content-end align-items-center">
                        @can(App\Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE)
                            <new-scenario v-bind:btnclass="RoundedDark" :type="'New'" :reload-scenario="reloadScenario" v-model:loading-count="loadingCount"> </new-scenario>
                        @endcan
                        <button v-on:click="viewMode = 'list'" v-bind:class="{ 'activeViewStatus' : viewMode == 'list' }" id="button-stepmail-listview" class="btn btn-outline-dark mx-1"><i class="fas fa-xs fa-list"></i></button>
                        <button v-on:click="viewMode = 'grid'" v-bind:class="{ 'activeViewStatus' : viewMode == 'grid' }" id="button-stepmail-gridview" class="btn btn-outline-dark mx-1"><i class="fas fa-xs fa-th"></i></button>
                    </div>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center mb-1">
                <div>
                    <button type="button" class="btn rounded-white m-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 2 }" @click="currentFilterIndex = 2">@{{$t("message.show_all")}}</button>
                    <button type="button" class="btn rounded-white m-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 1 }" @click="currentFilterIndex = 1">@{{$t("message.active_scenarios")}}</button>
                    <button type="button" class="btn rounded-white m-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.inactive_scenarios")}}</button>
                </div>
                @can(App\Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE)
                    <button class="btn rounded-red m-1" :disabled="disableDelete" @click="dialogVisible = true">@{{$t("message.delete_scenarios")}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                        <p>@{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                @endcan
            </div>
            <div class="row justify-content-between align-items-center px-2 font-size-table wordbreak-all">
                @can(App\Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE)
                    <div class="text-center">
                        <input type="checkbox" :checked="isAllSelected" @click="selectAll" class="m-1">
                    </div>
                @endcan
                <div class="col-3 text-left px-small">
                <select class="borderless-input custom-select m-1" v-model="selectedCategory" v-on:change="refresh">
                    <option v-bind:value="0">@{{$t("message.title")}}</option>
                    <option v-bind:value="1">@{{$t("message.creation_date_new")}}</option>
                    <option v-bind:value="2">@{{$t("message.creation_date_old")}}</option>
                    <option v-bind:value="3">@{{$t("message.delivery_status")}}</option>
                    <option v-bind:value="4">@{{$t("message.completed_deliveries_massive")}}</option>
                    <option v-bind:value="5">@{{$t("message.completed_deliveries_less")}}</option>
                    <option v-bind:value="6">@{{$t("message.number_of_subscribers_massive")}}</option>
                    <option v-bind:value="7">@{{$t("message.number_of_subscribers_less")}}</option>
                </select>
                </div>
                <div class="col-2 text-center px-small" v-if="viewMode == 'list'">
                    @{{$t('message.status')}}
                </div>
                <div class="col-2 col-sm-1 text-center px-small" v-if="viewMode == 'list'">
                    @{{$t('message.delivery_completed')}}
                </div>
                <div class="col-2 col-sm-1 text-center px-small" v-if="viewMode == 'list'">
                    @{{$t('message.subscription')}}
                </div>
                <div class="col-2 col-sm-1 text-center px-small" v-if="viewMode == 'list'">
                    @{{$t('message.after_delivery')}}
                </div>
                <div class="col-sm-3 text-center px-small" v-if="viewMode == 'list'"></div>
                <div class="col-7 text-center" v-else-if="viewMode == 'grid'"></div>
            </div>  
        </div>
        <div class="justify-content-between align-items-center">
            <div v-if="viewMode == 'grid'" class="row">
                <div class="col-sm-4 col-md-4 p-3" v-for="(data,key) in filterData" :key="key">
                    <gridview-scenarios :reload-scenario="reloadScenario" :data="data" :selected="selected" :can-edit="canEdit" v-model:loading-count="loadingCount"></gridview-scenarios>
                </div> 
            </div>
            <div v-else-if="viewMode == 'list'">
                <div class="py-1" v-for="(data,key) in filterData" :key="key">
                    <listview-scenarios :reload-scenario="reloadScenario" :data="data" :selected="selected" :can-edit="canEdit" v-model:loading-count="loadingCount"></listview-scenarios>
                </div>
            </div> 
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/stepmail/content-panel/contentpanel-createcarousel.js')}}"></script>
<script src="{{asset('js/components/stepmail/content-panel/contentpanel-addmap.js')}}"></script>
<script src="{{asset('js/components/stepmail/content-panel/contentpanel-addsurvey.js')}}"></script>
<script src="{{asset('js/components/stepmail/content-panel/contentpanel-addcontact.js')}}"></script>

<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-delete.js')}}"></script>
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-send.js')}}"></script>
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-close.js')}}"></script>
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-test.js')}}"></script>
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-testuser.js')}}"></script>
<script src="{{asset('js/components/stepmail/confirmation-modals/confirmation-testuser-confirm.js')}}"></script>

<!--Main Pages-->
<script src="{{asset('js/components/stepmail/newscenario.js')}}"></script>
<script src="{{asset('js/components/stepmail/editscenario.js')}}"></script>
<script src="{{asset('js/components/stepmail/newmessage.js')}}"></script>
<script src="{{asset('js/components/stepmail/editmessage.js')}}"></script>
<!--Cards/Lists-->
<script src="{{asset('js/components/stepmail/gridview-scenarios.js')}}"></script>
<script src="{{asset('js/components/stepmail/listview-scenarios.js')}}"></script>
<script src="{{asset('js/components/stepmail/editscenario-list.js')}}"></script>
<!-- Custom-Components -->
<script src="{{asset('js/components/stepmail/custom-components/survey-settings.js')}}"></script>
<script src="{{asset('js/components/custom-components/custom-components/survey-questionnaire.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/newmessage-slidebox.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/previously-uploaded.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/scenario-messages-list.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/select-draft.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/prepend-served-message-target-selection.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/prepend-message-target-selection.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/prepend-message-target-date-selection.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/scenariopreview.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/emoji.js')}}"></script>
<script src="{{asset('js/components/stepmail/custom-components/addimage-createcarousel.js')}}"></script>


<script src="{{asset('js/components/followers/userinfo.js')}}"></script>
<script src="{{asset('js/components/followers/user-journey.js')}}"></script>
<script src="{{asset('js/components/followers/user-setting-panel/usersetting-panel.js')}}"></script>

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

<script src="{{asset('js/components/custom-components/messagetarget.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-tags.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-scenario.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-survey.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-source.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-conversion.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-name.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-registerdate.js')}}"></script>

<script src="{{asset('js/components/custom-components/messageaction.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-action/messageaction-tag.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-action/messageaction-scenario.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-action/messageaction-survey.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-action/messageaction-menu.js')}}"></script>

<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                stepmail: 'Stepmail',
                title:'Title',
                active_scenarios: 'Active Scenarios',
                inactive_scenarios: 'Inactive Scenarios',
                delete_scenarios:'Delete Scenarios',
                status: 'Status',
                subscribed: 'Subscribed',
                subscription: 'Subscription',
                delivery_completed: 'Delivery Completed',
                after_delivery: 'After Delivery',
                creation_date_new: 'Creation date (newest)',
                creation_date_old: 'Creation date (olders)',
                delivery_status: 'Delivery status',
                completed_deliveries_massive: 'Number of completed deliveries (massive)',
                completed_deliveries_less: 'Number of completed deliveries (less)',
                number_of_subscribers_massive: 'Number of subscribers (massive)',
                number_of_subscribers_less: 'Number of subscribers (less)',
                show_all: 'Show all',
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?',
                from_register: 'Since registered'
            }
        },
        ja: {
            message: {
                stepmail: 'シナリオ配信',
                title: 'タイトル',
                active_scenarios: '配信中のみ表示',
                inactive_scenarios: '停止中のみ表示',
                delete_scenarios: '選択したものを削除',
                status: 'ステータス',
                subscribed: '配信中',
                subscription: '購読中',
                delivery_completed: '配信完了',
                after_delivery: '配信後',
                creation_date_new: '作成日（新しい順）',
                creation_date_old: '作成日（古い順）',
                delivery_status: '配信状態',
                completed_deliveries_massive: '配信完了人数（多い順）',
                completed_deliveries_less: '配信完了人数（少ない順）',
                number_of_subscribers_massive: '購読中人数（多い順）',
                number_of_subscribers_less: '購読中人数（少ない順）',
                show_all: '全て表示',
                confirm_title: '確認',
                confirm_text: '本当に実行していいですか？',
                from_register: '登録から'
            }
        }
    }

    // Create VueI18n instance with options
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}', // locale form config/app.php 
        messages, // set locale messages
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
            isAllSelected: false,
            disableDelete: true,
            //Variable for changing the vierw of the cards from grid to list
            viewMode: 'grid',
            RoundedDark: "btn-outline-dark",
            BtnSuccess: "btn-success",
            selectedCategory: 0,
            canEdit: {{ var_export($canEdit) }},
            dialogVisible: false,
            confirmLoading: false,
            tutorial: {{ var_export(Auth::user()->finished_tutorial) }},
            tutorial_state: 0,
            currentFilterIndex: 2
        },
        watch: {
            currentFilterIndex: function () {
                this.filterOption()
            }
        },
        beforeMount() {
            this.reloadScenario()
        },
        methods: {
            filterOption() {
                if (this.currentFilterIndex == 2) {
                    this.filterData = this.data
                } else {
                    this.filterData = this.data.filter(this.filterCheck(this.currentFilterIndex))
                }
            },
            filterCheck(filterIndex) {
                return function(scenario) {
                    return scenario.is_active == filterIndex
                }
            },
            reloadScenario() {
                this.loadingCount++
                axios.get("stepmail/lists")
                .then(response => {
                    this.data = response.data
                    this.filterOption()
                    this.refresh()
                })
                .finally(() => this.loadingCount--)
            },
            deleteScenario() {
                self = this
                this.loadingCount++
                axios.delete('stepmail/' + this.selected)
                .then(function(response){
                    self.selected = []
                    self.reloadScenario()
                    self.disableDelete = true
                    self.confirmLoading = false
                    self.handleCancel()
                })
                .finally(() => this.loadingCount--)
            },
            refresh() {
                switch(this.selectedCategory) {
                    case 0:
                        this.filterData.sort(function(a, b){
                            if(a.name < b.name) { return -1; }
                            if(a.name > b.name) { return 1; }
                            return 0;
                        })
                        break;
                    case 1:
                        this.filterData.sort(function(a, b) { 
                            return new Date(b.created_at) - new Date(a.created_at).getTime();
                        });
                        break;
                    case 2:
                        this.filterData.sort(function(a, b) { 
                            return new Date(a.created_at) - new Date(b.created_at).getTime();
                        });
                        break;
                    case 3:
                        //is_activeの場合は、上に表示
                        this.filterData.sort(function(a, b) { 
                            return b.is_active - a.is_active;
                        });
                        break;
                    case 4:
                        this.filterData.sort(function(a, b) { 
                            return b.user_count - a.user_count;
                        });
                        break;
                    case 5:
                        this.filterData.sort(function(a, b) { 
                            return a.user_count - b.user_count;
                        });
                        break;
                    case 6:
                        this.filterData.sort(function(a, b) { 
                            return b.subscription_count - a.subscription_count;
                        });
                        break;
                    case 7:
                        this.filterData.sort(function(a, b) { 
                            return a.subscription_count - b.subscription_count;
                        });
                        break;
                    default:
                        break;
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
            handleOk() {
                this.confirmLoading = true
                this.deleteScenario()
            },
            handleCancel() {
                this.dialogVisible = false
            },
            changeTutorialState(step) {
                if(this.tutorial == 1) return
                if (this.tutorial_state == step) {
                    this.tutorial_state++
                }
            }
        }
    });
    $('#tutorialBtn1').click(function () {
        app.changeTutorialState(0)
    })
</script>

<style scoped>
/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  /* transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0); */
  transition: all ease;
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(-10px);
  opacity: 0;
}

.activeViewStatus {
    border: solid 2px black;
    background-color: #e6e8ed;
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

.actions_btn{
    font-size:0.5rem;
}


.answer_list{
    cursor: pointer;
}

.selectAnswer{
    background: #EEE;
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

.scrollable {
    overflow-y: auto;
    max-height: 200px;
}

.auto_reply{
    font-size: 0.7rem;
}

</style>

@endsection

