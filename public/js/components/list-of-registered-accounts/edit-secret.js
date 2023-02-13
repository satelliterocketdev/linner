Vue.component('edit-secret', {
  template: 
      `<div class="text-center">
      <button @click="showModal" class="btn btn-outline-dark py-1 px-2">{{$t('message.edit')}}</button>
      <a-modal :centered="true" v-model="visible" :confirmLoading="confirmLoading" :closable="closable" @ok="onUpdate" :okText="$t('message.edit')" :maskClosable="false" :cancelButtonProps="{ props: { disabled: confirmLoading }}" :width="700" :destroyOnClose="false">
        <form method="post" v-on:submit.prevent>
          <div class="text-center" style="font-size: 24px">
              <b>{{$t("message.title")}}</b>
          </div>
          <div>
              <input class="form-control" type="text" v-model="channelSecret">
              <div class="text-danger" v-if="error">
                {{ errMsg }}
              </div>
          </div>
        </form>
      </a-modal>
      </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    edit: 'Edit',
                    title: 'Change Channel Secret',
                    error: 'A system error has occurred'
                }
            },
            ja: {
                message: {
                    edit: '変更する',
                    title: 'Channel Secret の変更',
                    error: 'システムエラーが発生しました'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'id', 'loadingCount'],
    data() {
        return {
            visible: false,
            confirmLoading: false,
            closable: true,
            error: false,
            errMsg: "",
            channelSecret: this.data
        }
    },
    methods: {
        showModal() {
            this.visible = true
            this.confirmLoading = false
            this.closable = true
        },
        handleOk(e) {
            this.visible = false
        },
        onUpdate: function() {
            this.$emit('input', this.loadingCount + 1)
            axios.post('accountinfo/edit/secret/' + this.id, {
                channel_secret: this.channelSecret,
            })
            .then( (res) => {
                this.handleOk(null)
                this.$emit('update', this.channelSecret)
            })
            .catch(res => {
                if (res.response.status === 500) {
                    this.error = true
                    this.errMsg = res.response.data

                    this.confirmLoading = false
                    this.closable = true
                    console.log(res)
                    return
                }

                // 想定外のエラー
                this.error = true
                this.errMsg = this.$t('message.error');

                this.confirmLoading = false
                this.closable = true
                console.log(res)
                return
            })
            .finally(() => this.$emit('input', this.loadingCount - 1));
        }
    }
});