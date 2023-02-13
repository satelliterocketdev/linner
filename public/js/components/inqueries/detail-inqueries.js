Vue.component('detail-inqueries', {
  template: 
  `<div>
  <button @click="showModal" v-bind:class="buttonclass" class="font-size-table">{{$t('message.detail')}}</button>
  <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null">
      <h3>{{$t("message.contact_details")}}</h3>
      <hr>
      <div class="col-sm align-items-center">
          <u>{{$t("message.contents_of_question")}}</u>
          </br>
          <div class="row-8"><b>{{data.body}}</b></div>
      </div>
      <hr>
      <div class="col-sm align-items-center">
          <u>{{$t("message.answer")}}</u>
          </br>
          <div class="row-8"><b>{{data.answer}}</b></div>
      </div>
  </a-modal>
  </div>`,
  i18n: {
    messages: {
      en: {
        message: {
          detail: 'detail',
          contact_details: 'contact details',
          contents_of_question: 'contents of question',
          answer: 'senanswerd',
        }
      },
      ja: {
        message: {
          detail: '詳細',
          contact_details: 'お問い合わせ詳細',
          contents_of_question: '質問内容',
          answer: '回答',
        }
      }
    }
  },
  props: ['data', 'btnclass', 'reloadInqueries', 'type'],
  data() {
    return {
      visible: false,
      buttonclass: "btn mx-1 " + this.btnclass,
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
