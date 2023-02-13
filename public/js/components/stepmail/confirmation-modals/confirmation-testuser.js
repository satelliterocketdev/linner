Vue.component ('confirmation-testuser', {
    template: 
    `<div>
    <button @click="showModal" class="btn m-1 rounded-white">{{ user.display_name }}</button>
    <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
        <div class="row justify-content-center p-2">
            <b>{{$t('message.test_user_change')}}</b>
        </div>
        <div class="row justify-content-center p-4">
            <b>{{$t('message.enter_test_user')}}</b>
        </div>
        <b><a-select
          style="width: 100%"
          v-model="userKey"
        >
          <a-select-option v-for="follower in followers" :key="follower.id">
            {{ follower.display_name }}
          </a-select-option>
        </a-select></b>
        <div class="row justify-content-center align-items-center p-4">
            <confirmation-testuser-confirm :user="chosenUser" :changeUser="changeUser"></confirmation-testuser-confirm>
            <button @click="handleOk" class="btn rounded-red m-1">{{$t('message.cancel')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                  done: 'Done', //ConfirmChange
                  cancel: 'Cancel',
                  test_user_change: 'Test User change',
                  enter_test_user: 'Please enter the test user username'
                } 
            },
            jp: { 
                message: { 
                  done: '完了', //ConfirmChange
                  cancel: 'キャンセル',
                  test_user_change: 'テストユーザー変更',
                  enter_test_user: 'テストユーザーのユーザー名を入力してください'
                } 
            }
        }
    },
    props: ['user', 'followers', 'confirmChangeUser'],
    data() {
        return {
          visible: false,
          userKey: this.user.id,
          chosenUser: {},
        }
      },
    methods: {
      changeUser() {
        this.confirmChangeUser(this.chosenUser)
        this.visible = false
      },
      showModal() {
        this.visible = true
      },
      handleOk(e) {
        this.visible = false
      },
    },
    watch: {
      userKey() {
        this.chosenUser = this.followers[this.userKey - 1]
      }
    }
});