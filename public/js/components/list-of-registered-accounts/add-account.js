Vue.component('add-account', {
    template:
        `<div>
            <button v-if="type == 'header'" @click="showModal" class="btn mx-1 btn-outline-dark">{{$t('message.add_account')}}</button>
            <a v-if="type == 'grid'" @click="showModal" class="text-center">
                <img src="/img/menu/addaccount.png" width="180px;" class="rounded-circle">
                <p class="mt-2">{{$t('message.add_account')}}</p>
            </a>
            <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="800" :footer="null">
                <h3 class="text-center">{{$t("message.add_to")}}</h3>
                <span>@{{ $t('message.hint1') }}</span>
                <form class="form-horizontal wordbreak-all" id="newAccountForm" v-on:submit.prevent>
                    <input type="hidden" name="_token" :value="csrf">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{ $t('message.name') }}</label>
                        <div class="col-sm-8">
                            <input name="name" v-model="name" type="text" maxlength="33" class="form-control">
                            <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Basic ID</label>
                        <div class="col-sm-8">
                            <input name="basic_id" v-model="basic_id" type="text" class="form-control">
                            <span v-if="errors.basic_id" class="error">{{ errors.basic_id[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Channel ID</label>
                        <div class="col-sm-8">
                            <input name="channel_id" v-model="channel_id" type="text" maxlength="11" class="form-control">
                            <span v-if="errors.channel_id" class="error">{{ errors.channel_id[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">User ID</label>
                        <div class="col-sm-8">
                            <input name="user_id" v-model="user_id" type="text" maxlength="33" class="form-control">
                            <span v-if="errors.account_user_id" class="error">{{ errors.account_user_id[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{ $t('message.access_token') }}</label>
                        <div class="col-sm-8">
                            <input name="access_token" v-model="access_token" type="text" maxlength="172" class="form-control">
                            <span v-if="errors.channel_access_token" class="error">{{ errors.channel_access_token[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Channel Secret</label>
                        <div class="col-sm-8">
                            <input name="channel_secret" v-model="channel_secret" type="text" maxlength="32" class="form-control">
                            <span v-if="errors.channel_secret" class="error">{{ errors.channel_secret[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{ $t('message.follow_link') }}</label>
                        <div class="col-sm-8">
                            <input name="follow_link" v-model="follow_link" type="text" class="form-control">
                            <span v-if="errors.line_follow_link" class="error">{{ errors.line_follow_link[0] }}</span>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center p-2">
                        <button type="submit" @click="register" class="btn btn-info m-1">{{ $t('message.next') }}</button>
                    </div>
                </form>
            </a-modal>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    add_account: 'Add Account',
                    add_to: 'Add Line account',
                    input_line_information: 'Enter your LINE account information',
                    hint1: 'Establish LINE official account, messaging from settings',
                    hint2: 'Please paste in the messassing API image of the LINE official account.',
                    access_token: 'Access Token',
                    hint3: 'For LINE official account response settings, set “Response mode” to Bot, “Webhook” to on, and “greeting message” to off.',
                    hint4: 'Please change the monthly plan of the LINE official account according to your plan.',
                    next: 'Register',
                    previous: 'Back',
                    name: 'Account Name',
                    follow_link: 'Line Follow Link'
                }
            },
            ja: {
                message: {
                    add_account: 'アカウント追加',
                    add_to: 'LINEアカウント情報を入力してください',
                    input_line_information: 'LINEアカウント情報を入力してください',
                    hint1: 'LINE公式アカウントを開設し、設定からmessaging APIを設定し、LINE Developers内で確認してください。',
                    hint2: 'LINE公式アカウントのmessassing API画像に貼り付けてください。',
                    access_token: 'アクセストークン',
                    hint3: 'LINE公式アカウント応答設定は、「応答モード」をBotに、「Webhook」をオンに、「挨拶メッセージ」をオフにしてください。',
                    hint4: 'ご利用のプランに応じて、LINE公式アカウントの月額プラン変更をお願いします。',
                    next: '登録する',
                    previous: '戻る',
                    name: 'アカウント名',
                    follow_link: 'Lineフォローリンク'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'type', 'reloadAccounts', 'loadingCount'],
    data() {
        return {
            visible: false,
            name: '',
            basic_id: '',
            channel_id: '',
            user_id: '',
            access_token: '',
            channel_secret: '',
            follow_link: '',
            errors: [],
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        register() {
            form = $("#newAccountForm")
            form.validate({
                rules: {
                    name: "required",
                    basic_id: "required",
                    channel_id: "required",
                    user_id: "required",
                    access_token: "required",
                    channel_secret: "required",
                    follow_link: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            self = this
            this.$emit('input', this.loadingCount + 1)
            axios({
                method: 'post',
                url: 'accounts',
                responseType: 'json',
                data: {
                    name: this.name,
                    basic_id: this.basic_id,
                    channel_id: this.channel_id,
                    account_user_id: this.user_id,
                    channel_access_token: this.access_token,
                    channel_secret: this.channel_secret,
                    line_follow_link: this.follow_link
                }
            }).then(function(response){
                self.handleOk()
                self.reloadAccounts()
            }).catch(error => {
                if (error.response.status == 422) {
                    this.errors = error.response.data
                    console.log(error.response);
                }
            })
            .finally(() => this.$emit('input', this.loadingCount - 1));
        }
    },
});
