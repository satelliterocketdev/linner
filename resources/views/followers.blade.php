@extends('layouts.app')

@section('content')
<div id="followers" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <a-card :bordered="false" class="mb-4">
    <div class="row" id="head">
      <div class="col-sm-4 col-md-6">
        <h2>@{{ $t('message.followers') }}</h2>
      </div>
      <!-- search section -->
      <div class="col-sm-5 col-md-3 text-left mb-2 mb-sm-0">
        <div class="row">
          <div class="col-md-12 px-1">
            <a-input-search :placeholder="$t('message.search')" @search="searchFriends"/>
          </div>
        </div>
      </div>
      <div class="col-sm-3 col-md-3 text-left mb-2 mb-sm-0">
        <div class="row">
          <div class="col d-flex justify-content-end">
            <a-select class="flex-fill" default-value="0" v-model="searchOption" @change="changeSearchOption" >
              <a-select-option value="0">@{{ $t('message.friend_list') }}</a-select-option>
              <a-select-option value="9">@{{ $t('message.block_list') }}</a-select-option>
              <a-select-option value="Z">@{{ $t('message.test_user') }}</a-select-option>
            </a-select>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-1">
      <div class="form-inline">
        <add-tag-modal :targets="selectedFollowers" @updated="userInfoUpdated" :disabled="!hasSelectedFollowers || searchOption === '9'" class="mt-1 pr-2" v-model:loading-count="loadingCount"></add-tag-modal>
        <add-scenario-modal :targets="selectedFollowers" @updated="userInfoUpdated" :disabled="!hasSelectedFollowers || searchOption === '9'" class="mt-1 pr-2" v-model:loading-count="loadingCount"></add-scenario-modal>
        <add-richmenu-modal :targets="selectedFollowers" @updated="userInfoUpdated" :disabled="!hasSelectedFollowers || searchOption === '9'" class="mt-1 pr-2" v-model:loading-count="loadingCount"></add-richmenu-modal>
        <follower-block-button :targets="selectedFollowers" @updated="userInfoUpdated" :disabled="!hasSelectedFollowers || searchOption === '9'" class="mt-1 pr-2" v-model:loading-count="loadingCount"></follower-block-button>
      </div>
    </div>

    <!-- table-grid-header -->
    <grid-header-general class="mt-md-5" ref="headerGeneral" @all-check-changed="onCheckAllChange" @item-sort="itemSort"></grid-header-general>
  </a-card>
  <!-- table-grid -->
  <user-info ref="userInfo"></user-info>
  
  <a-card class="mt-2" v-for="(follower,key) in processedRecords" :key="key">
    <grid-body-general :follower="follower" @row-check-changed="onChange" @row-button-clicked="goTalk" @row-link-clicked="userInfo"></grid-body-general>  
  </a-card>
</div>
@endsection
@section('footer-scripts')

<!-- modal -->
<script src="{{ asset('js/components/followers/userinfo.js') }}"></script>
<script src="{{ asset('js/components/follower/addtagmodal.js') }}"></script>
<script src="{{ asset('js/components/follower/addscenariomodal.js') }}"></script>
<script src="{{ asset('js/components/follower/addrichmenumodal.js') }}"></script>
<script src="{{ asset('js/components/follower/follower-block.js') }}"></script>
<script>
Vue.config.devtools = true

const messages = {
    en: {
        message: {
            followers: 'Friends',
            search: 'Search',
            sort_option: 'Search Option',
            sort_by_tag: 'Sort by tag',
            sort_by_scenario: 'Sort by scenario',
            sort_by_source: 'Sort by source',
            show_only_new_friends_today: 'Show only new friends today',
            show_only_new_friends_week: 'Show only new friends this week',
            show_only_friend_with_unread_messages: 'Show only friends with unread messages',
            search_option: 'Search Option',
            show_blocked_users: 'Show Blocked Users',
            add_tag: 'Add Tag',
            add_scenario: 'Add Scenario',
            add_menu: 'Add Menu',
            blocked: 'Blocked',
            tag: 'Tag',
            name: 'Name',
            state: 'State',
            talk: 'Talk',
            user_info: 'UserInfo',
            not_published: 'Not published',
            added_date: 'Added Data',
            source: 'Source',
            finish: 'Finish',
            reset: 'Reset',
            rich_menu_create: 'Create Rich Menu',
            show_only_next_date: 'I dont know',
            remove: 'Remove',
            add: 'Add',
            rich_menu_choose: 'Choose Rich Menu',
            now: 'Now',
            after_one_hour: 'After One Hour',
            after_three_hour: 'After Three Hours',
            after_twelf_hour: 'After Twelf Hours',
            after_twenty_four_hour: 'After Twenty Four Hours',
            publishment_start_source: 'Unknown',
            start_publish: 'Start Publish',
            choose_tag: 'Choose Tag',
            publishment_settings: 'Publishment Settings',
            tag_name: 'Tag Name',
            create_new_tag: 'Create New Tag',
            add_tag: 'Add Tag',
            add_scenario: 'Add Scenario',
            add_menu: 'Add Menu',
            tag_settings: 'Tag Settings',
            scenario_settings: 'Scenario Settings',
            rich_menu_settings: 'Rich Menu Settings',
            friend_list: 'Friend List',
            block_list: 'Block List',
            test_user: 'Test User',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        }
    },
    ja: {
        message: {
            followers: '友だちリスト',
            search: '友達検索',
            sort_option: '表示オプション',
            sort_by_tag: 'タグ別で表示',
            sort_by_scenario: 'シナリオ別で表示',
            sort_by_source: '経由元別で表示',
            show_only_new_friends_today: '今日登録した友達のみ表示',
            show_only_new_friends_week: '今週登録した友達のみ表示',
            show_only_friend_with_unread_messages: '未読メッセージのある友達のみ表示',
            search_option: '詳細検索',
            show_blocked_users: '非表示リスト',
            add_tag: 'タグ追加',
            add_scenario: 'シナリオ追加',
            add_menu: 'メニュー追加',
            blocked: '非表示にする',
            tag: 'タグ',
            name: 'お名前',
            state: '配信状態',
            talk: 'トーク',
            user_info: '表示',
            not_published: '未配信',
            added_date: '友達追加日',
            source: '経由元',
            finish: '完了',
            reset: 'リセット',
            rich_menu_create: 'リッチメニュー作成画面へ',
            show_only_next_date: '次の期間のみ表示',
            remove: '外す',
            add: '追加する',
            rich_menu_choose: 'リッチメニュー名を選ぶ',
            now: '即時',
            after_one_hour: '１時間後',
            after_three_hour: '３時間後',
            after_twelf_hour: '１２時間後',
            after_twenty_four_hour: '２４時間後',
            publishment_start_source: '通目から配信',
            start_publish: 'に配信開始する',
            choose_tag: 'タグを選ぶ',
            publishment_settings: '配信設定',
            tag_name: 'タグ名',
            create_new_tag: '新しいタグを作成',
            add_tag: 'タグ追加',
            add_scenario: 'シナリオ追加',
            add_menu: 'メニュー追加',
            tag_settings: 'タグ設定',
            scenario_settings: 'シナリオ設定',
            rich_menu_settings: 'リッチメニュー設定',
            friend_list: 'フレンドリスト',
            block_list: 'ブロックリスト',
            test_user: 'テストユーザー',
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
      default: 'pf_user_display_name'
    },
    initialSortDirection: {
      type: String,
      default: 'asc'
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
    }
  },
  template:`
  <div class="row mt-2" id="lower">
    <div class="col-3">
      <div class="row">
        <div class='pl-0 pr-1 col-2'>
          <a-checkbox
            @change="onAllCheckChanged"
            :checked="checkAll"
          ></a-checkbox>
        </div>
        <div class='d-none d-md-inline-block col-md-4'>&nbsp;</div>
        <div class='col col-10 col-md-6 header-colmun-sortable' @click="sort($event, 'pf_user_display_name')"
        :class="{ active: sortKey == 'pf_user_display_name' }">
          @{{ $t('message.name') }}
          <span class="arrow" :class="sortDirection"></span>
        </div>
      </div>
    </div>
    <div class='col header-colmun-sortable' @click="sort($event, 'timedate_followed')"
      :class="{ active: sortKey == 'timedate_followed' }">
      @{{ $t('message.added_date') }}
      <span class="arrow" :class="sortDirection"></span>
    </div>
    <div class='col header-colmun-sortable' @click="sort($event, 'scenario_name')"
      :class="{ active: sortKey == 'scenario_name' }">
      @{{ $t('message.state') }}
      <span class="arrow" :class="sortDirection"></span>
    </div>
    <div class='col'>
      @{{ $t('message.tag') }}
    </div>
    <div class='col' ></div>
  </div>`
}

/* Event:
  row-check-changed　: 行チェックボックスChangeイベント
  row-button-clicked : 行ボタンClickイベント
 */
const gridBodyGeneral = {
  props:{
    follower : Object
  },
  methods:{
    onChange(event){
      this.$emit('row-check-changed', event);
    },
    onClick(event){
      this.$emit('row-button-clicked', this.follower);
    },
    onLink(event){
      this.$emit('row-link-clicked', this.follower);
    }
  },
  template:`
  <div class="row" style="align-items: center;">
    <div class="col-3" style="border-right:#e4e4e4 solid 1px;">
      <div class="row pr-3" style="align-items: center;">
        <div class="col-sm-2 pl-0 pr-1">
          <a-checkbox @change="onChange" :value="follower" :checked="follower.checked" class="mr-1"></a-checkbox>
        </div>
        <div class="col-sm-4 px-1">
          <a-avatar size="large" style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="follower.pf_user_picture==null" ></a-avatar>
          <a-avatar size="large" :src="follower.pf_user_picture" v-if="follower.pf_user_picture!=null" ></a-avatar>
        </div>
        <div class="col-sm-6 pl-1 pr-0" style="color: #25A5D0;"><a @click.prevent="onLink">@{{follower.pf_user_display_name}}</a></div>
      </div>
    </div>
    <div class="col">@{{ follower.timedate_followed }}</div>
    <div class="col">@{{ follower.scenario_name }}</div>
    <div class="col">@{{ follower.tags }}</div>
    <div class="col">
    <span class="new-message-mark" :class="{ 'active': follower.message_status == '0' }">●</span>
    <button class="btn mx-1 btn-info small-text" @click="onClick" :disabled="!follower.source_user_id">@{{ $t('message.user_info') }}</button>
    </div>
  </div>`
}

var followers = new Vue({
  i18n,
  el:'#followers',
  components:{
    'grid-header-general' :gridHeaderGeneral,
    'grid-body-general' : gridBodyGeneral,
  },
  data(){
    return{
      loadingCount: 0,
      followers:[],
      selectedFollowers:[],
      sortKey: {field: 'pf_user_display_name', type: 'asc',},
      hasSelectedFollowers: false,
      filteredRows: [],
      searchTerm: '',
      searchOption: '0',
    }
  },
  watch: {
    followers:{
      handler(){
        this.filteredRows = _.cloneDeep(this.followers);
      }
    },
    selectedFollowers:{
        handler(){
          this.hasSelectedFollowers = this.selectedFollowers.length > 0;
        }
      },
  },
  computed: {
    processedRecords(){
      var records = this.filteredRows;

      var term = this.searchTerm && this.searchTerm.toLowerCase();
      if(term){
        records = records.filter((r) => {
          return r.pf_user_display_name.toLowerCase().indexOf(term) !== -1;
        });
      }

      if (Object.keys(this.sortKey).length) {
        var sortKey = this.sortKey
        records = records.sort(function(a,b){
          a = a[sortKey.field]
          b = b[sortKey.field]
          var order = sortKey.type == 'asc' ? 1 : -1

          return (a === b ? 0 : a > b ? 1 : -1) * order
        })
      }
      if(term){
        this.filteredRows = records;
      }
      return records;
    }

  },
  methods: {
    openNotificationWithIcon(type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    onChange(event) {
      console.log(event);
      var follower = event.target.value;
      if (event.target.checked == true) {
        this.selectedFollowers.push(follower);
        this.$set(follower, 'checked', true)

      } else {
        var id = this.selectedFollowers.indexOf(follower);
        this.selectedFollowers.splice(id, 1);
        this.$set(follower, 'checked', false)
      }
      this.$refs.headerGeneral.resetCheckbox();
      this.debugSelected();
    },
    onCheckAllChange (e) {
      let checkAll = e.target.checked;
      this.selectedFollowers = [];
      if (checkAll) {
        // 全件選択
        this.filteredRows.forEach( follower => {
          this.selectedFollowers.push(follower);
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
      this.selectedFollowers = [];

      // データ初期化
      this.filteredRows = _.cloneDeep(this.followers);      
      this.searchTerm = value; 
    },
    changeSearchOption(value, option) {
      this.getData()
    },
    goTalk(userinfo){
      window.location.href = '/talk/' + userinfo.id;
    },
    getData(){
      var data = {}
      if(this.searchOption){
        data = { params: { 'mode' : this.searchOption }}
      }
      this.loadingCount++
      axios.get("follower/lists", data)
      .then((res)=>{
        this.followers = res.data.followers.map((follower)=>{
           follower['checked'] = false;
           return follower;
        });
      })
      .catch(e=>{
        this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
      })
      .finally(() => this.loadingCount--)
    },
    userInfo(follower){
      this.$refs.userInfo.show(follower.id)
    },
    getSelected(){
      return this.selectedFollowers.map( follower => {
        return follower.id
      })
    },
    reloadData(){
      this.followers = []
      this.selectedFollowers = []
      this.$refs.headerGeneral.resetCheckbox();
      this.getData()
    },
    debugSelected(){
      console.log(this.selectedFollowers.map((s)=>{return s.pf_user_display_name}));
    },
    userInfoUpdated(){
      this.reloadData()
    }
  },
  beforeMount(){
    this.getData();
  }
})
</script>
<style scoped>
</style>
@endsection

@section('css-styles')
<style>
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
  border-bottom: 4px solid rgba(0,0,0,.65);
}

.arrow.desc {
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 4px solid rgba(0,0,0,.65);
}

.header-colmun-sortable {
  color: var(--real-blue) !important;
}

.new-message-mark {
  color: var(--real-blue) !important;
  opacity: 0;
}
.new-message-mark.active{
  color: var(--real-blue) !important;
  font-size: large;
  opacity: 1;
}

.block-message-br {
    white-space: pre;
}

.content-1st {
    border-right: solid 1px rgba(0,0,0,0.1);
}

@media (max-width: 767px) {
    .block-message-br {
        white-space: normal;
    }
}

@media (max-width: 576px) {
    .content-1st {
        border-right: 0;
        border-bottom: solid 1px rgba(0,0,0,0.1);
    }

    .ant-card-body .col-sm-2,
    .ant-card-body .col-sm-4,
    .ant-card-body .col-sm-6,
    .ant-card-body .col {
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 0.4em !important;
        word-break: break-all !important;
    }
}
</style>
@endsection