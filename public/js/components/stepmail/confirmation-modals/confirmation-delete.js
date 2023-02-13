Vue.component ('confirmation-delete', {
    template: 
    `<div>
    <button type="button" :disabled="selected.length<1" @click="showModal" class="btn rounded-red">{{$t('message.delete_selected')}}</button>
    <a-modal :closable="false" :centered="true" v-model="visible" @ok="handleOk" :width="400" :footer="null" :destroyOnClose="true">
        <div class="row justify-content-center p-2">
            <b>{{$t('message.delete_this')}}</b>
        </div>
        <div class="row justify-content-center p-4">
            <button type="button" @click="handleOk" class="btn rounded-white m-1">{{$t('message.back_to_edit')}}</button>
            <button type="button" @click="confirmDelete" class="btn rounded-red m-1">{{$t('message.delete')}}</button>
        </div>
    </a-modal>
    </div>`,
    props: ['selected', 'data', 'id', 'reloadMessage'],
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                  delete_this: 'Do you really want to delete this?',
                  back_to_edit: 'Return to previous window', //Return to previous window
                  delete: 'Delete', //Execute
                  delete_selected: "Delete selected"
                } 
            },
            ja: {
                message: { 
                  delete_this: '本当にこれを削除しますか？',
                  back_to_edit: '編集に戻る', //Return to previous window
                  delete: ' 削除', //Execute,
                  delete_selected: "選択したものを削除"
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
          this.visible = false
        },
        confirmDelete() {
          self = this
          
          data = []
          $.each(self.selected.sort().reverse(), function(key, value){
            message = self.data.splice(value, 1)
            self.selected.splice(key, 1)
            message_id = message[0].id
            if (message_id != 0) {
              data.push(message_id)
            }
          })

          if (this.id && data.length > 0) {
            this.deleteMessage(data)
          }
          
          this.visible = false
        },
        deleteMessage(data) {
          self = this
          axios.delete('stepmail/message/' + data)
          .then(function(response){
            // self.reloadMessage()
            // self.selected = []
            // self.reloadScenario()
          })
        }
    }
});