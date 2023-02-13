Vue.component ('select-account', {
      template: 
      `<div>
      <button @click="showModal" v-bind:class="buttonclass">{{$t('message.select')}}</button>
      <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
        <form id="AccountsForm" method="post" v-on:submit.prevent>
          <div class="text-center" style="font-size: 24px">
              <b>{{$t("message.title")}}</b>
          </div>
          <div class="mb-2">
              <div class="mb-3">
                  <label>{{$t("message.line_id")}}</label>
                  <input name="basic_id" class="form-control" type="text" v-model="basic_id">
              </div>
              <div class="mb-3">
                  <label>{{$t("message.access_token")}}</label>
                  <input name="channel_access_token" class="form-control" type="text" v-model="channel_access_token">
              </div>
              <div class="mb-3">
                  <label>{{$t("message.secret_token")}}</label>
                  <input name="channel_secret" class="form-control" type="text" v-model="channel_secret">
              </div>
              <div class="mb-3">
                  <label>{{$t("message.channel_id")}}</label>
                  <input name="channel_id" class="form-control" type="text" v-model="channel_id">
              </div>
              <div class="mb-3">
                  <label>{{$t("message.line_user_id")}}</label>
                  <input name="account_user_id" class="form-control" type="text" v-model="account_user_id">
              </div>
          </div>
          <a-checkbox>{{$t("message.planA")}}</a-checkbox>
          <div class="footer">
              <div class="row justify-content-center">
                  <button type="button" class="btn rounded-green m-1" @click.prevent="register">{{$t('message.send')}}</button>
              </div>
          </div>
        </form>
      </a-modal>
      </div>`,
      i18n: { 
        messages: {
          en: { 
            message: { 
              title: "Edit Application",
              line_id: "Line@Account ID",
              access_token: "Access Token",
              secret_token: "Secret Token",
              channel_id: "Channel ID",
              line_user_id: 'Line User ID',
              planA: "Plan A",
              edit: 'Edit',
              send: 'Send',
            } 
          },
          ja: {
            message: { 
              title: "選択",
              line_id: "Line@アカウントID",
              access_token: "アクセストークン",
              secret_token: "シークレットトークン",
              channel_id: "チャンネルID",
              line_user_id: 'ラインユーザーID',
              planA: "プランA",
              select: '選択',
              send: '送信',
            }
          }
        }
      },
      props: ['account', 'btnclass', 'reloadAccount'],
      data() {
        return {
          sendToAll: false,
          basic_id: '',
          channel_access_token: '',
          channel_secret: '',
          channel_id: '',
          account_user_id: '',
          visible: false,
          buttonclass: "btn mx-1 " + this.btnclass,
          RoundedGreen: "rounded-green",
          RoundedWhite: "rounded-white",
        }
      },
      methods: {
        reset() {
          this.id = this.account.id
          this.basic_id = ''
          this.channel_access_token = ''
          this.channel_secret = ''
          this.channel_id = ''
          this.account_user_id = ''
        },
        showModal() {
          this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        register() {
            form = $("#AccountsForm")

            form.validate({
              rules: {
                
              }
            })
            
            if (!form.valid()) {
              return
            }
  
            if (this.id) {
              this.update()
              return
            }
            self = this
            
            axios.post('accountinfo', {
              basic_id: this.basic_id,
              channel_access_token: this.channel_access_token,
              channel_secret: this.channel_secret,
              channel_id: this.channel_id,
              account_user_id: this.account_user_id,
            })
            .then((res) => {
                self.reloadAccount()
                self.reset()
                self.visible = false
                console.log(res);
            });
          },
        addMessage(event) {
            alert(event)
        }
      }
  });
