Vue.component('popup-notifications', {
  template:`
    <div>
      <a-popconfirm icon=" " placement="bottom" @confirm="allNotificationScreenTransition">
        <template slot="title">
          <div v-if="notifications.length === 0">{{$t('message.no_notification')}}</div>
          <div v-if="notifications.length !== 0">
            <div v-for="notification in notifications">
              <div style="width: 100%; max-width: 350px;" class="row mx-0 no-gutters wordbreak-all not-item link-pointer" v-on:click.capture="selectNotification(notification)">
                <div class="col-1">
                  <a-icon v-show="notification.is_read == 0" type="info-circle" style="color: red;" />
                </div>
                <div class="col-11 pl-2">
                  {{ notification.title }}
                  <br>
                  {{ notification.created_at }}
                </div>
              </div>
              <a-divider class="m-2" />
            </div>
          </div>
        </template>
        <template slot="okText">{{ $t('message.view_all') }}</template>
        <template slot="cancelText">{{ $t('message.close') }}</template>
        <a-button class="btn btn-outline-dark px-4" style="height: 40px">{{ $t('message.information') }}</a-button>
      </a-popconfirm>
    </div>`,
  i18n: {
    messages: {
      en: {
        message: {
          information: 'Information',
          no_notification: 'There is no notification',
          view_all: 'View all',
          close: 'Close',
        }
      },
      ja: {
        message: {
          information: 'お知らせ',
          no_notification: 'お知らせはありません。',
          view_all: 'すべて見る',
          close: '閉じる',
        }
      }
    }
  },
  props: ['notifications', 'reloadNotifications', 'showModal'],
  data() {
    return {}
  },
  methods: {
    selectNotification(notification) {
      location.href = "/notifications/" + notification.id;
    },
    allNotificationScreenTransition() {
        window.location.href = '/notifications';
    }
  }
})