Vue.component('detail', {
    template:
        `<div>
            <button @click="showModal" v-bind:class="buttonclass">{{$t('message.details')}}</button>
            <a-modal :centered="true" v-model="visible" @ok="handleOk" @close="handleOk" :width="700" :footer="null">
                <form class="form-horizontal wordbreak-all" id="updateAccountForm" v-on:submit.prevent>
                    <!-- アカウント名 -->
                    <div class="form-group row">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.account_name') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            <input name="name" type="text" class="form-control" v-model="data.name">
                            <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
                        </div>
                    </div>
                    <!-- ベーシックID -->
                    <div class="row mb-2 line-height">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.line_id') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            {{data.basic_id}}
                        </div>
                    </div>
                    <!-- チャンネルID -->
                    <div class="row mb-2 line-height">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.channel_id') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            {{data.channel_id}}
                        </div>
                    </div>
                    <!-- secret ID -->
                    <div class="form-group row">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.channel_secret') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            <warning-secret @update="updateSecret" :secret="secret" :id="data.id" style="display: inline" v-model:loading-count="loadingCountData"></warning-secret>
                            <span v-if="errors.channel_secret" class="error">{{ errors.channel_secret[0] }}</span>
                        </div>
                    </div>
                    <!-- アクセストークン -->
                    <div class="form-group row">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.Access_token') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            <warning-accesstoken @update="updateAccessToken" :access-token="data.channel_access_token" :id="data.id" style="display: inline" v-model:loading-count="loadingCountData"></warning-accesstoken>
                            <span v-if="errors.channel_access_token" class="error">{{ errors.channel_access_token[0] }}</span>
                        </div>
                    </div>
                    <!-- webhook url -->
                    <div class="row mb-2 line-height">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.webhook_url') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            {{data.line_follow_link}}/line/bot/callback/{{data.webhook_token }}
                        </div>
                    </div>
                    <!-- 友達追加URL -->
                    <div class="form-group row">
                        <div class="col-12 col-sm-3">
                            {{ $t('message.line_link') }}
                        </div>
                        <div class="col-12 col-sm-8">
                            <input type="text" name="follow_link" class="form-control" v-model="data.line_follow_link">
                            <span v-if="errors.line_follow_link" class="error">{{ errors.line_follow_link[0] }}</span>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center p-2">
                        <button type="submit" @click="handleOk" class="btn btn-info m-1">{{ $t('message.close') }}</button>
                    </div>
                </form>
            </a-modal>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    details: 'details',
                    account_name: 'Account Name',
                    line_id: 'Line@ ID',
                    channel_id: 'Channel ID',
                    channel_secret: 'Channel Secret',
                    Access_token: 'Access Token',
                    webhook_url: 'Webhook URL',
                    line_link: 'Link to line follow',
                    close: 'close'
                }
            },
            ja: {
                message: {
                    details: '詳細',
                    account_name: 'アカウント名',
                    line_id: 'Line@ ID',
                    channel_id: 'Channel ID',
                    channel_secret: 'Channel Secret',
                    Access_token: 'アクセストークン',
                    webhook_url: 'Webhook URL',
                    line_link: 'LINEフォローリンク',
                    close: '閉じる'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'loadingCount', 'reloadAccounts'],
    data() {
        return {
            visible: false,
            buttonclass: "btn mx-1 " + "btn-outline-dark",
            accessToken: this.data.channel_access_token,
            secret: this.data.channel_secret,
            errors: [],
            isUpdate: false
        }
    },
    computed: {
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
        showModal() {
            this.visible = true
            this.isUpdate = false
        },
        handleOk(e) {
            this.visible = false
            if (this.isUpdate) {
                this.reloadAccounts()
            }
        },
        updateSecret(secret) {
            this.secret = secret
            this.isUpdate = true
        },
        updateAccessToken(accessToken) {
            this.accessToken = accessToken
            this.isUpdate = true
        },
    },
});
