Vue.component('generate-receipt', {
  template:
  `<div>
      <button :disabled="data.printed == 1" @click="showModal" v-bind:class="buttonclass" class="font-size-table">{{$t('message.generate')}}</button>
      <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null">
        <h3 class="text-center">{{$t('message.generate')}}</h3>
        <form id="receiptGenerator" v-on:submit.prevent>
          <div class="row">
            <label>{{$t("message.address_input")}}</label>
            <input name="address" type="text" class="form-control" v-model="address">
          </div>
          <hr>
          <div class="row align-items-center justify-content-center p-2">
            <button type="submit" class="btn btn-info m-1" @click="download">{{ $t('message.download') }}</button>
          </div>
        </form>
      </a-modal>
  </div>`,
  i18n: {
    messages: {
      en: {
        message: {
          generate: 'Generate Receipt',
          address_input: 'Address Input',
          download: 'Download'
        }
      },
      ja: {
        message: {
          generate: '領収書発行',
          address_input: '宛名入力',
          download: 'ダウンロード'
        }
      }
    }
  },
  props: ['data', 'btnclass', 'reloadSettlement', 'type'],
  data() {
      return {
          visible: false,
          buttonclass: "btn mx-1 " + this.btnclass,
          address: ''
      }
  },
  methods: {
    showModal() {
      self = this
      this.$confirm({
        title: '注意',
        content: '領収書の発行は一度のみ可能です。発行してもよろしいですか？',
        onOk() {
          self.visible = true
        },
        onCancel() {}
      });
    },
    handleOk(e) {
      this.visible = false
    },
    download() {
      form = $("#receiptGenerator")

      form.validate({
        rules: {
          address: "required"
        }
      })
      
      if (!form.valid()) {
        return
      }

      this.reloadSettlement()
      this.handleOk()

      window.location.href = '/settlement/pdf/' + this.data.id + '/' + this.address;
    }
  },
});

