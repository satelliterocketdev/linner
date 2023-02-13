Vue.component('modal-notifications', {
  template:`<a-modal :footer="null" :centered="true" v-model="visible" @ok="handleOk" @cancel="handleOk" :width="700" :destroyOnClose="true">
      <template slot="title">{{$t('message.notification')}}</template>
      <div v-for="notification in data">
        <div class="row h5 mb-2">
          <div class="col-8">
            {{ notification.title }}
          </div>
          <div class="col-4 text-right">
            {{ notification.created_at }}
          </div>
        </div>
        <div class="row">
          <div class="col">
            {{notification.body}}
          </div>
        </div>
        <a-divider class="m-2" />
      </div>
    </a-modal>`,
  props: ['data', 'visible', 'handleOk'],
  i18n: {
    messages: {
      en: {
        message: {
          notification: "Notification"
        }
      },
      ja: {
        message: {
          notification: "お知らせ"
        }
      }
    }
  },
  data() {
    return {}
  },
  methods: {

  }
})