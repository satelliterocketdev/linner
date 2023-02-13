Vue.component ('new-account', {
    template:
    `<div>
        <button @click="showModal" v-bind:class="buttonclass">{{$t('message.new')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
        
            <!-- カルーセル start -->
            <carousel v-bind:per-page=1 class="carousel">
              <template v-for="n of 10" :key="n">
                <slide><img :src="'img/line_accounts/flow_'+ n +'.jpg'" /></slide>
              </template>
            </carousel>
            <!-- カルーセル end -->

            <p></p>

            <form id="AccountsForm" method="post" v-on:submit.prevent>
                <div class="text-center" style="font-size: 24px">
                </div>
                <div class="mb-2">
                    <div>
                       <label for="basic_id" class="form-control-label">{{$t("message.line_id")}}</label>
                       <input id="basic_id" name="basic_id" class="form-control" type="text" v-model="basic_id">
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
                <a-radio-group v-model="plan">
                    <span v-for="(value, key) in plans">
                        <a-radio :value="key">{{value}}</a-radio>
                    </span>
                </a-radio-group>
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
              title: "New Account",
              description: "Explain the LINE official account, set the messaging API from the settings, please check in LINE Developper.",
              line_id: "Line@Account ID",
              access_token: "Access Token",
              secret_token: "Secret Token",
              channel_id: "Channel ID",
              line_user_id: 'Line User ID',
              new: 'New',
              send: 'Send',
            } 
          },
          ja: {
            message: { 
              title: "新規申請",
              description: "LINE公式アカウントを解説し、設定からmessaging APIを設定し、LINE Developper内で確認してください。",
              line_id: "Line@アカウントID（BASIC ID）",
              access_token: "アクセストークン",
              secret_token: "シークレットトークン",
              channel_id: "チャンネルID",
              line_user_id: 'LINEユーザーID',
              new: '新規申請',
              send: '送信',
            }
          }
        }
      },
      props: ['btnclass', 'reloadAccount'],
      data() {
        return {
          id: 0,
          basic_id: '',
          channel_access_token: '',
          channel_secret: '',
          channel_id: '',
          account_user_id: '',
          plan: '',
          visible: false,
          buttonclass: "btn mx-1 " + this.btnclass,
          RoundedGreen: "rounded-green",
          RoundedWhite: "rounded-white",
          plans: {
              0: 'planA',
              1: 'planB',
              2: 'planC',
              3: 'planD',
              4: 'planE',
              5: 'planF'
          },
        }
      },
      methods: {
        reset() {
          this.id = 0
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
                basic_id: "required",
                channel_access_token: "required",
                channel_secret: "required",
                channel_id: { required: true, digits: true},
                account_user_id: "required",
                plan: "required"
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
            
            axios.post('line_accounts', {
                basic_id: this.basic_id,
                channel_access_token: this.channel_access_token,
                channel_secret: this.channel_secret,
                channel_id: this.channel_id,
                account_user_id: this.account_user_id,
                plan: this.plan
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
      },
      components: {
        'carousel': VueCarousel.Carousel,
        'slide': VueCarousel.Slide
      },
  });
