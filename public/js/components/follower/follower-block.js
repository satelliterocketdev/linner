Vue.component('follower-block-button',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
            button_name: 'Block',
            block_confirm_message1: 'The blocked friend disappears from the friend list and no longer sends a message.',
            block_confirm_message2: 'Currently registered tags and friend information will also be deleted.',
            block_confirm: 'Are you sure you want to block?',
            confirm_no : 'No',
            confirm_yes: 'Yes',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        },
      },
      ja: {
        message: { 
            button_name: 'ブロックする',
            block_confirm_message1: 'ブロックをした友達は友達一覧から消え、\nメッセージの送信も行われなくなります。',
            block_confirm_message2: '現在登録されているタグや友達情報なども消去されます。',
            block_confirm: '本当にブロックしますか？',
            confirm_no : 'いいえ',
            confirm_yes: 'はい',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        },
      }
    }
  },
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props: {
    targets: {
      type: Array,
      required: true,
    },
    disabled: {
      type: Boolean,
    },
    loadingCount: {
        type: Number,
        default: 0
    }
  },
  data(){
    return{
      visible: false,
      loading: false,
      info: [],
      buttonName: this.$i18n.t('message.button_name')
    }
  },
  methods:{
    openNotificationWithIcon(type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    showModal() {
      this.visible = true
    },
    confirm(){
      this.loading = true
      let data = { followerIds: this.getTarget() }
      this.$emit('input', this.loadingCount + 1)
      axios.post("follower/block", data)
      .then(res=> this.done())
      .catch(e=> this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
      .finally(() => {
          this.loading = false
          this.$emit('input', this.loadingCount - 1)
      })
    },
    getTarget(){
      return this.targets.map( follower => {
        return follower.id
      })
    },
    cancel(){
      this.info = []
      this.visible = false
    },
    done(){
      this.$emit('updated')
      this.cancel()
    }
  },
  template:
  `<div>
  <button type="button" @click="showModal" class="btn rounded-red" :disabled=disabled>{{ buttonName }}</button>
  <a-modal :centered="true" v-model="visible" :max-width="450" :footer="null" :maskClosable="false" :destroyOnClose="true">
      <div id="message-content" class="p-2" style="text-align:center; ">
        <div class="m-3">
          <span class="block-message-br">{{ $t('message.block_confirm_message1') }}</span>
        </div>
        <div>
          <span>{{ $t('message.block_confirm_message2') }}</span>
        </div>
        <div>
          <span>{{ $t('message.block_confirm') }}</span>
        </div>
      </div>
      <div class="footer pt-4">
          <div class="row justify-content-center">
              <button type="button" class="btn m-2 px-5 rounded-white" @click="cancel"> {{$t('message.confirm_no')}} </button>
              <button type="button" class="btn m-2 px-5 rounded-red" @click="confirm"> {{$t('message.confirm_yes')}} </button>
          </div>
      </div>
  </a-modal>
  </div>`,
});

