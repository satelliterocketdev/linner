@extends('layouts.app-mother')

@section('content')
<div id="followers" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <tutorial></tutorial>
  <a-card :bordered="false" class="mb-4">
    <div class="row" id="head">
      <div class="col-md-6">
        <h2>@{{ $t('message.all_friends_list') }}</h2>
      </div>
      <!-- search section -->
      <div class="col-md-3 col-lg-4 text-left mb-1">
        <div class="row">
          <div class="col-md-12">
            <a-input-search :placeholder="$t('message.search')" @search="searchFriends"/>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-lg-2 text-left mb-1">
        <div class="row">
          <div class="col d-flex justify-content-end">
            <a-select class="flex-fill" default-value="0" v-model="searchOption" @change="changeSearchOption" >
              <a-select-option value="0">@{{ $t('message.friend_list') }}</a-select-option>
              <a-select-option value="Z">@{{ $t('message.test_user') }}</a-select-option>
              <a-select-option value="X">@{{ $t('message.invisible_list') }}</a-select-option>
            </a-select>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-1">
        <div class="col-md-6 form-inline">
            <button class="btn rounded-purple my-1" @click="changeVisibility" :disabled="!hasSelectedFollowers || searchOption === '9'">@{{$t("message.display_none")}}</button>
            <follower-block-button :targets="selectedFollowers" @updated="userInfoUpdated" :disabled="!hasSelectedFollowers || searchOption === '9'" class="px-2 " v-model:loading-count="loadingCount"></follower-block-button>
        </div>
        <div class="col text-md-right">
            <a class="btn btn-koniro text-white" @click="changeScreen">
              <div v-if="isBlockedUsersScreen">@{{$t("message.friend_list")}}</div>
              <div v-else>@{{$t("message.block_list")}}</div>
            </a>
        </div>
    </div>
    <grid-header-general ref="headerGeneral" @all-check-changed="onCheckAllChange" @item-sort="itemSort"></grid-header-general>
  </a-card>
  <a-card class="mt-2" v-for="(follower,key) in sortedFollowers" :key="key">
    <grid-body-general :follower="follower" @row-check-changed="onChange"></grid-body-general>  
  </a-card>
</div>
@endsection
@section('footer-scripts')

<script src="{{ asset('js/components/follower/follower-block.js') }}"></script>
<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script>
Vue.config.devtools = true

const messages = {
    en: {
        message: {
            all_friends_list: 'All Friends List',
            search: 'Search',
            search_option: 'Search Option',
            name: 'Name',
            account: 'Account',
            added_date: 'Added Date',
            source: 'Source',
            block_list: 'Block List',
            display_none: 'Display None',
            block: 'Block',
            friend_list: 'Friend List',
            test_user: 'Test User',
            invisible_list: 'Invisible List',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        }
    },
    ja: {
        message: {
            all_friends_list: '全フレンドリスト',
            search: '友達検索',
            search_option: '詳細検索',
            name: 'お名前',
            account: 'アカウント',
            added_date: '友達追加日',
            source: '経由元',
            block_list: 'ブロックリスト',
            display_none: '非表示にする',
            block: 'ブロック',
            friend_list: 'フレンドリスト',
            test_user: 'テストユーザー',
            invisible_list: '非表示リスト',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        }
    }
}
const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', // locale form config/app.php
    messages, // set locale messages
})

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
        sortDirection: this.initialSortDirection
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
  <div class="row mt-2 font-size-table" id="lower">
    <div class="col-3 px-small">
      <div class="row mx-0 no-gutters">
        <div class='col-3'>
          <a-checkbox
            @change="onAllCheckChanged"
            :checked="checkAll"
          ></a-checkbox>
        </div>
        <div class='col-0'>&nbsp;</div>
        <div class='col-8 header-colmun-sortable' @click="sort($event, 'pf_user_display_name')"
        :class="{ active: sortKey == 'pf_user_display_name' }">
          @{{ $t('message.name') }}
          <span class="arrow" :class="sortDirection"></span>
        </div>
      </div>
    </div>
    <div class='col px-small header-colmun-sortable' @click="sort($event, 'timedate_followed')"
      :class="{ active: sortKey == 'timedate_followed' }">
      @{{ $t('message.added_date') }}
      <span class="arrow" :class="sortDirection"></span>
    </div>
{{--    <div class='col px-small header-colmun-sortable' @click="sort($event, 'conversion_title')"--}}
{{--      :class="{ active: sortKey == 'conversion_title' }">--}}
{{--      @{{ $t('message.source') }}--}}
{{--      <span class="arrow" :class="sortDirection"></span>--}}
{{--    </div>--}}
    <div class='col px-small header-colmun-sortable' @click="sort($event, 'account')"
      :class="{ active: sortKey == 'account' }">
      @{{ $t('message.account') }}
      <span class="arrow" :class="sortDirection"></span>
    </div>
  </div>`
}

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
    }
  },
  template:`
  <div class="row wordbreak-all font-size-table" style="align-items: center; line-height: 1.2;">
    <div class="col-12 col-sm-3 follower-icon">
      <div class="row no-gutters" style="align-items: center;">
        <div class="col-2">
          <a-checkbox @change="onChange" :value="follower" :checked="follower.checked" class="mr-1"></a-checkbox>
        </div>
        <div class="col-4">
          <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="follower.pf_user_picture==null" ></a-avatar>
          <a-avatar :src="follower.pf_user_picture" v-if="follower.pf_user_picture!=null" ></a-avatar>
        </div>
        <div class="col-6">@{{follower.pf_user_display_name}}</div>
      </div>
    </div>
    <div class="col-6 col-sm-4 px-small font-size-table">@{{ follower.timedate_followed }}</div>
{{--    <div class="col-4 col-sm-3 px-small font-size-table">@{{ follower.conversion_title }}</div>--}}
    <div class="col-6 col-sm-5 px-small font-size-table">
      <p v-for="account in follower.accounts" style="line-height:1; margin-bottom: 5px;">
        @{{ account }}
      </p>
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
      filteredRows: [],
      hasSelectedFollowers: false,
      searchTerm: '',
      searchOption: '0',
      isBlockedUsersScreen: false,
      visibilityBtnLoading: false,
      tutorial: {{ var_export(Auth::user()->finished_tutorial) }}
    }
  },
  watch: {
    selectedFollowers:{
      handler(){
        this.hasSelectedFollowers = this.selectedFollowers.length > 0;
      }
    }
  },
  computed: {
    sortedFollowers(){
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
      // if(term){
      //   this.filteredRows = records;
      // }
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
      // this.filteredRows = _.cloneDeep(this.followers);
      this.searchTerm = value; 
    },
    getData(){
      self = this
      this.loadingCount++
      axios({
        method:"get",
        url:"friends/list",
      }).then((res)=>{
        this.followers = res.data
        this.changeSearchOption('0', null)
      }).catch(e=>{
        this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
      })
      .finally(() => this.loadingCount--)
    },
    changeSearchOption(value, option) {
      switch(value) {
        // テストユーザー
        case 'Z':
          this.filteredRows = this.followers.filter(function(follower) {
            return follower.is_tester == 1
          })
          break
        // フレンドリスト
        case '0':
          this.filteredRows = this.followers.filter(function(follower) {
            return follower.is_blocked == 0 && follower.is_visible == 0;
          })
          break
        // 非表示リスト
        case 'X':
          this.filteredRows = this.followers.filter(function(follower) {
            return follower.is_visible == 1;
          })
          break
        case 'Y':
          this.filteredRows = this.followers.filter(function(follower) {
            return follower.is_blocked == 1;
          })
          break
      }
    },
    changeScreen() {
      this.isBlockedUsersScreen = !this.isBlockedUsersScreen
      if (this.isBlockedUsersScreen) {
        this.changeSearchOption('Y', null)
      } else {
        this.changeSearchOption('0', null)
      }
    },
    userInfoUpdated(){
      this.reloadData()
    },
    reloadData(){
      this.followers = []
      this.selectedFollowers = []
      this.$refs.headerGeneral.resetCheckbox();
      this.getData()
    },
    changeVisibility() {
      this.visibilityBtnLoading = true
      let ids = this.selectedFollowers.map( follower => {
        return follower.id
      })
      this.loadingCount++
      axios.post("friends/visibility", {
        followers_ids: ids,
        visibility: 1
      })
      .then( res => {
        this.reloadData()
      })
      .catch( e => {
        this.openNotificationWithIcon("error","block process")
      })
      .finally( () => {
        this.visibilityBtnLoading = false
        this.loadingCount--
      })
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
.btn-koniro {
    color: #fff;
    background-color: #002EAA;
    border-radius: 10px;
}
.btn-koniro:hover {
    color: #fff;
    background-color: #223a70;
}
.btn-koniro.focus,.btn-koniro:focus{
    color:#fff;
}

.follower-icon {
    border-right:#e4e4e4 solid 1px;
}

@media (max-width: 576px) {
    .follower-icon {
        border-right:0px;
        border-bottom:#e4e4e4 solid 1px;
        padding-bottom: 8px;
        margin-bottom: 10px;
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