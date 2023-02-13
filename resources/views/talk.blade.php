@extends('layouts.app')

@section('content')
<div id="talk" v-cloak>
    <loading :visible="loadingCount > 0"></loading>
    <div class="rounded card-like px-4 py-4">
        <div class="row">
            <div class="col-sm-7">
                <h2>@{{ $t('message.talk') }}</h2>
            </div>
            <!-- search section -->
            <div class="col-sm-5 text-left">
                <div class="d-flex ">
                    <div class="align-self-center flex-fill">
                        <a-input-search :placeholder="$t('message.search')" @search="searchFriends"></a-input-search>
                    </div>
                    <div class="align-self-center">
                        <button v-on:click="viewMode = 'list'" v-bind:class="{ 'active': viewMode == 'list' }" class="btn btn-outline-dark m-1">
                            <i class="fas fa-xs fa-list"></i>
                        </button>
                    </div>
                    <div class="align-self-center">
                        <button v-on:click="viewMode = 'chat'" v-bind:class="{ 'active': viewMode == 'chat' }" class="btn btn-outline-dark m-1">
                            <i class="fas fa-mobile-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row px-1 px-sm-3 py-3 d-flex">
            <div>
                <button class="btn rounded-cyan m-1" :disabled="!hasCheckedFollowers" @click="markUnRead">@{{ $t('message.unreaded') }}</button>
                <button class="btn rounded-green m-1" :disabled="!hasCheckedFollowers" @click="markRead">@{{ $t('message.readed') }}</button>
                <a-dropdown>
                    <a-menu slot="overlay" @click="markSupported">
                        <a-menu-item key="none">@{{ $t('message.mark_none') }}</a-menu-item>
                        <a-menu-item key="required">@{{ $t('message.mark_required') }}</a-menu-item>
                    </a-menu>
                    <button class="btn rounded-grey m-1" :disabled="!hasCheckedFollowers">@{{ $t('message.mark') }}</button>
                </a-dropdown>

                <button class="btn rounded-red m-1" :disabled="!hasCheckedFollowers" @click="messageDelete">@{{ $t('message.delete') }}</button>
            </div>
        </div>
        <!-- table-grid-header -->
        <grid-header-general ref="headerGeneral" @all-check-changed="onCheckAllChange" @item-sort="itemSort"></grid-header-general>
    </div>
    <!-- table-grid -->
    <div v-show="viewMode == 'list'">
        <user-info ref="userInfo" v-model:loading-count="loadingCount"></user-info>
        <div class="mt-2 card-like px-3 py-2" v-for="(follower,key) in processedRecords" :key="key">
            <div class="row font-size-table wordbreak-all" style="align-items: center;">
                <div class="col-1 text-center px-small">
                    <a-checkbox @change.stop="checkChanged" :value="follower" :checked="follower.checked"></a-checkbox>
                </div>
                <div class="col-2 px-small">
                    <span v-if="follower.status == 0">@{{ $t('message.mark_none') }}</span>
                    <span v-if="follower.status == 1">@{{ $t('message.mark_required') }}</span>
                </div>
                <div class="col-3 d-flex px-small">
                    <div class="flex-fill">
                        <a-avatar size="large" style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="follower.pf_user_picture==null"></a-avatar>
                        <a-avatar size="large" :src="follower.pf_user_picture" v-if="follower.pf_user_picture!=null"></a-avatar>
                        <a style="color: #25A5D0;" @click.prevent="showUserInfo(follower)"><span>@{{ follower.pf_user_display_name }}</span></a>
                    </div>
                    <div style="align-self: center">
                        <span class="new-message-mark" :class="{ 'active': follower.message_status == '0' }">●</span>
                    </div>
                </div>
                <div class="col-4 px-small border-left border-right d-flex align-items-center justify-content-between">
                    <span v-html="follower.latest_message" style="max-height:100px; overflow:hidden;"></span>
                    <button class="btn mx-1 btn-info small-text font-size-table" @click="showTalk(follower)">@{{ $t('message.view') }}</button>
                </div>
                <div class="col-2 px-small text-center d-flex flex-column">
                    <span>@{{ follower.latest_message_date }}</span>
                    <span>@{{ follower.latest_message_time }}</span>
                </div>
            </div>
        </div>
    </div>
    <div v-show="viewMode == 'chat'">
        <div class="chat-view d-flex no-gutters bg-white mt-3">
            <!-- 左ペイン -->
            <div class="d-flex flex-column col-4 border-left border-top border-right ">
                <div class="flex-fill" style="overflow:hidden">
                    <div class="chat-list h-100">
                        <div class="d-flex flex-column flex-fill wordbreak-all">
                            <div v-for="(follower,key) in processedRecords" :key="key" @click="changeSelectedFollower(follower)" class="d-flex align-items-center border-bottom px-1 py-1 py-sm-2 chat-item wordbreak-all" :class="{ active: follower === selectedFollower }">
                                <div class="col-2 flex-column text-center p-0 mr-1">
                                    {{--changeSelectedFollower のclick発火を防ぐため click.stopを設置（functionは空） --}}
                                    <div @click.stop="">
                                        <a-checkbox @change="checkChanged" :value="follower" :checked="follower.checked"></a-checkbox>
                                    </div>
                                </div>
                                <div class="col-3 text-center p-0 mr-1">
                                    <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink); width: 100%; height: auto; max-width: 32px;" icon="user" v-if="follower.pf_user_picture==null"></a-avatar>
                                    <a-avatar :src="follower.pf_user_picture" v-if="follower.pf_user_picture!=null" style="width: 100%; height: auto; max-width: 32px;"></a-avatar>
                                </div>
                                <div class="col-7 px-0">
                                    <div class="font-size-table">@{{ follower.pf_user_display_name }}</div>
                                    <div class="small font-size-table sp-min">
                                        <span>@{{ follower.latest_message_date }}</span> <span>@{{ follower.latest_message_time }}</span>
                                    </div>
                                    <br class="pc">
                                    <div class="sp-min">
                                        <span v-if="follower.status == 0" class="small">@{{ $t('message.mark_none') }}</span>
                                        <span v-if="follower.status == 1" class="small">@{{ $t('message.mark_required') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div @mousedown="startScrollChatList" @mouseup="stopScrollChatList" @mouseleave="stopScrollChatList" class="pb-2 pt-0 border-top border-bottom text-center" style="cursor: pointer;">
                    <i class="fas fa-sort-down fa-2x"></i>
                </div>
            </div>
            <!-- チャット領域 -->
            {{-- :keyにdata.idを指定しているのは、talk-viewを再利用させず、毎回生成させるためのトリック。 --}}
            <div class="col-8 border">
                <div v-for="(data, index) in chatRequests" :key="index">
                    <talk-view v-if="data !== undefined" :follower="data" v-model:loading-count="loadingCount"></talk-view>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/talk/previously-uploaded.js')}}"></script>
<script src="{{asset('js/components/talk/addcontent-image.js')}}"></script>
<script src="{{asset('js/components/talk/addcontent-video.js')}}"></script>
<script src="{{asset('js/components/talk/addcontent-audio.js')}}"></script>
<script src="{{asset('js/components/talk/addcontent-other.js')}}"></script>
<script src="{{asset('js/components/follower/follower-block.js') }}"></script>
<script src="{{asset('js/components/followers/userinfo.js')}}"></script>

<script>
    const messages = {
    en: {
      message: {
        talk: 'Talk',
        search: 'Search',
        unreaded: "Unreaded",
        readed: "Readed",
        mark: "Mark",
        mark_none: "None",
        mark_required: "Required",
        delete: "Delete",
        status: "Status",
        username: "Username",
        date: "Date",
        message_content: "Message Content",
        search_option: "Search Option",
        view: "View",
        images: "Images",
        videos: "Videos",
        audio: "Audio",
        map: "Map",
        template: "Template",
        survey: "Survey",
        file: "File",
        mute: "Mute",
        send: "Send",
        friends: "Friends",
        source: "Source",
        tags: "Tags",
        notes: "Notes",
        user_journey: "User Journey",
        rich_menu: "Rich Menu",
        survei: "Survey",
        delivery_status: "Delivery Status",
        add_option: "Add Option",
        relativedate_today: "Today",
        relativedate_yesterday: "Yesterday",
        user_info: "User Info",
        getting_data: "Getting Data",
        fetch_fail: "Fail to fetch data"
      }
    },
    ja: {
      message: {
        talk: 'トーク一覧',
        search: '友達検索',
        unreaded: "未読にする",
        readed: "既読にする",
        mark: "対応マーク",
        mark_none: "なし",
        mark_required: "要対応",
        delete: "選択したものを削除",
        status: "ステータス",
        username: "お名前",
        date: "受信日時",
        message_content: "メッセージ内容",
        search_option: "友達検索",
        view: "表示",
        images: "画像を送る",
        videos: "動画を送る",
        audio: "音声を送る",
        map: "位置情報を送る",
        template: "テンプレートを選ぶ",
        survey: "アンケートを送る",
        file: "ファイルを送る",
        mute: "ミュートする",
        send: "送信",
        friends: "友達追加日",
        source: "経由元",
        tags: "タグ",
        notes: "メモ",
        user_journey: "ユーザージャーニー表示",
        rich_menu: "リッチメニュー",
        survei: "アンケート",
        delivery_status: "配信状態",
        add_option: "追加オプション",
        relativedate_today: "今日",
        relativedate_yesterday: "昨日",
        user_info: "友達情報詳細",
        getting_data: "データの取得",
        fetch_fail: "データの取得に失敗しました。"
      }
    }
  }
  const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', // locale form config/app.php
    messages, // set locale messages
  })




/* Event:
  all-check-changed　: 全選択チェックChangeイベント
  item-sort : 項目ソートイベント
 */
const gridHeaderGeneral = {
  props:{
    initialSortKey: {
      type: String,
      default: 'timestamp'
    },
    initialSortDirection: {
      type: String,
      default: 'desc'
    }
  },
  data(){
    return {
      checkAll: false,
      sortKey : this.initialSortKey,
      sortDirection: this.initialSortDirection,
      }
  },
  methods:{
    onAllCheckChanged(e){
      this.checkAll = e.target.checked;
      this.$emit('all-check-changed', e);
    },
    // sort関連
    inverseSortType(type){
      if (type === 'asc') return 'desc';
      return 'asc';
    },
    sort(event, colField){
      if(colField == this.sortKey){
        this.sortDirection = this.inverseSortType(this.sortDirection);
      } else {
        this.sortDirection = 'asc'
      }
      this.sortKey = colField;
      this.$emit('item-sort',  {field: colField, type: this.sortDirection,});
    },
    resetCheckbox(){
      this.checkAll = false;
    },
  },
  template:`
    <div class="row mt-2">
      <div class="col-1 px-small text-center">
        <a-checkbox
          @change="onAllCheckChanged"
          :checked="checkAll"
        ></a-checkbox>
      </div>
      <div class="col-2 px-small font-size-table header-colmun-sortable" @click="sort($event, 'status')"
        :class="{ active: sortKey == 'status' }">
        @{{ $t('message.status') }}
        <span class="arrow" :class="sortDirection"></span>
      </div>
      <div class="col-2 px-small font-size-table header-colmun-sortable text-center" @click="sort($event, 'pf_user_display_name')"
        :class="{ active: sortKey == 'pf_user_display_name' }">
        @{{ $t('message.username') }}
        <span class="arrow" :class="sortDirection"></span>
      </div>
      <div class="col-5 px-small font-size-table">@{{ $t('message.message_content') }}</div>
      <div class="col-2 px-small font-size-table header-colmun-sortable text-center" @click="sort($event, 'timestamp')"
        :class="{ active: sortKey == 'timestamp' }">
        @{{ $t('message.date') }}
        <span class="arrow" :class="sortDirection"></span>
      </div>
    </div>`
  }

const calloutElement = {
  props:{
    avatar:{
      type: String,
    },
    message: {
      type: Object,
      required: true,
    }
  },
  data(){
    return {
    }
  },
  beforeMount(){
  },
  mounted(){
  },
  template:`
    <div>
      {{-- 送信メッセージは右寄せ　 --}}
      <div v-if="message.is_send" class="chat-right d-flex justify-content-end mb-2">
        <div v-html="message.content" class="order-2 px-3 py-2" :class="{ callout : message.message_type != 'sticker' }"></div>

        <div class="chat-time align-self-end order-1 mx-2 text-center small">
          <div>@{{message.time}}</div>
        </div>
      </div>

      {{-- 受信メッセージは左寄せ　 --}}
      <div v-else class="chat-left d-flex mb-2">
        <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink); margin-right: 5px;" icon="user" v-if="avatar==null" ></a-avatar>
        <a-avatar style="margin-right: 5px;" :src="avatar" v-if="avatar!=null" ></a-avatar>
        <div v-html="message.content" class="px-3 py-2" :class="{ callout : message.message_type != 'sticker' }"></div>

        <div class="chat-time align-self-end mx-2 small">
          <div>@{{message.time}}</div>
        </div>
      </div>
    </div>
  `,
}

/*
  propsでfollowerを定義していますが、talkViewコンポーネントではbindされたfollowerが切り替わることを想定していません。
  表示するfollowerを切り替えたい場合は、talkViewコンポーネント自体を差し替えること。
　Event:
 */
const talkView = {
  components: {
    'callout-element': calloutElement
  },
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props:{
    follower: {
      type: Object,
      default: null
    },
    loadingCount: {
        type: Number,
        default: 0
    }
  },
  data(){
    return {
      talkMessages: [],
      chatCtrl: 'input',
      toLatestMessageFlg: false,
      currentItemDate: null,

      textarea: '',
      attachmentFile: null,
      loading: false,
      requestInvalidated: false,
    }
  },
  methods:{
    openNotificationWithIcon(type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    resetView(){
      this.currentItemDate = null
      this.resetInputArea()
      this.getMessages()
    },
    getMessages(){
      let follower = this.follower;
      this.talkMessages = [];
      if(follower){
        this.$emit('input', this.loadingCount + 1);
        let self = this
        axios.get("/talk/message/" + follower.id)
        .then(res=>{
          if (!self.requestInvalidated) {
            self.toLatestMessageFlg = true;
            self.talkMessages = res.data.messages;
          }
        })
        .catch(e=> self.openNotificationWithIcon('error','メッセージの取得に失敗しました'))
        .finally(() => this.$emit('input', this.loadingCount - 1))
      }
    },
    formatDate(date){
      if(date == 'relativeToday'){
        return i18n.t('message.relativedate_today');
      } else if (date == 'relativeYesterday'){
        return i18n.t('message.relativedate_yesterday');
      }
      return date;
    },
    resetInputArea(){
      this.attachmentFile = null
      this.chatCtrl = 'input'
      this.textarea = ''
    },
    // ファイル送信：ファイル系選択処置
    selectedFile(fileData){
      if(this.attachmentFile === fileData){
        return
      }
      // file 確定
      this.attachmentFile = fileData
    },
    removeFile(fileData){
      // file 削除
      this.attachmentFile = null
    },
    shouldDisabled(data, type){
      if(data == null) {
        return false
      }
      if(data.attachment_type == type){
        return false
      }
      return true
    },
    // メッセージ送信
    sendMessage(e){
      var data = null;
      var type = null;
      let follower = this.follower
      if (follower == null)
        return

      if(this.attachmentFile != null){
        data = {
          messageType: this.attachmentFile.attachment_type,
          body : this.attachmentFile,
        }
      } else {
        let text = $('#messageText').val();
        data = {
          messageType: 'text',
          body : text,
        }
      }

      let self = this
      this.$emit('input', this.loadingCount + 1)
      axios.post("/talk/sendMessage/" + follower.id, data)
        .then(res => {
          if (!self.requestInvalidated) {
            // TODO: 確認用Notification
            self.openNotificationWithIcon('success','send ' + res.data.message_type)
            self.resetView()
          }
        })
        .catch(e => {
            let status = e.response.status
            if (status === 401) {
                this.openNotificationWithIcon('error', '有効なチャネルアクセストークンが指定されていません。')
            } else if (status === 400) {
                this.openNotificationWithIcon('error', 'LINEへの送信データに問題があります。');
            } else if (status === 429) {
                this.openNotificationWithIcon('error', 'LINE APIコールのレート制限を超過しました');
            } else {
                this.openNotificationWithIcon('error',this.$t('message.something_wrong'));
            }
        })
        .finally(() => this.$emit('input', this.loadingCount - 1))
    },
    cancelAttachment(){
      this.removeFile()
      this.chatCtrl = 'input'
    },
    showUserInfo(follower){
      this.$refs.userInfo.show(follower.id)
    },
  },
  destroyed(){
    this.requestInvalidated = true
  },
  mounted(){
    this.getMessages();
    this.chatScroll = new SimpleBar($('.chat-scroll')[0]).getScrollElement();
  },
  updated(){
    if(this.toLatestMessageFlg){
      this.chatScroll.scrollTo(0, this.chatScroll.scrollHeight);
      this.toLatestMessageFlg = false;
    }
  },
  computed: {
      loadingCountData: {
          get() {
              return this.loadingCount
          },
          set(val) {
              this.$emit('input', val)
          }
      }
  },
  template:`
  <div class="d-flex flex-column">
    <user-info ref="userInfo" v-model:loading-count="loadingCountData"></user-info>
    <div class="chat-body flex-fill" style="height: 370px; overflow: hidden;">
      <div style="position: absolute; right:  10px; top:  10px; z-index:2000;"><a-button @click="showUserInfo(follower)" class="font-size-table">@{{ $t('message.user_info') }}</a-button></div>
      <div class="chat-scroll h-100 px-2 px-sm-3 pb-2 pb-sm-3 pt-5 pt-sm-3">
        <div class="d-flex flex-column justify-content-end">
          <div v-for="message in talkMessages" :key = "message.id" class="d-flex flex-column justify-content-end">
            <div v-if="message.souldRenderDate" class="chat-info align-self-center small mb-2">
              @{{ formatDate(message.date) }}
            </div>
            <callout-element :message="message" :avatar=follower.pf_user_picture></callout-element>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-column input-area">
      <div class="d-flex flex-fill px-1 pt-1 px-sm-3 pt-sm-3" v-if="chatCtrl == 'input'">
        <div class="d-flex flex-column">
            <button class="btn btn-outline-dark mb-2" @click.prevent="chatCtrl = 'content'"><i class="fas fa-plus"></i></button>
        </div>
        <textarea id="messageText" v-model="textarea" class="border p-2 ml-1 p-sm-3 ml-sm-3 flex-fill"></textarea>
      </div>
      <div class="d-flex flex-fill flex-wrap chat-content-ctrl" v-if="chatCtrl == 'content'">
        <div class="col-6 d-flex flex-column justify-content-center border-right border-bottom">
          <addcontent-image @selected="selectedFile" @remove="removeFile" :disabled="shouldDisabled(attachmentFile, 'image')"></addcontent-image>
        </div>
        <div class="col-6 d-flex flex-column justify-content-center border-right border-bottom">
          <addcontent-video @selected="selectedFile" @remove="removeFile" :disabled="shouldDisabled(attachmentFile, 'video')"></addcontent-video>
        </div>
        <div class="col-6 d-flex flex-column justify-content-center border-right border-bottom">
          <addcontent-audio @selected="selectedFile" @remove="removeFile" :disabled="shouldDisabled(attachmentFile, 'audio')"></addcontent-audio>
        </div>
        <div class="col-6 d-flex flex-column justify-content-center border-right border-bottom">
          <addcontent-other @selected="selectedFile" @remove="removeFile" :disabled="shouldDisabled(attachmentFile, 'other')"></addcontent-other>
        </div>
      </div>
      <div class="text-center py-2" style="position: relative;">
        <button class="btn btn-sm btn-outline-dark px-5" @click="sendMessage">@{{ $t('message.send') }}</button>
        <button v-if="chatCtrl == 'content'" @click="cancelAttachment" class="btn btn-sm fa-2x"
          style="position: absolute; right: 10px;"><i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>`
  }

  var talk = new Vue({
    i18n,
    el: '#talk',
    components:{
    'grid-header-general' :gridHeaderGeneral,
    'talk-view' :talkView,
    },
    data(){
      return {
        loadingCount: 0,
        viewMode : '{{ $selectedFromOutside != null ? "chat" : "list" }}',
        followers:[],
        checkedFollowers:[],
        sortKey: {field: 'timestamp', type: 'desc',},
        hasCheckedFollowers: false,
        filteredRows: [],
        searchTerm: '',
        chatListScroll: null,
        selectedFollower: null,
        initialSelectedId: {!! $selectedFromOutside != null ? "'".$selectedFromOutside->id."'" : 'null' !!},
        initFlg : true,

        chatCtrl: 'input',
        talkMessages: [],

        loading: false,
        batchProcessing: false,
        chatRequests: [],
      }
    },
    watch: {
      followers:{
        handler(){
          this.filteredRows = _.cloneDeep(this.followers);
        }
      },
      checkedFollowers:{
        handler(){
          this.hasCheckedFollowers = this.checkedFollowers.length > 0;
        }
      },
    },
    computed: {
      processedRecords(){
        let records = this.filteredRows;

        const term = this.searchTerm && this.searchTerm.toLowerCase();
        if(term){
          records = records.filter((r) => {
            return r.pf_user_display_name.toLowerCase().indexOf(term) !== -1;
          });
        }
        let sortKey = this.sortKey;

        if (Object.keys(sortKey).length) {
          records = records.sort(function(a,b){
            a = a[sortKey.field]
            b = b[sortKey.field]
            let order = sortKey.type == 'asc' ? 1 : -1

            return (a === b ? 0 : a > b ? 1 : -1) * order
          })
        }

        if(term){
          this.filteredRows = records;
        }
          // 初回だけ初期選択の機会を与える
        if (this.initFlg) {
          if (this.filteredRows.length > 0) {
            const records = this.filteredRows
            const initialSelected = this.initialSelectedId != null
             ? records.find((r)=>r.id == this.initialSelectedId)
             : records[0]
            this.initFlg = false
            const self = this
            this.$nextTick(function () {
              self.changeSelectedFollower(initialSelected)
            })
          }
        }
        return records;
      },
    },
    methods: {
      openNotificationWithIcon(type, message, desc) {
        this.$notification[type]({
          message: message,
          description: desc,
        });
      },
      checkChanged(event) {
        console.log(event);
        var follower = event.target.value;
        if (event.target.checked == true) {
          this.checkedFollowers.push(follower);
          this.$set(follower, 'checked', true)

        } else {
          var id = this.checkedFollowers.indexOf(follower);
          this.checkedFollowers.splice(id, 1);
          this.$set(follower, 'checked', false)
        }
        this.$refs.headerGeneral.resetCheckbox();
        this.debugSelected();
      },
      onCheckAllChange (e) {
        let checkAll = e.target.checked;
        this.checkedFollowers = [];
        if (checkAll) {
          // 全件選択
          this.filteredRows.forEach( follower => {
            this.checkedFollowers.push(follower);
            this.$set(follower, 'checked', true)
          });
        } else {
          this.filteredRows.forEach( follower => {
            this.$set(follower, 'checked', false)
          });
        }
        this.debugSelected();
      },

      itemSort(field){
        this.sortKey = field;
      },
      // search
      searchFriends(value, event){
        this.$refs.headerGeneral.resetCheckbox();

        // 選択済み解除
        this.checkedFollowers = [];

        // データ初期化
        this.filteredRows = _.cloneDeep(this.followers);
        this.searchTerm = value;
      },
      // List mode method
      showTalk(selectedFollower){
        this.viewMode = 'chat';
        this.changeSelectedFollower(selectedFollower)
      },
      // Chat mode methods
      startScrollChatList() {
        var self = this;

        if (!this.chatListSCrollInterval) {
          this.chatListSCrollInterval = setInterval(function () {
            self.chatListScroll.scrollTo(0, self.chatListScroll.scrollTop + 20)
          }, 30);
        }
      },
      stopScrollChatList() {
        clearInterval(this.chatListSCrollInterval);
        this.chatListSCrollInterval = null;
      },
      changeSelectedFollower(selectedFollower) {
        this.selectedFollower = selectedFollower;
        console.log('change');
        this.chatRequests.push(selectedFollower)
        delete this.chatRequests[this.chatRequests.length - 2]
      },
      getData(){
        this.loadingCount++
        axios.get("/talk/list")
        .then(res => {
          this.followers = res.data.latest_list;
        })
        .catch(e => this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
        .finally(() => this.loadingCount--)
      },
      getSelected(){
        return this.checkedFollowers.map( follower => {
          return {follower_id: follower.id}
        })
      },
      reloadData(){
        this.followers = []
        this.checkedFollowers = []
        this.selectedFollower = null
        this.$refs.headerGeneral.resetCheckbox();
        this.getData()
      },
      batch(url, data){
        this.batchProcessing = true
          this.loadingCount++
          axios.post(url, data)
          .then(res => {
            this.reloadData()
          })
          .catch(e => this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
          .finally(() => {
              this.batchProcessing = false
              this.loadingCount--
          })
      },
      markUnRead() {
        if (this.batchProcessing){
          return
        }
        this.batch('/talk/mark-unread', this.getSelected())
      },
      markRead() {
        if (this.batchProcessing){
          return
        }
        this.batch('/talk/mark-read', this.getSelected())
      },
      markSupported(item) {
        if (this.batchProcessing){
          return
        }
        this.batch('/talk/mark-supported/' + item.key, this.getSelected())
      },
      messageDelete() {
        if (this.batchProcessing){
          return
        }
        this.batch('/talk/delete', this.getSelected())
      },
      showUserInfo(follower){
        this.$refs.userInfo.show(follower.id)
      },
      debugSelected(){
        console.log(this.checkedFollowers.map((s)=>{return s.pf_user_display_name}));
      },
    },
    mounted(){
      this.chatListScroll = new SimpleBar($('.chat-list')[0]).getScrollElement()
      this.getData();
    },
  });
</script>
@endsection

@section('css-styles')
<style>
    .card-like {
        background-color: #fff;
    }

    .chat-view {
        height: 720px;
    }

    .header-colmun-sortable.active {
        background-color: #f0f0f0;
    }

    .header-colmun-sortable.active .arrow {
        opacity: 1;
    }

    .arrow {
        display: inline-block;
        vertical-align: middle;
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: 0;
    }

    .arrow.asc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 4px solid rgba(0, 0, 0, .65);
    }

    .arrow.desc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid rgba(0, 0, 0, .65);
    }

    .header-colmun-sortable {
        color: var(--real-blue) !important;
    }

    .new-message-mark {
        color: var(--real-blue) !important;
        opacity: 0;
    }

    .new-message-mark.active {
        color: var(--real-blue) !important;
        font-size: large;
        opacity: 1;
    }

    .circle-badge {
        background-color: #34ffe0;
        margin: 0 auto;
        border-radius: 50%;
        width: 16px;
        height: 16px;
    }

    .chat-active {
        background-color: #e7f9fd;
    }

    .chat-item {
        cursor: pointer;
    }

    .chat-item:hover {
        background-color: #f9feff;
    }

    .chat-item.active {
        background-color: #e7f9fd;
    }

    .chat-body {
        background-color: #f5f0f0;
    }

    .chat-content-ctrl>div {
        text-align: center;
    }

    .user-info {
        width: 250px;
    }

    .callout {
        border-radius: .25rem;
        max-width: 300px;
    }

    .chat-left .callout {
        background-color: #fff;
    }

    .chat-right .callout {
        background-color: #cff4c9;
    }

    .survey-caption,
    .survey-actions {
        min-width: 200px;
        margin-left: -16px;
        margin-right: -16px;
        padding-left: 16px;
        padding-right: 16px;
    }

    .survey-caption {
        border-bottom: 1px solid #cccccc;
        text-align: left;
    }

    .survey-title {
        font-weight: bold;
    }

    .survey-text {
        margin: 8px auto;
    }

    .survey-action {
        text-align: center;
        margin: 8px auto;
    }

    .attachment img {
        width: 100%;
        height: auto;
    }

    .chat-info {
        color: #fff;
        background-color: #a2a2a2;
        border-radius: .75rem;
        padding: .25rem 1rem;
    }

    .sp-min {
        display: inline;
    }

    .input-area {
        height: 350px;
    }

    @media (max-width: 576px) {
        .sp-min {
            display: none;
        }

        .input-area {
            height: 200px;
        }

        .chat-view {
            height: auto;
        }

        .ant-checkbox-inner {
            width: 100%;
            height: auto;
            min-width: 14px;
            min-height: 14px;
        }
    }
</style>
@endsection
