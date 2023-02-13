Vue.component ('confirmation-send', {
  template: 
  `<div>
  <a-modal :closable="false" :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
      <div class="row justify-content-center p-2">
          <b>[Date 2019-04-02] [Time 12:00] {{$t('message.to')}} [number of people] {{$t('message.deliver_a_message')}}</b>
          <b>Are you really sure?</b>
      </div>
      <div class="row justify-content-center p-4">
          <button @click="handleOk" class="btn rounded-white mx-1">Return to previous window</button>
          <button @click="handleOk" class="btn rounded-red mx-1">Close without saving</button>
      </div>
  </a-modal>
  </div>`,
  i18n: { // `i18n` option, setup locale info for component
    messages: {
        en: { 
            message: { 
              back_to_edit: 'Return to previous window', //Return to previous window
              yes: 'Yes', //Execute
              are_you_sure: "Are you sure?",
              to: 'to',
              deliver_a_message: "Deliver a message to a person",
            } 
        },
        jp: { 
            message: { 
              back_to_edit: '編集に戻る', //Return to previous window
              yes: ' はい', //Execute,
              are_you_sure: "本当によろしいですか？",
              to: 'に',
              deliver_a_message: "人にメッセージを配信します。",
            } 
        }
    }
  },
  data() {
      return {
        visible: false,
      }
    },
  methods: {
      showModal() {
        this.visible = true
      },
      handleOk(e) {
        console.log(e);
        this.visible = false
      },
  }
  // <button @click="showModal" class="btn rounded-red">ConfirmationMessage_Delete</button>
});