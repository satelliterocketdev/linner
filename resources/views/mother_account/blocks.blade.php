@extends('layouts.app-mother')

@section('content')
<div id="followers" v-cloak>
  <a-card :bordered="false" class="mb-4">
    <div class="row" id="head">
      <div class="col">
        <h2>@{{ $t('message.entire_block_list') }}</h2>
      </div>
      <!-- search section -->
      <div class="col-sm-4 text-right">
        <div class="row">
          <div class="col-md-12">
            <a-input-search :placeholder="$t('message.search')" @search="searchFriends"/>
          </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a-button type="primary" size="small" class="mt-1 px-2 float-md-right">@{{ $t('message.search_option') }}</a-button>
            </div>
        </div>
      </div>
    </div>
    <div class="row my-2">
        <div class="col text-right">
            <a class="btn btn-cobaltblue m-1" href="{{url('all_friends_list')}}">@{{$t("message.friends_list")}}</a>
        </div>
    </div>
    <!-- table-grid-header -->
    <grid-header-general ref="headerGeneral" @all-check-changed="onCheckAllChange" @item-sort="itemSort"></grid-header-general>
  </a-card>
  <!-- table-grid -->
  <a-card class="mt-2" v-for="(follower,key) in processedRecords" :key="key">
    <grid-body-general :follower="follower" @row-check-changed="onChange" @row-button-clicked="transition"></grid-body-general>  
  </a-card>
</div>
@endsection
@section('footer-scripts')

<script>
Vue.config.devtools = true

const messages = {
    en: {
        message: {
            entire_block_list: 'Entire Block List',
            search: 'Search',
            search_option: 'Search Option',
            name: 'Name',
            account: 'Account',
            added_date: 'Added Date',
            source: 'Source',
            block_list: 'Block List',
            friends_list: 'Friends List',
            blocked: 'Blocked',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        }
    },
    ja: {
        message: {
            entire_block_list: '全体ブロックリスト',
            search: '友達検索',
            search_option: '詳細検索',
            name: 'お名前',
            account: 'アカウント',
            added_date: '友達追加日',
            source: '経由元',
            block_list: 'ブロックリスト',
            friends_list: 'フレンドリスト',
            blocked: 'ブロック済み',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        }
    }
}
const i18n = new VueI18n({
    locale: '{{config('app.locale')}}',
    messages,
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
        <div class='col-md-2'>
          <a-checkbox
            @change="onAllCheckChanged"
            :checked="checkAll"
          ></a-checkbox>
        </div>
        <div class='col-md-4'>&nbsp;</div>
        <div class='col-md-6 header-colmun-sortable' @click="sort($event, 'pf_user_display_name')"
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
    <div class='col header-colmun-sortable' @click="sort($event, 'conversion_title')"
      :class="{ active: sortKey == 'conversion_title' }">
      @{{ $t('message.source') }}
      <span class="arrow" :class="sortDirection"></span>
    </div>
    <div class='col header-colmun-sortable' @click="sort($event, 'account')"
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
  <div class="row" style="align-items: center;">
    <div class="col-3" style="border-right:#e4e4e4 solid 1px;">
      <div class="row pr-3" style="align-items: center;">
        <div class="col-md-2">
          <a-checkbox @change="onChange" :value="follower" :checked="follower.checked" class="mr-1"></a-checkbox>
        </div>
        <div class="col-md-4">
          <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="follower.pf_user_picture==null" ></a-avatar>
          <a-avatar :src="follower.pf_user_picture" v-if="follower.pf_user_picture!=null" ></a-avatar>
        </div>
        <div class="col-md-6">@{{follower.pf_user_display_name}}</div>
      </div>
    </div>
    <div class="col">@{{ follower.timedate_followed }}</div>
    <div class="col">@{{ follower.conversion_title }}</div>
    <div class="col">@{{ follower.account }}</div>
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
      followers:[],
      selectedFollowers:[],
      sortKey: {field: 'pf_user_display_name', type: 'asc',},
      filteredRows: [],
      searchTerm: '',
    }
  },
  watch: {
    followers:{
      handler(){
        this.filteredRows = _.cloneDeep(this.followers);
      }
    }
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
    transition(userinfo){
      window.location.href = '/talk/' + userinfo.id;
    },
    getData(){
      axios({
        method:"get",
        url:"friends/list",
      }).then((res)=>{
        this.followers = res.data.followers.map((follower)=>{
           follower['checked'] = false;
           return follower;
        });
      }).catch(e=>{
        this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'))
      })
    },
    handleMenuClick(e) {
      console.log('click', e);
    },
    debugSelected(){
      console.log(this.selectedFollowers.map((s)=>{return s.pf_user_display_name}));
    },
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
    .btn-cobaltblue {
        color: #fff;
        background-color: #0068b7;
        border-color: #0068b7;
        border-radius: 10px;
    }
    .btn-cobaltblue:hover {
        color: #fff;
    }
    .btn-cobaltblue:hover {
        color: #fff;
        background-color: #0174DF;
        border-color: #0174DF;
    }
    .btn-cobaltblue.focus,.btn-cobaltblue:focus{
        color:#fff;
    }
</style>
@endsection