@extends('layouts.app-mother')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <tutorial></tutorial>
        <div class="bg-white rounded py-3">
            <div class="row px-3 mb-3">
                <div class="col-sm-6">
                    <h2 style="display:inline; margin-right: 10px;">@{{$t("message.list_of_registered_accounts")}}</h2>
                    <div class="plan-box px-xs-2 py-xs-1 ml-md-1 mb-2">@{{ user.planName }}</div>
                </div>
                <div class="col-sm-6 text-md-right">
                    <div class="btn-group upgradecolor">
                        <a href="/plan" class="btn btn-primary px-2">@{{$t('message.upgrade')}}</a>
                        <add-account v-if="accountLimit" :type="'header'" :reload-accounts="reloadAccounts"></add-account>
                    </div>
                </div>
            </div>
            <div class="px-3">
                <p class="mt-1 mb-0">@{{$t('message.registered_account')}} <strong v-bind:class="{　'text-danger': user.accountCount > user.addableAccountCount }">@{{ user.accountCount }} / @{{ user.addableAccountCount }}</strong></p>
            </div>
        </div>
        <div class="row mx-0">
            <div class="col-sm-6 col-md-4 mx-0 my-2 p-0 card-deck" v-for="(data,key) in filterData" :key="key">
                <gridview-accounts :reload-accounts="reloadAccounts" :data="data" v-model:loading-count="loadingCount"></gridview-accounts>
            </div>
            <div class="col-sm-6 col-md-4 mx-0 my-2 p-0 card-deck">
                <increase-accounts></increase-accounts>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<!--Main Pages-->
<script src="{{asset('js/components/list-of-registered-accounts/gridview-accounts.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/add-account.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/increase-accounts.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/detail.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/edit-access-token.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/edit-secret.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/warning-accesstoken.js')}}"></script>
<script src="{{asset('js/components/list-of-registered-accounts/warning-secret.js')}}"></script>

<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                list_of_registered_accounts: 'list of registered accounts',
                add_to: 'add to',
                registered_account: 'registered account{0}/5',
                upgrade: 'upgrade',
                increase_accounts: 'Increase the number of distribution accounts',
                update: 'Update'
            }
        },
        ja: {
            message: {
                list_of_registered_accounts: '登録アカウント一覧',
                add_to: '追加',
                registered_account: '登録アカウント ',
                upgrade: 'アップグレード',
                increase_accounts: '配信アカウント数を増やす',
                update: '更新'
            }
        }
    }
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el:"#app",
        data: {
            loadingCount: 0,
            data: [],
            accounts: {},
            user: {},
            filterData: [],
            visible: false,
            nice: 'asdasd',
            RoundedDark: "btn-outline-dark",
            BtnSuccess: "btn-success",
            accountLimit: false,
            tutorial: {{ var_export(Auth::user()->finished_tutorial) }}
        },
        beforeMount() {
            this.reloadAccounts()
        },
        methods: {
            reloadAccounts() {
                this.loadingCount++
                axios.get("accounts/list")
                .then(response => {
                    this.accounts = response.data.accounts
                    this.user = response.data.user
                    // this.accountLimit = response.data.accountLimit
                    this.prepare(response.data.user)
                    console.log(response.data)
                })
                .finally(() => this.loadingCount--)
            },
            prepare(user) {
                switch (user.addableAccountCount) {
                    case 1:
                        this.filterData = [{}]
                        break
                    case 3:
                        this.filterData = [{}, {}, {}]
                        break
                    case 5:
                        this.filterData = [{}, {}, {}, {}, {}]
                        break
                    case 10:
                        this.filterData = [{}, {}, {}, {}, {}, {}, {}, {}, {}, {}]
                        break
                    case 30:
                        this.filterData = [
                            {}, {}, {}, {}, {}, {}, {}, {}, {}, {},
                            {}, {}, {}, {}, {}, {}, {}, {}, {}, {},
                            {}, {}, {}, {}, {}, {}, {}, {}, {}, {}
                        ]
                        break;
                }
                Object.assign(this.filterData, this.accounts)
                this.accountLimit = user.canAddNewAccount
            }
        }
    })
</script>
@endsection
@section('css-styles')
    <style>
        .upgradecolor .btn-primary {
            background: linear-gradient(60deg, #ff60dd 0%, #aa40ff 100%);
            transition: .4s;
            border: none;
        }
        .expertcolor {
            padding: 3px 20px;
            background: linear-gradient(60deg, #FF0000 0%, #FF00FF 100%);
            color: #FFFFFF;
            display:inline;
        }
    </style>
@endsection