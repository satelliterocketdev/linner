@extends('layouts.app')
@section('content')
  <div id="accountinfo" v-cloak>
    <loading :visible="loadingCount > 0"></loading>
    <div class="card mb-3">
      <div class="card-body row">
        <div class="col-7 col-md-6">
            <h2>@{{ $t('message.account_manage') }}</h2>
        <template v-if="isAdmin">
            @{{ $t('message.plan') }}
            <div class="plan-box px-xs-2 py-xs-1 ml-md-1 mb-1">{{ $plan }}</div>
            <div class="upgradecolor ml-md-1"><button class="btn btn-primary btn-sm px-2 py-1" style="padding-right: 3px; padding-left: 3px;"><a href="/plan">@{{$t('message.upgrade')}}</a></button></div>
        </template>
        </div>
        <div class="col-5 col-md-6 text-right">
          <div style="display: inline">
            <a-badge :count="unreadNotifications.length" class="mb-1" />
            <popup-notifications :notifications="notifications"></popup-notifications>
          </div>
          <button class="btn btn-danger ml-md-3 px-md-3" style="height: 40px;" onclick="location.href='{{ route('logout') }}'">@{{ $t('message.logout') }}</button>
        </div>
      </div>
    </div>
    <!-- account info start -->
    <div class="card mb-3">
      <div class="card-body row">
        <div class="cal-12 col-sm-4 col-md-3 text-center">
            <div class="image-box">
                <input type="file" id="upload_img" name="upload_img" style="display:none" @change="changeImage" />
                @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
                    <a id="upload_img_button" @click="clickUploadImage">
                @endcan
                    @if(!isset($account->profile_image))
                        <img src="img/user-admin.png" class="w-auto h-100">
                    @else
                        <img src="{{ $account->profile_image }}" class="w-auto h-100">
                    @endif
                @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
                    </a>
                @endcan
            </div>
          <div class="mt-3">@{{ $t('message.friend_count') }}</div>
          <div class="font-size-big">@{{ $t('message.usercount', [ninzu]) }}</div>
          @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
          <div><a href="{{ route('password') }}"><p style="color: #007bff;">@{{ $t('message.change_password') }}</p></a></div>
          @endcan
        </div>
        <div class="col-12 col-sm-8 col-md-5">
          <div class="row">
            <div class="col">
              <h3>@{{ $t('message.account_info') }}</h3>
            </div>
          </div>
          {{-- アカウント名 --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.account_name') }}
            </div>
            <div class="col-7">
              @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
              <input type="text" v-model:value="name" v-on:blur="updateName" class="form-control">
              <div class="text-danger" v-if="name_error">
                  @{{ $t('message.error') }}
              </div>
              @else
              @{{ name }}
              @endcan
            </div>
          </div>
          {{-- ベーシックID --}}
          <div class="row mb-2">
            <div class="col-5">
                @{{ $t('message.line_id') }}
            </div>
            <div class="col-7">
              {{$account->basic_id}}
            </div>
          </div>
          {{-- チャンネルID --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.channel_id') }}
            </div>
            <div class="col-7">
              {{$account->channel_id}}
            </div>
          </div>
          {{-- secret ID --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.channel_secret') }}
            </div>
            <div class="col-7">
                {{$account->channel_secret}}
                @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
                <warning-secret :secret="secret" v-model:loading-count="loadingCount"></warning-secret>
                @endcan
            </div>
          </div>
          {{-- アクセストークン --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.Access_token') }}
            </div>
            <div class="col-7">
                {{$account->channel_access_token}}
                @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
                <warning-accesstoken :access-token="accessToken" v-model:loading-count="loadingCount"></warning-accesstoken>
                @endcan
            </div>
          </div>
          {{-- webhook url --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.webhook_url') }}
            </div>
            <div class="col-7">
              <input type="text" class="form-control" value="{{  url('/line/bot/callback/') .'/' . $account->webhook_token }}" readonly="readonly" ref="webhookToken" @click="$refs.webhookToken.select()">
            </div>
          </div>
          {{-- 友達追加URL --}}
          <div class="row mb-2">
            <div class="col-5">
              @{{ $t('message.line_link') }}
            </div>
            <div v-if="isAdmin" class="col-7">
              @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
              <input type="text" name="line_follow_link"  v-model:value="lineFollowLink" v-on:blur="updateLineFollowLink" class="form-control">
              <div class="text-danger" v-if="followlink_error">
                  @{{ $t('message.error') }}
              </div>
              @else
              @{{ lineFollowLink }}
              @endcan
            </div>
            <div v-else class="col-7">
              {{$account->line_follow_link}}
            </div>
          </div>
        </div>
        {{-- アカウント一覧・追加 --}}
        <div id="accountlist" class="col-12 col-md-4 pt-3 pt-md-0">
          <div class="row" style="height: calc(100% - 80px); overflow-y: scroll; max-height: 400px;">
            <?php $index=0; ?>
            @foreach ($accounts as $account_data)
                @if ($account_data->id != $account->id)
                    <?php $index++; ?>
                    @if ($index < $account_count_denom)
                    <div class="col-6 ps-1 text-center">
                            <a href="/changeaccount/{{$account_data->id}}">
                                <div class="image-box">
                                @if(!isset($account_data->profile_image))
                                    <img src="img/user-admin.png" class="w-auto h-100">
                                @else
                                    <img src="{{ $account_data->profile_image }}" class="w-auto h-100">
                                @endif
                                </div>
                                <p style="line-height:1.2" class="mb-0">
                                    @if (!is_null($account_data->name))
                                        {{$account_data->name}}
                                    @else
                                        &nbsp;
                                    @endif
                                </p>
                                <p style="line-height:1.2">{{$account_data->basic_id}}</p>
                            </a>
                        </div>
                    @else
                        <div class="col-6 ps-1 text-center">
                            <div class="image-box">
                                @if(!isset($account_data->profile_image))
                                    <img src="img/user-admin.png" class="w-auto h-100">
                                @else
                                    <img src="upload/{{ $account_data->profile_image }}" class="w-auto h-100">
                                @endif
                            </div>
                            <p class="mb-0" style="color: #FF7474">{{$account_data->name}}</p>
                            <p style="color: #FF7474">{{$account_data->basic_id}}</p>
                        </div>
                    @endif
                @endif
            @endforeach
          </div>
          <div style="height: 70px; margin-top: 10px;">
            <div class="text-center">
              @if($account_count_denom > $account_count_num)
              <account-add-to v-bind:btnclass="RoundedDark" :data="data" v-model:loading-count="loadingCount"> </account-add-to>
              @endif
              <!-- @if (session('new_webhook_token'))
                <p>トークン：{{ session('new_webhook_token') }}</p>
                <br>
              @endif -->
              <p class="mt-1 mb-0">@{{$t('message.registered_account')}} <strong @if ($account_count_num > $account_count_denom) style="color: #FF7474" @endif >{{$account_count_num}} / {{$account_count_denom}}</strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- account info end -->

    <div class="bg-white rounded py-3">
      <div class="d-flex align-items-center justify-content-between px-3 mb-3">
        <h3>@{{ $t('message.users_info') }}</h3>
        <div>
          @can(App\Role::ROLE_ACCOUNT_ADMINISTRATOR)
            <new-user v-bind:btnclass="RoundedDark" :reload-user="reloadUser" v-model:loading-count="loadingCount"> </new-user>
          @endcan
        </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="col-2 text-center">@{{ $t('message.name') }}</div>
{{--            <div class="col-3 text-center">@{{ $t('message.loginID') }}</div>--}}
            <div class="col-4 text-center">@{{ $t('message.email') }}</div>
            <div class="col-4 text-center">@{{ $t('message.authority') }}</div>
            <div class="col-2 text-center">&nbsp;</div>
        </div>
      </div>
      
      <div class="py-1" v-for="(data,key) in filterData" :key="key">
        <listview-user :data="data" :reload-user="reloadUser" :is-admin="isAdmin" :user-id="userId" v-model:loading-count="loadingCount"></listview-user>
      </div>
    </div>
  
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/vue2-filters/dist/vue2-filters.min.js"></script>
<script src="{{asset('js/components/notifications/popup-notifications.js')}}"></script>
<script src="{{asset('js/components/accountInfo/new-user.js')}}"></script>
<script src="{{asset('js/components/accountInfo/edit-user.js')}}"></script>
<script src="{{asset('js/components/accountInfo/edit-access-token.js')}}"></script>
<script src="{{asset('js/components/accountInfo/edit-secret.js')}}"></script>
<script src="{{asset('js/components/accountInfo/listview-user.js')}}"></script>
<script src="{{asset('js/components/accountInfo/confirmation-delete.js')}}"></script>
<script src="{{asset('js/components/accountInfo/account-add-to.js')}}"></script>
<script src="{{asset('js/components/accountInfo/warning-secret.js')}}"></script>
<script src="{{asset('js/components/accountInfo/warning-accesstoken.js')}}"></script>
<script src="{{asset('js/components/notifications/modal.js')}}"></script>
<script>
Vue.config.devtools = true

const messages = {
    en: {
        message: {
            account_manage: 'Account  Management',
            account_info: 'Account Information',
            users_info: 'Users Information',
            logout: 'Logout',
            change_image: 'Change your profile image',
            friend_count: 'Number of active friends',
            change_password: 'Change Your Password',
            account_name: 'Account Name(changeable)',
            line_id: 'Line@ ID',
            channel_id: 'Channel ID',
            channel_secret: 'Channel Secret',
            hidden: 'non-display',
            Access_token: 'Access Token',
            webhook_url: 'Webhook URL',
            line_link: 'Link to line follow',
            name: 'name',
            loginID: 'loginID',
            email: 'email',
            plan: 'Plan',
            upgrade: 'upgrade',
            authority: 'authority',
            usercount: '{0}person',
            registered_account: 'registered account ',
            error: 'A system error has occurred',
        }
    },
    ja: {
        message: {
            account_manage: 'アカウント管理',
            account_info: 'アカウント情報',
            users_info: 'ユーザー情報',
            logout: 'ログアウト',
            change_image: 'プロフィール画像を変更',
            friend_count: '有効フレンド数',
            change_password: 'パスワード変更',
            account_name: 'アカウント名(変更可)',
            line_id: 'Line@ ID',
            channel_id: 'Channel ID',
            channel_secret: 'Channel Secret',
            hidden: '非表示',
            Access_token: 'アクセストークン',
            webhook_url: 'Webhook URL',
            line_link: 'LINEフォローリンク',
            name: '名前',
            loginID: 'ログインID',
            email: 'メールアドレス',
            plan: '登録プラン',
            upgrade: 'アップグレード',
            authority: '権限',
            usercount: '{0}人',
            registered_account: '登録アカウント ',
            error: 'システムエラーが発生しました',
        }
    }
}
// Create VueI18n instance with options
const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', // locale form config/app.php
    messages, // set locale messages
})

var account_Info = new Vue({
    i18n,
    el:"#accountinfo",
    data:{
        loadingCount: 0,

        // お知らせ
        notifications: [],
        unreadNotifications: [],
        disable:true,
        visible: false,

        name: '{{$account->name}}',
        lineFollowLink: '{{$account->line_follow_link}}',

        // 新規登録ボタン
        RoundedDark: "btn-outline-dark",

        // ユーザー一覧
        data: [],
        filterData: [],
        currentFilter: 'all',
        disableDelete: true,
        userId: {{ $user_id }},
        accessToken: '{{ $account->channel_access_token }}',
        secret: '{{ $account->channel_secret }}',

        // 権限
        isAdmin: {{ var_export($isAdmin) }},
        ninzu: '{{ $follower_count  }}',

        // 登録アカウント数
        account_count_num: '{{ $account_count_num }}',
        // 登録アカウント母数（プランごと）
        account_count_denom: '{{ $account_count_denom }}',

        // 入力項目変更エラー用
        name_error: false,
        followlink_error: false,

        // プロフィール画像
        file_info: '',
    },
    filters: {
        shortenString: function(value) {
            return 'asdasd';
        }
    },
    methods:{
        openNotificationWithIcon (type, message, desc) {
            this.$notification[type]({
                message: message,
                description: desc,
            });
        },
        enableEdit(){
            this.disable = !this.disable
        },
        reloadNotifications() {
            // お知らせ一覧を取得する
            this.loadingCount++
            axios.get('notifications/lists')
            .then(res => {
                var allNotifications = res.data;
                this.unreadNotifications = allNotifications.filter( function(notification) {
                    return notification.is_read == 0
                })
                this.notifications = allNotifications.slice(0, 5)
                for (let i = 0; i < this.notifications.length; i++) {
                    let created_at = new Date(this.notifications[i].created_at);
                    let updated_at = new Date(this.notifications[i].updated_at);
                    this.notifications[i].created_at = created_at.getFullYear() + "/" + (created_at.getMonth() + 1) + "/" + created_at.getDate()
                }
            })
            .finally(() => this.loadingCount--)
        },
        handleOk(e) {
            this.visible = false
        },
        reloadUser() {
            this.loadingCount++
            axios.get("accountinfo/list")
            .then(response => {
                this.data = response.data
                this.filterData = this.data
                console.log(response.data)
            })
            .finally(() => this.loadingCount--)
        },
        updateName(e) {
            this.loadingCount++
            axios.post("accountinfo/edit/name", { name: this.name})
            .catch(res => {
                // 想定外のエラー
                this.name_error = true
                console.log(res)
                return
            })
            .finally(() => this.loadingCount--)
        },
        updateLineFollowLink(e) {
            this.loadingCount++
            axios.post("accountinfo/edit/followlink", { line_follow_link: this.lineFollowLink})
            .catch(res => {
                // 想定外のエラー
                this.followlink_error = true
                console.log(res)
                return
            })
            .finally(() => this.loadingCount--)
        },
        clickUploadImage() {
            $('#upload_img').click()
            return false
        },
        changeImage(event) {
            this.file_info = event.target.files[0]
            console.log(event)
            console.log(this.file_info)

            const formData = new FormData()
            formData.append('file', this.file_info)

            this.loadingCount++
            axios.post("accountinfo/edit/image", formData)
            .then( res => {
                location.reload()
            })
            .catch(res => {
                if (res.response.status === 400) {
                    alert(res.response.data)
                    console.log(res)
                    return
                }

                // 想定外のエラー
                alert(this.$t('message.error'))
                console.log(res)
                return
            })
            .finally(() => this.loadingCount--);
        }
    },
    beforeMount: function() {
        // call axios
        this.reloadNotifications();
        this.reloadUser()
    }
});
</script>
@endsection
@section('css-styles')
    <style>
        #accountinfo .image-box {
            overflow: hidden;
        }
        .upgradecolor {
            display: inline-block;            
        }

        .upgradecolor .btn-primary {
            display: inline-block;
            text-align: center;
            padding: 10px;
            background: linear-gradient(60deg, #ff60dd 0%, #aa40ff 100%);
            box-shadow: 1px 1px 4px rgba(0,0,0,.3);
            transition: .4s;
            border: 0;
            font-size: 0.7rem;
        }

        #accountlist .image-box {
            overflow: hidden;
        }

        #accountlist .image-box img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
        }

        .ant-popover-message-title {
            padding-left: 0;
        }

        @media (max-width: 576px) {
            .ant-popover-buttons .ant-btn-sm {
                padding: 0 7px !important;
            }
        }
    </style>

@endsection


