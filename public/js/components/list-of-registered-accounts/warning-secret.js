
Vue.component('warning-secret', {
  template: 
      `<div style="display: inline">
      <button @click="showModal" class="btn btn-outline-dark py-1 px-2">{{$t('message.edit')}}</button>
      <a-modal :centered="true" v-model="visible" :width="700" :footer="null">
        <h2 class="mb-5 text-danger text-center">{{$t("message.title")}}</h2>
        <p class="mb-4 text-danger text-center">{{$t("message.text1")}}</p>
        <p style="white-space: pre-wrap" class="text-danger text-center">{{$t("message.text2")}}</p>
        <edit-secret :data="secret" :id="id" @update="onUpdate" @closeModal="closeModal()" v-model:loading-count="loadingCountData"></edit-secret>
      </a-modal>
      </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    edit: 'Edit',
                    title: 'Please be careful!',
                    text1: 'If you change it, the link with LINE@ may be canceled.',
                    text2: 'If you change items with LINE @,\nIf your access token expires,\nIf you made a mistake in the original description,\nPlease change this to match LINE@.',
                }
            },            
            ja: {
                message: {
                    edit: '変更する',
                    title: 'ご注意ください！',
                    text1: '変更するとLINE@との連携が解除される恐れがあります。',
                    text2: 'LINE@で項目を変更した場合、\nアクセストークンが失効した場合、\nもともとの記載を間違えていた場合などは、\nこちらをLINE@と合わせるために変更してください。',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['secret', 'loadingCount', 'id'],
    data() {
        return {
            visible: false,
            confirmLoading: false,
            closable: true
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
        },
        closeModal() {
            this.visible = false
        },
        onUpdate(secret) {
            this.closeModal()
            this.$emit('update', secret)
        }
    },
});
