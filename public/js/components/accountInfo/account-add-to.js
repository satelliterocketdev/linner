Vue.component('account-add-to', {
    template:
        `<div>
            <button  @click="showModal" v-bind:class="buttonclass">{{$t('message.account_add_to')}}</button>
            <a-modal :centered="true" v-model="visible" :confirmLoading="confirmLoading" :closable="closable" @ok="addAccount" :okText="$t('message.sign_up')" :maskClosable="true" :cancelButtonProps="{ props: { disabled: confirmLoading }}" :width="700" :destroyOnClose="false">
                <div class="justify-content-between align-items-center">
                    <h3 class="text-center">{{$t("message.line_account_information")}}</h3>
                    <p class="text-center">{{$t("message.explanatory_text_page1")}}</p>
                    <div id="area-input">
                        <!-- アカウント名 -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.account_name') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_account" name="input_account" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_name }}
                                </div>
                            </div>
                        </div>
                        <!-- ベーシックID -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.basic_id') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_basic_id" name="input_basic_id" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_basic_id }}
                                </div>
                            </div>
                        </div>
                        <!-- アクセストークン -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.Access_token') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_access_token" name="input_access_token" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_access_token }}
                                </div>
                            </div>
                        </div>
                        <!-- シークレットトークン -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.secret_token') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_secret_token" name="input_secret_token" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_secret_token }}
                                </div>
                            </div>
                        </div>
                        <!-- チャンネルID -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.channel_id') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_channel_id" name="input_channel_id" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_channel_id }}
                                </div>
                            </div>
                        </div>
                        <!-- Line User ID -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-4 text-md-right" style="font-size: 16px;">
                                {{ $t('message.line_user_id') }}
                            </div>
                            <div class="col-12 col-md-8">
                                <input id="input_user_id" name="input_user_id" type="text" class="form-control">
                                <div class="text-danger mt-1" v-if="validate_error">
                                {{ errMsg_line_user_id }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3 next-color">
                        <div class="text-danger mt-1" v-if="system_error">
                        {{ errMsg }}
                        </div>
                    </div>
                </div>
            </a-modal>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    account_add_to: 'Account add to',
                    line_account_information: 'Enter your LINE account information',
                    explanatory_text_page1: 'Open a LINE official account, set the messaging API from the settings, and check in LINE Deveroppers.',
                    secret_token: 'secret token',
                    line_user_id: 'Line User ID',
                    basic_id: 'Basic ID',
                    channel_id: 'channel id',
                    sign_up: 'sign up',
                    error: 'A system error has occurred'
                }
            },
            ja: {
                message: {
                    account_add_to: 'アカウント追加',
                    line_account_information: 'LINEアカウント情報を入力してください',
                    explanatory_text_page1: 'LINE 公式アカウントを開設し、設定から messaging API を設定し、 LINE Deveroppers 内で確認してください。',
                    secret_token: 'シークレットトークン',
                    line_user_id: 'Line User ID',
                    basic_id: 'Basic ID',
                    channel_id: 'チャンネルID',
                    sign_up: '登録する',
                    error: 'システムエラーが発生しました'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'type', 'loadingCount'],
    data() {
        return {
            visible: false,
            buttonclass: "btn mx-1 " + this.btnclass,
            confirmLoading: false,
            closable: true,
            validate_error: false,
            system_error: false,
            errMsg: "",
            errMsg_name: "",
            errMsg_basic_id: "",
            errMsg_access_token: "",
            errMsg_secret_token: "",
            errMsg_channel_id: "",
            errMsg_line_user_id: "",
        }
    },
    methods: {
        showModal() {
            this.visible = true
            this.confirmLoading = false
            this.closable = true
            this.validate_error = false
            this.system_error = false
        },
        handleOk(e) {
            this.visible = false
        },
        addAccount: function() {
            this.confirmLoading = true
            this.closable = false

            this.errMsg_name = ""
            this.errMsg_basic_id = ""
            this.errMsg_access_token = ""
            this.errMsg_secret_token = ""
            this.errMsg_channel_id = ""
            this.errMsg_line_user_id = ""

            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('addaccount', {
                name: input_account.value,
                basic_id: input_basic_id.value,
                access_token: input_access_token.value,
                secret_token: input_secret_token.value,
                channel_id: input_channel_id.value,
                line_user_id: input_user_id.value,
            })
            .then( res => {
                location.reload()
            })
            .catch(res => {
                if (res.response.status === 400) {
                    this.validate_error = true
                    if(res.response.data['name'] != null) {
                        this.errMsg_name = res.response.data['name'][0]
                    }
                    if(res.response.data['basic_id']) {
                        this.errMsg_basic_id = res.response.data['basic_id'][0]
                    }
                    if(res.response.data['access_token']) {
                        this.errMsg_access_token = res.response.data['access_token'][0]
                    }
                    if(res.response.data['secret_token']) {
                        this.errMsg_secret_token = res.response.data['secret_token'][0]
                    }
                    if(res.response.data['channel_id']) {
                        this.errMsg_channel_id = res.response.data['channel_id'][0]
                    }
                    if(res.response.data['line_user_id']) {
                        this.errMsg_line_user_id = res.response.data['line_user_id'][0]
                    }
                    this.confirmLoading = false
                    this.closable = true
                    console.log(res)
                    return
                }

                // 想定外のエラー
                this.system_error = true
                this.errMsg = this.$t('message.error');
                console.log(res)
                return
            })
            .finally(() => this.$emit('input', this.loadingCount - 1));
        },
    },
});