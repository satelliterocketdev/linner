@extends('layouts.app')
@section('content')
  <div id="line_accounts">
    <loading :visible="loadingCount > 0"></loading>
    <div class="bg-white border rounded p-3">
        <div class="d-flex align-items-center justify-content-between px-3 mb-3">
            <h4>@{{$t("message.line_account")}}</h4>
            <div>
              <new-account v-bind:btnclass="RoundedDark" :reload-account="reloadAccount"> </new-account>
            </div>
        </div>
    </div>
    <div class="row">  
      <div class="col-sm-4 col-md-4 p-3" v-for="(account,key) in filterData" :key="key">
          <gridview-line-accounts :reload-account="reloadAccount" :account="account" :user="user"></gridview-line-accounts>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/vue2-filters/dist/vue2-filters.min.js"></script>
<script src="{{asset('js/components/line_account/new-account.js')}}"></script>
<script src="{{asset('js/components/line_account/select-account.js')}}"></script>
<script src="{{asset('js/components/line_account/edit-account.js')}}"></script>
<script src="{{asset('js/components/line_account/gridview-line-accounts.js')}}"></script>
<script src="{{asset('js/components/line_account/confirmation-delete.js')}}"></script>
<script>
Vue.config.devtools = true

const messages = {
    en: {
        message: {
            line_account: 'Line@Account Manage',
        }
    },
    ja: {
        message: {
            line_account: 'Line@アカウント管理',
        }
    }
}

const i18n = new VueI18n({
    locale: '{{config('app.locale')}}',
    messages, 
})

var account_Info = new Vue({
    i18n,
    el:"#line_accounts",
    data:{
        loadingCount: 0,
        // 新規登録ボタン
        RoundedDark: "btn-outline-dark",

        // ユーザー一覧
        accounts: [],
        user: {},
        filterData: [],
        currentFilter: 'all',
        disableDelete: true,
    },
    filters: {
        shortenString: function(value) {
            return 'asdasd';
        }
    },
    methods:{
        reloadAccount() {
            this.loadingCount++
            axios.get("line_accounts/list")
            .then(response => {
                this.user = response.data.user
                this.accounts = response.data.accounts
                this.filterData = this.accounts
            })
            .finally(() => this.loadingCount--)
        },
    },
    beforeMount: function() {
        this.reloadAccount()
    }
});

</script>
@endsection


