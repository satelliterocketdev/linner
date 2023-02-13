Vue.component('account-information-registration', {
    template:
        `<div>
            <button @click="showModal" v-bind:class="buttonclass">{{$t('message.next')}}</button>
            <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="900" :footer="null">
                <div class="justify-content-between align-items-center">
                    <h3 class="text-center">{{$t("message.line_account_information")}}</h3>
                    <div class="text-center mb-3">
                        <div class="maru1Page2 mr-3" style="display: inline-block;">
                            <span>1</span>
                        </div>
                        <div class="maru2Page2 ml-3" style="display: inline-block;">
                            <span>2</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="text-center">
                            <img class="mr-2" src="/img/menu/accountinformation3.png" width="40%"/>
                        </div>
                    </div>
                    <p class="text-center">{{$t("message.explanatory_text_page2_1")}}</p>
                    <!-- webhook URL -->
                    <div class="row mb-2">
                        <div class="col-3 text-right" style="font-size: 16px;">
                            {{ $t('message.webhook_url') }}
                        </div>
                        <div class="col-8">
                            <input type="text" id="webhook_url" name="webhook_url" value={{webhook_url}} class="form-control" style="color: #0000FF;"/>
                        </div>
                    </div>
                    <p class="text-center mt-3 mb-3" style="font-size: 12px;" >{{$t("message.explanatory_text_page2_2")}}</p>
                    <p class="text-center mb-3"><strong>{{$t("message.explanatory_text_page2_3")}}</strong></p>
                    <div class="text-center mt-3 next-color">
                        <button class="btn btn-sm btn-primary mx-1">{{$t('message.confirm')}}</button>
                    </div>
                </div>
            </a-modal>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    line_account_information: 'Enter your LINE account information',
                    explanatory_text_page2_1: 'Please paste it on the messaging API screen of the LINE official account.',
                    explanatory_text_page2_2: 'For the LINE official account response settings, set “Response mode” to Bot, “Webhook” to on, and “greeting message” to off.',
                    explanatory_text_page2_3: '* Please change the monthly plan for the LINE official account according to your plan.',
                    webhook_url: 'Webhook URL',
                    next: 'next',
                    confirm: 'confirm',
                }
            },
            ja: {
                message: {
                    line_account_information: 'LINEアカウント情報を入力してください',
                    explanatory_text_page2_1: 'LINE 公式アカウントの messaging API 画面に貼り付けてください。',
                    explanatory_text_page2_2: 'LINE 公式アカウントの応答設定は、「応答モード」をBotに、「Webhook」をオンに、「あいさつメッセージ」をオフにしてください。',
                    explanatory_text_page2_3: '※ご利用のプランに応じて、LINE公式アカウントの月額プラン変更をお願いします。',
                    webhook_url: 'Webhook URL',
                    next: '次へ',
                    confirm: '完了',
                }
            }
        }
    },
    props: ['data', 'btnclass', 'reloadUser', 'type', 'webhook_url'],
    data() {
        return {
            visible: false,
            buttonclass: "btn btn-sm btn-primary mx-1 " + this.btnclass,
        }
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
    },
});
