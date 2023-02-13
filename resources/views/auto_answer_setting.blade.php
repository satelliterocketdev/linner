@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <tutorial></tutorial>
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-9 justify-content-between align-items-center"> 
                    <h2>@{{$t("message.auto_reply_message")}}</h2>
                </div>
                <div class="col-3 align-items-center">
                    <div class="row justify-content-end align-items-center">
                        @can(App\Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE)
                            <new-auto-answer v-bind:btnclass="RoundedDark" :type="'New'" :reload-auto-answer-setting="reloadAutoAnswerSetting" v-model:loading-count="loadingCount"></new-auto-answer>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn header_btn m-1" v-bind:class="{ 'select_header_btn' : currentFilterIndex == 2 }" @click="currentFilterIndex = 2">@{{$t("message.all_auto_answers")}}</button>
                    <button type="button" class="btn header_btn m-1" v-bind:class="{ 'select_header_btn' : currentFilterIndex == 1 }" @click="currentFilterIndex = 1">@{{$t("message.active_auto_answers")}}</button>
                    <button type="button" class="btn header_btn m-1" v-bind:class="{ 'select_header_btn' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.inactive_auto_answers")}}</button>
                </div>
                @can(App\Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE)
                    <button class="btn rounded-red m-1" :disabled="disableDelete" @click="dialogVisible = true">@{{$t("message.delete_selected")}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                        <p>@{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                @endcan
            </div>
        </div>
        <div class="justify-content-between align-items-center">
            <div class="row">
                <div class="col-sm-4 col-md-4 p-3" v-for="(data,key) in filterData" :key="key">
                    <gridview-autoanswersetting :reload-auto-answer-setting="reloadAutoAnswerSetting" :data="data" :can-edit="canEdit" v-model:loading-count="loadingCount"></gridview-autoanswersetting>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')

<script src="{{asset('js/components/auto_answer_setting/new-auto-answer.js')}}"></script>

<!--Main Pages-->
<script src="{{asset('js/components/auto_answer_setting/gridview-autoanswersetting.js')}}"></script>
<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script src="{{asset('js/components/auto_answer_setting/confirmation-modals/confirmation-test.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                auto_reply_message: 'auto reply message',
                active_auto_answers: 'active auto answers',
                inactive_auto_answers: 'inactive auto answers',
                all_auto_answers: 'all auto answers',
                delete_selected: 'delete selected',
                new: 'new',
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?'
            }
        },
        ja: {
            message: {
                auto_reply_message: '自動応答メッセージ',
                active_auto_answers: '配信中のみ表示',
                inactive_auto_answers: '停止中のみ表示',
                all_auto_answers: 'すべて表示',
                delete_selected: '選択したものを削除',
                new: '新規',
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
            RoundedDark: "btn-outline-dark",
            selected: [],
            disableDelete: true,
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
            this.reloadAutoAnswerSetting()
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
                return function(autoanswer) {
                    return autoanswer.is_active == filterIndex
                }
            },
            reloadAutoAnswerSetting() {
                this.loadingCount++
                axios.get("auto_answer_setting/lists")
                .then(response => {
                    this.data = response.data
                    this.filterOption()
                })
                .finally(() => this.loadingCount--)
            },
            deleteAutoAnswer() {
                let self = this

                // 念の為
                if (this.disableDelete) {
                    return
                }
                this.loadingCount++
                axios.delete('auto_answer_setting/' + this.selected)
                .then(response => {
                    self.selected = []
                    self.reloadAutoAnswerSetting()
                    self.disableDelete = true
                    self.confirmLoading = false
                    self.handleCancel()
                })
                .finally(() => this.loadingCount--)
            },
            handleOk() {
                this.confirmLoading = true
                this.deleteAutoAnswer()
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

    /*
    * ヘッダーメニュー
    */
    .header_btn{
        background: #fff;
        border: 1px solid #000;
        border-radius: 50px;
        color: #000;
        font-size: 12px;
    }

    .select_header_btn{
        border: none;
        background: #32A5D0;
        color: #fff;
    }

    .selected_btn{
        background: #32A5D0;
        border-radius: 50px;
        color: #fff;
        font-size: 12px;
    }

    .date_button{
        margin: 0 0 10px 10px;
    }

    .date_button label{
        font-size:10px !important;
    }

    .date_button .btn{
        color: #999;
        background: #fff;
        border: 1px solid #999;
    }

    .date_button .active{
        color: #fff !important;
        background: #999 !important;
        border: 1px solid #999 !important;
    }

    .btn label{
        margin: 0;
    }

    /*
    * 有効/無効 関連
    */

    .message_title{
        color: #32A5D0;
        font-size:1.4rem;
        line-height: 1;
        margin: 10px 0;
    }

    .enabled_text{
        color: #0000CC;
        font-size:1.1rem;
    }

    .disabled_text{
        color: #ff0000;
        font-size:1.1rem;
    }

    .disabled_box{
        background: #bbb;
    }

    .disabled_box .message_title{
        color: #FFFFFF;
    }

    /*
    * 新規登録・編集
    */
    .condition_box{
        margin: 0 0 15px 0;
    }
    .condition_detail{
        margin: 0 0 15px 30px;
    }
    .week_box{
        display: flex;
        flex-wrap: wrap;
        margin:  0 0 15px;
    }
    .week_item{
        margin: 0 3px 0 0;
    }
    .week_btn{
        cursor: pointer;
        padding:3px 8px;
        border: 1px solid #434343;
        border-radius: 5px;
        margin: 0;
    }

    .week_checkbox{
        display: none;
    }

    .week_checkbox:checked + .week_btn {
         background-color: #32A5D0;
         color: #fff;
    }

    .timeset_box{
        display: flex;
        flex-wrap: wrap;
    }

  </style>
@endsection