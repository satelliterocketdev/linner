Vue.component ('gridview-line-accounts', {
    template: 
    `<div class="card">
        <div class="card-body">
            <div class="row card-title">
                <div class="col-12 justify-content-center text-center">
                    <b>{{ account.basic_id }}</b>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5 col-sm-12 font-weight-bold">
                    {{ $t('message.plan') }}：
                </div>
                <div class="col-md-7 col-sm-12">
                    {{ account.plan_name }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5 col-sm-12 font-weight-bold">
                    {{ $t('message.financial_results') }}：
                </div>
                <div class="col-md-7 col-sm-12">
                    <!-- TODO: 決済状況 -->
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5 col-sm-12 font-weight-bold">
                    {{ $t('message.authority') }}：
                </div>
                <div class="col-md-7 col-sm-12">
                    <!-- TODO: 権限の表示について -->
                    <span v-if="user.admin">{{$t('message.administrator')}}</span>
                    <span v-else>{{ $t('message.user') }}</span>
                </div>
            </div>
            <div>
                <div class="row justify-content-end align-items-center small-text text">
                    <div class="col-sm-12 col-md-6 col-lg-3 px-1 mt-1">
                        <a :class="cardButtonClass + 'btn-primary'" href="inqueries">{{$t('message.contact')}}</a>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 px-1 mt-1">
                        <button :class="cardButtonClass + 'btn-primary'" :disabled="isSelected" @click="selectAccount(id)">{{$t('message.select')}}</button>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 px-1 mt-1">
                        <edit-account :account="account" :reload-account="reloadAccount" :btnclass="cardButtonClass + 'btn-success'"></edit-account>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 px-1 mt-1">
                        <confirmation-delete :btnClass="cardButtonClass + 'btn-danger'" @delete-account="confirmDelete"></confirmation-delete>
                    </div>
                </div>
            </div>
        </div>
    </div>`,
    i18n: {
      messages: {
        en: {
          message: {
            delete: 'Delete',
            plan: 'Plan',
            financial_results: 'Financial Results',
            authority: 'Authority',
            administrator: 'Administrator',
            user: 'Account',
            contact: 'Contact',
            admin_setting: 'Admin Settings',
            select: 'Select',
          }
        },
        ja: {
          message: {
            delete: '削除',
            plan: 'プラン',
            financial_results: '決算状況',
            authority: '権限',
            administrator: '管理者',
            user: 'ユーザー',
            contact: 'お問い合わせ',
            admin_setting: '権限設定',
            select: '選択',
          }
        }
      }
    },
    props:['account', 'reloadAccount', 'user'],
    data() {
        return {
            defaults: {},
            id: -1,
            basic_id: '',
            plan: '',
            admin: false,
            cardButtonClass: " btn mx-1 small-text w-100 ",
            BtnSuccess: "btn-success small-text my-1",
            BtnInfo: "btn-info small-text my-1",
            BootstrapRed: "btn-danger",
            isSelected: false,
        }
    },
    methods: {
        render() {
            this.id = this.account.id
            this.basic_id = this.account.basic_id
            this.plan = this.account.plan_name
            this.isSelected = this.id === this.user.account_id;
        },
        confirmDelete() {
            self = this
            
            axios.delete('line_accounts/delete/' + this.id )
            .then((response) => {
              self.reloadAccount()
            }).catch((error) => {
              console.log(error)
            })
        },
        selectAccount(id) {
            axios.put('line_accounts/select/' + id)
            .then(res => {
                this.reloadAccount()
            })
        }
    },
    filters: {
        count() {
            return 0
        }
    },
    created() {
        this.render()
    },
    updated() {
        this.render()
    }
});