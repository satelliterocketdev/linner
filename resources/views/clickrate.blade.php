@extends('layouts.app')

@section('content')
  <div id="app" v-cloak>
    <loading :visible="loadingCount > 0"></loading>
    <clickrate-item-editor ref="editor" @completion="reload" v-model:loading-count="loadingCount"></clickrate-item-editor>
    <clickrate-detail ref="detail" v-model:loading-count="loadingCount"></clickrate-detail>
    <div class="bg-white rounded p-3">
      <div class="row no-gutters px-1 align-items-center ">
        <div class="col-9 align-items-center justify-content-between">
          <h2>@{{ $t('message.clickrate') }}</h2>
        </div>
        <div class="col-3">
          <div class="row justify-content-end align-items-center">
            <button @click="createItem" class="btn btn-outline-dark mx-1">@{{$t('message.create')}}</button>
          </div>
        </div>
      </div>
      <div class="py-1 py-sm-3 d-flex align-items-center justify-content-between">
        <div></div>
        <button class="btn rounded-red my-1" :disabled="!hasSelected" @click="deleteItems">@{{ $t('message.delete') }}</button>
      </div>
      <div class="d-flex align-items-center font-size-table">
        <div class="mr-1 mr-sm-3">
          <a-checkbox @change="changedAllCheck" :checked="isAllSelected"></a-checkbox>
        </div>
        <div class="flex-fill row no-gutters align-items-center text-center">
          <div class='col-4 col-md-2 header-colmun-sortable' @click="sort($event, 'title')"
              :class="{ active: sortKey.field == 'title' }">
              @{{ $t('message.title') }}
              <span class="arrow" :class="sortDirection"></span>
          </div>
          <div class="col-1 text-center">
            <span>@{{ $t('message.send_count') }}</span>
          </div>
          <div class="col-1 text-center">
            <span>@{{ $t('message.access_count') }}</span>
          </div>
          <div class="col-3 px-1 text-center">
            <span>@{{ $t('message.url') }}</span>
          </div>
          <div class="col-3 px-1 text-center">
            <span>@{{ $t('message.redirect_url') }}</span>
          </div>
          <div class="col-md-2 text-center pc">
            <span>&nbsp;</span>
          </div>
        </div>
      </div>
    </div>
    <div class="justify-content-between align-items-center my-3">
      <div>
        <div v-for="(data,key) in processedRecords" :key="key">
          <clickrate-item-list :item="data" @row-check-changed="changedRowCheckbox" class="bg-white rounded px-2 px-sm-3 mt-3 ">
            <template slot="detailButton" slot-scope="slotProps">
              <!--TODO: Detail （仮） -->
              <button @click="showDetail(slotProps.item)" class="btn btn-info small-text mb-1">@{{$t('message.detail')}}</button>
            </template>
            <template slot="editButton" slot-scope="slotProps">
              <button @click="editItem(slotProps.item)" class="btn btn-success small-text mb-1">@{{$t('message.edit')}}</button>
            </template>
          </clickrate-item-list>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
  <script type='text/javascript' src="{{ asset('js/chart.bundle.js') }}"></script>
  <script type='text/javascript' src="{{ asset('js/chart.js') }}"></script>

  <script src="{{asset('js/components/clickrate/clickrate-item-list.js')}}"></script>
  <script src="{{asset('js/components/clickrate/clickrate-item-editor.js')}}"></script>
  <script src="{{asset('js/components/clickrate/clickrate-detail.js')}}"></script>
  
  <script>
    const messages = {
      en: {
        message: {
          clickrate: 'Click Rate',
          create: 'Create',
          delete: 'Delete',
          edit: 'Edit',
          detail: 'Detail',
          title: 'Title',
          send_count: 'Send',
          access_count: 'Clicked',
          url: 'URL',
          redirect_url: 'Redirect URL',
        }
      },
      ja: {
        message: {
          clickrate: 'クリック分析',
          create: '新規',
          delete: '選択したものを削除',
          edit: '編集',
          detail: '表示',
          title: 'タイトル',
          send_count: 'URL送信数',
          access_count: '訪問数',
          url: 'URL',
          redirect_url: '登録URL',
        }
      }
    }

    const i18n = new VueI18n({
      locale: '{{config('app.locale')}}', 
      messages,
    })

    var app = new Vue({
      i18n,
      el: '#app',
      data: {
        loadingCount: 0,
        visible: true,
        modal_tab_active: 1,
        modal_tab_showall: false,

        data: [],
        filterData: [],
        selected: [],
        isAllSelected: false,

        hasSelected: false,
        sortKey: {field: 'created_at', type: 'desc',},
        sortDirection: 'asc',
        filterNumber: -1,
      },
      watch: {
        data:{
          handler(){
            this.filterData = _.cloneDeep(this.data);
          }
        },
        selected:{
          handler(){
            this.hasSelected = this.selected.length > 0;
          }
        },
      },
      computed: {
        processedRecords(){
          var records = this.filterData;

          var filtering = this.filterNumber;
          if(filtering >= 0 ){
            if(filtering == 0 || filtering == 1){
              records = records.filter((item) => {
                return (item.is_active == filtering)
              });
            }
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
          if(filtering >= 0){
            this.filterData = records;
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
        changedRowCheckbox(event) {
          var item = event.target.value;
          if (event.target.checked == true) {
            this.selected.push(item);
            this.$set(item, 'checked', true)
          } else {
            var id = this.selected.indexOf(item);
            this.selected.splice(id, 1);
            this.$set(item, 'checked', false)
          }
          this.$emit('change-checked', this.selected)
          this.isAllSelected = false
        },
        changedAllCheck (e) {
          this.isAllSelected = e.target.checked;
          this.selected = [];
          if (this.isAllSelected) {
            // 全件選択
            this.filterData.forEach( item => {
              this.selected.push(item);
              this.$set(item, 'checked', true)
            });
          } else {
            this.filterData.forEach( item => {
              this.$set(item, 'checked', false)
            });
          }
          this.$emit('change-checked', this.selected)
        },
        // sort関連
        inverseSortType(type){
          if (type === 'asc') return 'desc';
          return 'asc';
        },
        sort(event, colField){
          if(colField == this.sortKey.field){
            this.sortDirection = this.inverseSortType(this.sortDirection);
          } else {
            this.sortDirection = 'asc'
          }

          this.sortKey = {field: colField, type: this.sortDirection,}
        },
        reload() {
          this.data = [];
          this.filterData = []
          this.selected = []
          this.isAllSelected = false

          let self = this
          this.loadingCount++
          axios.get("clickrate/lists")
          .then(response => {
            self.data = response.data
          })
          .finally(() => this.loadingCount--)
        },
        createItem(){
          this.$refs.editor.showModal();
        },
        editItem(data){
          this.$refs.editor.showModal(data.id);
        },
        deleteItems(){
          let data = {item_ids: this.selected.map((item)=>item.id)}
          let self = this
          this.loadingCount++
          axios.post("clickrate/batch-delete", data)
          .then((res)=>{ 
            self.openNotificationWithIcon("success", 'Deleted')
            self.reload()
          })
          .catch(e=> {
            console.error(e);
            self.openNotificationWithIcon('error','An Error Occurred')
          })
          .finally(() => this.loadingCount--)
        },
        showDetail(data){
          this.$refs.detail.showModal(data.id);
        }
      },
      beforeMount: function() {
          this.reload()
      }
    });
  </script>
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

  /* detail */
  .flex-even {
    flex: 1;
  }
  
  .summary {
    font-size: 1.75rem;
    font-weight: bolder
  }
  
  .font-mediumpurple {
    color: #9370db; 
  }
  
  .chart-container {
    position: relative;
  }

  .chart-container > div.chart-parcent {
    position: absolute;
    top: 30%;
    left: 0;
    right: 0;
    margin: auto;
  }

  div.chart-parcent .percentage {
    font-size: 1.4rem !important;
  }
  
  div.chart-legend li {
    position: relative;
  }

  .modal-tab-scenario > div {
    background-color: #fff;
    color: #4a14ff;
    text-align: center;
    padding: .5rem;
    margin: 0 1rem;
    flex: 1;
    cursor: pointer;
  }

  .modal-tab-scenario > div.active {
    background-color: #4a14ff;
    color: #fff;
  }

  .showmoreorless {
    color: #4a14ff;
    cursor: pointer;
  }

  .chart-legends {
      line-height: 1;
  }

    #chart-click,
    #chart-people {
        width: 100%;
        height: auto;
        }

  @media (max-width: 576px) {
    input[type="text"] {
        font-size: 0.4em !important;
        line-height: 1.2 !important;
        padding: .15rem .4rem;
    }

    .modal-tab-scenario > div {
        margin: 0;
        padding: .25rem;
        font-size: 0.4em !important;
        line-height: 1.2 !important;
    }

    div.chart-parcent .percentage {
        font-size: 1.2rem !important;
        margin-bottom: 0.5em !important;
    }

    .chart-parcent .description {
        font-size: 0.6rem;
    }

    .chart-container > div.chart-parcent {
        top: 10%;
    }
}

</style>
@endsection