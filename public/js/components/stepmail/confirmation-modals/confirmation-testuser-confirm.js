Vue.component ('confirmation-testuser-confirm', {
    template: 
    `<div>
    <button @click="showModal" class="btn rounded-green">Execute</button>
    <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
        <div class="row justify-content-center p-4">
            <b>{{$t('message.change_test_username')}}</b>
        </div>
        <div class="row justify-content-center">
            <!-- <i class="far fa-5x fa-user-circle"></i> -->
            <a-avatar :size="100" icon="user" />
        </div>
        <div class="row justify-content-center p-4" style="font-size: 18px">
            <b>{{ user.display_name }}</b>
        </div>
        <div class="row justify-content-center">
            {{$t('message.warning_text')}}
        </div>
        <div class="row justify-content-center p-2">
            <button @click="handleOk" class="btn rounded-white m-1">{{$t('message.return_to_change_screen')}}</button>
            <button @click="confirm" class="btn rounded-green m-1">{{$t('message.yes')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: { 
                    change_test_username: "Change test username",
                    username: "Username",
                    warning_text: "Change test user to username. Is it OK?",
                    yes: 'Yes', //Execute,
                    return_to_change_screen: 'Return'
                } 
            },
            jp: { 
                message: { 
                    change_test_username: "テストユーザー名を変更",
                    username: "ユーザー名",
                    warning_text: "テストユーザーをユーザー名に変更します。よろしいですか？",
                    yes: ' はい', //Execute,
                    return_to_change_screen: '変更画面に戻る'
                } 
            }
        }

    },
    props: ['user', 'changeUser'],
    data() {
        return {
          visible: false,
        }
      },
    methods: {
        confirm() {
            this.changeUser()
            this.visible = false
        },
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          console.log(e);
          this.visible = false
        },
    }
});
    