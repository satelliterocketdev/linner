Vue.component ('confirmation-delete', {
    template: 
    `<div>
    <button type="button" @click="showModal" :class="btnClass" :disabled="!!disabled">{{$t('message.delete')}}</button>
    <a-modal :closable="false" :centered="true" v-model="visible" @ok="handleOk" :width="400" :footer="null" :destroyOnClose="true">
        <div class="row justify-content-center p-2">
            <b>{{$t('message.delete_this')}}</b>
        </div>
        <div class="row justify-content-center p-4">
            <button type="button" @click="handleOk" class="btn rounded-white m-1">{{$t('message.back_to_edit')}}</button>
            <button type="button" @click="confirmDelete" class="btn rounded-red m-1">{{$t('message.yes')}}</button>
        </div>
    </a-modal>
    </div>`,
    props: ['data', 'id', 'reloadAccount', 'btnClass', 'disabled'],
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                  delete_this: 'Do you really want to delete this?',
                  back_to_edit: 'Return to previous window', 
                  delete: 'Delete', 
                  yes: 'Delete',
                } 
            },
            ja: {
                message: { 
                  delete_this: '本当にこれを削除しますかn？',
                  back_to_edit: '編集に戻る',
                  delete: ' 削除', 
                  yes: ' 削除する',
                } 
            }
        }
    },
    data() {
        return {
          visible: false,
          message: [],
        }
      },
    methods: {
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },

        confirmDelete(){

          if (this.id && data) {
            this.deleteMessage(data)
          }
          //　gridview-line-accounts.jsへ戻る
          this.$emit('delete-account');
          this.visible = false
          
        },
    }
});