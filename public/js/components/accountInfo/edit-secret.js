Vue.component('edit-secret', {
  template: 
      `<div class="text-center">
      <button @click="showModal" class="btn btn-outline-dark py-1 px-2">{{$t('message.ok')}}</button>
      <a-modal :centered="true" v-model="visible" :confirmLoading="confirmLoading" :closable="closable" @ok="onUpdate" :okText="$t('message.edit')" :maskClosable="true" :cancelButtonProps="{ props: { disabled: confirmLoading }}" :width="700" :destroyOnClose="false">
        <div class="text-center" style="font-size: 24px">
            <b>{{$t("message.title")}}</b>
        </div>
        <div>
            <input name="channel_secret" class="form-control" type="text" v-model="secret">
            <div class="text-danger" v-if="error">
            {{ errMsg }}
            </div>
        </div>
      </a-modal>
      </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    ok: 'OK',
                    edit: 'Edit',
                    title: 'Change Channel Secret',
                    error: 'A system error has occurred'
                }
            },
            ja: {
                message: {
                    ok: 'OK',
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
    props: ['data', 'loadingCount'],
    data() {
        return {
            visible: false,
            confirmLoading: false,
            closable: true,
            error: false,
            errMsg: "",
            secret: this.data
        }
    },
    methods: {
        showModal() {
            this.$emit('closeModal')

            this.visible = true
            this.confirmLoading = false
            this.closable = true 
            this.error = false
        },
        handleOk(e) {
            this.visible = false
        },
        onUpdate: function() {
            this.confirmLoading = true
            this.closable = false
            this.$emit('input', this.loadingCount + 1)
            axios.post('accountinfo/edit/secret', {
                channel_secret: this.secret,
            })
            .then( (res) => {                
                location.reload()
            })
            .catch(res => {
                if (res.response.status === 400) {
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