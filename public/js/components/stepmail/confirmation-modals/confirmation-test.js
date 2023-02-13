Vue.component ('confirmation-test', {
    template: 
    `<div>
    <div>
        <button type="button" v-if="data.is_edit" @click="showModal" class="btn btn-secondary mx-1 small-text font-size-table">{{$t('message.test')}}</button>
        <button type="button" v-else @click="showModal_no_test" class="btn btn-secondary mx-1 small-text font-size-table">{{$t('message.test')}}</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
            <div class="row justify-content-center p-4">
                <b>{{$t('message.send_test_email')}}</b>
            </div>
            <div class="row justify-content-center p-4">
                <button class="btn rounded-green m-1" @click="sendMessage">{{$t('message.send')}}</button>
                <button @click="handleOk" class="btn rounded-red m-1">{{$t('message.cancel')}}</button>
            </div>
        </a-modal>
        <a-modal :closable="false" :centered="true" v-model="visible_no_test" :width="450" :footer="null">
            <div class="row justify-content-center p-4">
                <b>{{$t('message.after_save')}}</b>
            </div>
            <div class="row justify-content-center p-4">
                <button @click="handleOk_no_test" class="btn rounded-red m-1">{{$t('message.cancel')}}</button>
            </div>
        </a-modal>
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                  send_test_email: 'Are you sure you want to send it to the test user?',
                  send: 'Send', //Execute
                  cancel: 'Cancel', //Return to previous window
                  test: 'Test',
                  after_save :'Can be tested after saving',
                  something_wrong: 'Something went wrong',
                  message_sent: 'A message was sent to the test user.',

                } 
            },
            ja: {
                message: { 
                  send_test_email: 'テストユーザーに送信してもよろしいですか？',
                  send: '送る', //Execute
                  cancel: 'キャンセル', //Return to previous window
                  test: 'テスト', //Send test
                  after_save :'画面下部の登録ボタンを押した後、テスト可能となります。',
                  something_wrong: '送信に失敗しました。',
                  message_sent: 'テストユーザーに送信しました。',
                } 
            }
        }
    },
    props: ['data'],
    data() {
      return {
        visible: false,
        visible_no_test: false
      }
    },
    methods: {
      openNotificationWithIcon (type, message, desc) {
        this.$notification[type]({
          message: message,
          description: desc,
        });
      },
      sendMessage() {
        console.log('sending..')
        var self = this
        axios.post("sendtestmsg",{
          message: self.data,
          type: 'scenario'
        })
        .then(response => {
            this.openNotificationWithIcon('success',this.$t('message.message_sent'));
            console.log('sent')
        })
        .catch(error => {
            let status = error.response.status
            if (status === 401) {
                this.openNotificationWithIcon('error', '有効なチャネルアクセストークンが指定されていません。')
            } else if (status === 400) {
                this.openNotificationWithIcon('error', 'LINEへの送信データに問題があります。');
            } else if (status === 429) {
                this.openNotificationWithIcon('error', 'LINE APIコールのレート制限を釣果しました');
            } else {
                this.openNotificationWithIcon('error',this.$t('message.something_wrong'));
            }
        })
        this.visible = false
      },
      showModal() {
        this.visible = true
      },
      showModal_no_test() {
        this.visible_no_test = true
      },
      handleOk(e) {
        // console.log(e);
        this.visible = false
      },
      handleOk_no_test(e) {
        // console.log(e);
        this.visible_no_test = false
      },
    }
});