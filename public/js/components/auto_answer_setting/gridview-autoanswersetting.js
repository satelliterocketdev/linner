Vue.component('gridview-autoanswersetting', {
    template:
        `<div>
            <div class="card h-100 w-100" :class="{ disabled_box: disabledClass }">
                <div class="card-header" style="margin-bottom: 10px;">
                    <div class="row justify-content-center">
                        <a-checkbox v-show="canEdit" class="text-left col" @click="onChangeCheckbox"></a-checkbox>
                        <button class="btn btn-sm btn-primary text-right mx-1 mb-1" @click="onChangeEnabled">{{ $t("message.enabled_or_disabled") }}</button>
                    </div>
                    <div class="row justify-content-center align-items-center text-center">
                        <b class="message_title">{{ data.title }}</b>
                    </div>
                    <div class="row justify-content-center align-items-center">
                        <span v-if="data.is_active == 1" class="enabled_text">{{$t('message.enabled')}}</span>
                        <span v-else class="disabled_text">{{$t('message.disabled')}}</span>
                    </div>
                </div>
                <div class="text-center">
                    <textarea style="border: solid 1px; margin: 0 8px; width: calc(100% - 16px); height: 90px;" v-model="data.content_message" readonly="readonly"></textarea>
                </div>
                <div class="card-body" style="margin-top: 5px; padding-top: 5px;">
                    <!--<div class="text-left" style="margin-bottom: 15px;">
                        {{$t('message.satisfy_all')}}
                    </div>-->
                    <!--<div class="text-left p-1">
                        {{$t('message.number_of_responses')}}
                       <span style="margin-left: 10px; color: #0000CC;">{{ responseCount }}</span>
                    </div>-->
                    <div class="text-left p-1">
                        {{$t('message.response_date_and_time')}}
                        <span style="margin-left: 10px; color: #0000CC;">
                            <template v-for="(week,key) in data.week">
                                <span v-if="week.value">{{ week.label }}</span>
                            </template>
                            {{ data.from_time }}<span v-if="data.from_time">~</span>{{ data.to_time }}
                        </span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row justify-content-end align-items-center">
                        <new-auto-answer v-show="canEdit" v-bind:btnclass="BtnSuccess" :type="'Edit'" :reload-auto-answer-setting="reloadAutoAnswerSetting" :data="data" v-model:loading-count="loadingCountData"></new-auto-answer>
                        <button v-show="canEdit" class="btn btn-info mx-1 mb-1 small-text" @click="dialogVisible = true">{{ $t("message.copy") }}</button>
                        <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                            <p>{{ $t('message.confirm_text') }}</p>
                        </a-modal>
                        <confirmation-test :data="data"></confirmation-test>
                    </div>
                </div>
            </div>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    edit: 'edit',
                    copy: 'copy',
                    send_test: 'send test',
                    response_date_and_time: 'response date and time',
                    number_of_responses: 'number of responses',
                    enabled:'enabled',
                    disabled:'disabled',
                    satisfy_all: 'satisfy_all',
                    enabled_or_disabled: 'enabled/disabled',
                    confirm_title: 'Confirm',
                    confirm_text: 'Are you sure?'
                }
            },
            ja: {
                message: {
                    edit:'編集',
                    copy: '複製',
                    send_test: 'テスト',
                    response_date_and_time :'応答日時',
                    number_of_responses: '応答回数',
                    enabled: '有効',
                    disabled: '無効',
                    satisfy_all: 'すべてを満たす',
                    enabled_or_disabled: '有効/無効',
                    confirm_title: '確認',
                    confirm_text: '本当に実行していいですか？'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'reloadAutoAnswerSetting', 'canEdit', 'loadingCount'],
    data() {
        return {
            BtnSuccess: "btn-success small-text",
            dialogVisible: false,
            confirmLoading: false
        }
    },
    computed: {
        disabledClass: function () {
            return disabledClass = this.data.is_active == 1 ? false : true 
        },
        responseCount: function () {
            //TODO::delivery_countカラムの数値をもってくる予定。ひとまず保留
            return responseCount = 0
        },
        loadingCountData: {
            get() {
                return this.loadingCount
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    },
    methods: {
        onChangeCheckbox(d) {
            if (d.target.checked === true) {
                this.$parent.selected.push(this.data.id)
            } else {
                const index = this.$parent.selected.indexOf(this.data.id)
                this.$parent.selected.splice(index, 1);
            }
            this.$parent.disableDelete = (this.$parent.selected.length > 0) ? false : true
        },
        copyAutoAnswer() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('auto_answer_setting/copy', { id: this.data.id })
            .then(function(response){
                self.reloadAutoAnswerSetting()
                self.confirmLoading = false
                self.handleCancel()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        onChangeEnabled(){
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('auto_answer_setting/change_enable', { id: self.data.id })
            .then(function(response){
                self.reloadAutoAnswerSetting()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        handleOk() {
            this.confirmLoading = true
            this.copyAutoAnswer()
        },
        handleCancel() {
            this.dialogVisible = false
        }
    },

});