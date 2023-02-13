Vue.component ('confirmation-close', {
    template: 
    `<div>
    <a-modal :closable="false" :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center p-2">
            <b>{{$t('message.close_without_saving')}}</b>
            <b>{{$t('message.contents_will_be_deleted')}}</b>
        </div>
        <div class="row justify-content-center p-4">
            <button @click="handleOk" class="btn rounded-white mx-1">{{$t('message.back_to_edit')}}</button>
            <button class="btn rounded-green mx-1">{{$t('message.save_as_draft')}}</button>
            <button @click="handleOk" class="btn rounded-red mx-1">{{$t('message.delete_created_content')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                  close_without_saving: 'Do you want to close without saving?',
                  contents_will_be_deleted: 'The content created up to here will be deleted.',
                  save_draft: 'Save as draft',
                  delete_created_content: 'Close without saving', //Close without saving
                  back_to_edit: 'Return to previous window', //Return to previous window
                } 
            },
            jp: { 
                message: { 
                  close_without_saving: '保存せずに閉じますか?',
                  contents_will_be_deleted: 'ここまで作成した内容が削除されます',
                  save_as_draft: '下書きを保存',
                  delete_created_content: '作成内容を削除', //Close without saving
                  back_to_edit: '編集に戻る', //Return to previous window
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