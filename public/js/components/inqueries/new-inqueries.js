Vue.component('new-inqueries', {
  template: 
  `<div>
  <button @click="showModal" v-bind:class="buttonclass">{{$t('message.new_inqueries')}}</button>
  <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null">
      <h3>{{$t("message.new_inqueries")}}</h3>
      <hr>
      <form id="inqueriesForm" method="post" v-on:submit.prevent>
        <div class="fixed-container p-2 my-1">
            <b><textarea name="body" class="form-control" v-model="body" type="text" :placeholder="$t('message.contents_of_question')" style="height: 250px;"></textarea></b>
        </div>
        <div class="footer">
          <div class="row justify-content-center">
              <button type="button" class="btn btn-primary" @click="register">{{$t('message.send')}}</button>
          </div>
      </div>
    </form>
  </a-modal>
  </div>`,
  i18n: {
    messages: {
      en: {
        message: {
          new_inqueries: 'new inqueries',
          send: 'send',
          contents_of_question: 'contents of question',

        }
      },
      ja: {
        message: {
          new_inqueries: '新規お問い合わせ',
          send: '送信',
          contents_of_question: '質問内容',

        }
      }
    }
  },
  props: ['data', 'btnclass', 'reloadInqueries', 'type'],
  data() {
    return {
      visible: false,
      buttonclass: "btn mx-1 " + this.btnclass,
      body: ''
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
      form = $("#inqueriesForm")

      form.validate({
        rules: {
          body: "required"
        }
      })
      
      if (!form.valid()) {
        return
      }

      self = this
      axios.post("inqueries", {
        body: this.body
      })
      .then(function(response){
          self.reloadInqueries()
          self.visible = false
          self.body = ''
      })
    },
  },
});
