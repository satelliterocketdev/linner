Vue.component('tags-body',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
          custom_order_created: 'Created Date',
          custom_order_updated: 'Updated Date',
          folder_tag: '○○ folder - tag',
          tag_name: 'Tag Name',
          tag_people: 'Number of People',
          tag_actions: 'Action',
          tag_edit_button: 'Edit',
          tag_create_button: 'Create Tag',
          addAction_message: 'Add {msg}',
          removeAction_message: 'Remove {msg}',
          tag_people_format: '{param}',
          }
      },
      ja: { 
        message: { 
          custom_order_created: '作成順',
          custom_order_updated: '更新順',
          folder_tag: 'フォルダ - タグ',
          tag_name: 'タグ名',
          tag_people: 'タグ人数',
          tag_actions: 'アクション',
          tag_edit_button: '編集',
          tag_create_button: '新規タグ',
          addAction_message: '{msg}を追加',
          removeAction_message: '{msg}を除去',
          tag_people_format: '{param}人',
        } 
      }
    },
  },
  template:`
    <div class="mt-4" >
      <a-card v-if="folder !== null">
        <div class="row">
          <div class="col-sm-6 p-0 wordbreak-all">
            <h2>{{ folder.folder_name }}</h2>
          </div>
          <div class="col-sm-6 p-0 d-flex justify-content-end">
            <slot name="createButton"></slot>
          </div>
        </div>
        <div class="row align-items-center m-1 font-size-table">
          <div class="col-1 px-small"><a-checkbox @change="changedAllCheck" :checked="checkAll"></a-checkbox></div>
          <div class='col-3 px-small header-colmun-sortable' @click="sort($event, 'title')"
          :class="{ active: sortKey.field == 'title' }">
            {{ $t('message.tag_name') }}
            <span class="arrow" :class="sortDirection"></span>
          </div>
          <div class='col-2 px-small header-colmun-sortable' @click="sort($event, 'count_tagged_user')"
          :class="{ active: sortKey.field == 'count_tagged_user' }">
            {{ $t('message.tag_people') }}
            <span class="arrow" :class="sortDirection"></span>
          </div>
          <div class='col-4 px-small header-colmun-sortable' @click="sort($event, 'actions')"
          :class="{ active: sortKey.field == 'actions' }">
            {{ $t('message.tag_actions') }}
            <span class="arrow" :class="sortDirection"></span>
          </div>
          <div class="col-2 px-small"></div>
        </div>

        <div v-for="(tag,index) in processedRecords"
          class="row align-items-center mx-0 my-1 border rounded shadow bg-white px-1 py-3 font-size-table" :key="tag.id">
          <div class="col-1 px-small">
            <a-checkbox @change="changedRowCheckbox" :value="tag" :checked="tag.checked" class="mr-1"></a-checkbox>
          </div>
          <div class="col-3 px-small wordbreak-all">{{tag.title}}</div>
          <div class="col-2 px-small wordbreak-all">{{ $t('message.tag_people_format' , {param:tag.count_tagged_user}) }}</div>
          <div class="col-4 px-small wordbreak-all">{{tag.actions}}</div>
          <div class="col-2 px-small wordbreak-all">
            <div class="d-flex justify-content-end">
            <slot name="editButton" :tag="tag"></slot>
            </div>
          </div>
        </div>
      </a-card>
    </div>`,
    props:{
      folder:{
        type: Object,
      },
      initialSortDirection: {
        type: String,
        default: 'asc'
      },
    },
    data() {
      return {
        checkAll: false,
        checkedTags: [],
        filteredRows: [],
        sortKey: {field: 'title', type: 'asc',},
        sortDirection: this.initialSortDirection,
      }
    },
    watch: {
      folder: {
        handler(){
          if (this.folder){
            this.filteredRows = this.takeOverStatus( _.cloneDeep(this.folder.tag_managements))
          } else {
            // folderが渡されない=表示しない
            this.checkAll = false
            this.checkedTags = []
            this.filteredRows = []
          }
        }
      }
    },
    computed: {
      processedRecords(){
        var records = this.filteredRows;

        let sortKey = this.sortKey;
        if (Object.keys(sortKey).length) {
          records = records.sort(function(a,b){
            a = a[sortKey.field]
            b = b[sortKey.field]
            var order = sortKey.type == 'asc' ? 1 : -1

            return (a === b ? 0 : a > b ? 1 : -1) * order
          })
        }

        return records;
      },
    },
    methods:{
      openNotificationWithIcon(type, message, desc) {
        this.$notification[type]({
          message: message,
          description: desc,
        });
      },
      takeOverStatus(newData){
        // 同一のデータ情報が渡された場合、チェック情報を引き継ぐ
        if (newData == null){
          this.checkedTags = []
          return          
        }
        var newCheckedTags = []
        this.checkedTags.forEach((c) =>{
          var checkedObj = newData.find((d)=> c.id == d.id)
          if(checkedObj){
            this.$set(checkedObj, 'checked', true)
            newCheckedTags.push(checkedObj)
          }
        })
        this.checkedTags = newCheckedTags
        if(this.checkAll){
          this.checkAll = (newData.length != 0 && newData.length == newCheckedTags.length)
        }
        this.$emit('change-checked', this.checkedTags)
        return newData
      },
      changedRowCheckbox(event) {
        var tag = event.target.value;
        if (event.target.checked == true) {
          this.checkedTags.push(tag);
          this.$set(tag, 'checked', true)
  
        } else {
          var id = this.checkedTags.indexOf(tag);
          this.checkedTags.splice(id, 1);
          this.$set(tag, 'checked', false)
        }
        this.$emit('change-checked', this.checkedTags)
        this.checkAll = false
      },
      changedAllCheck (e) {
        this.checkAll = e.target.checked;
        this.checkedTags = [];
        if (this.checkAll) {
          // 全件選択
          this.filteredRows.forEach( tag => {
            this.checkedTags.push(tag);
            this.$set(tag, 'checked', true)
          });
        } else {
          this.filteredRows.forEach( tag => {
            this.$set(tag, 'checked', false)
          });
        }
        this.$emit('change-checked', this.checkedTags)
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
    }
})
