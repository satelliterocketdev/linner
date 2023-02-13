Vue.component('tags-folder',{
  props:{
    folders: {
      type: Array,
    }
  },
  data(){
    return{
      orderKey: "0",
      selectedFolder: null, // 選択されたフォルダ
      checkedFolders: [], // チェックボックス用

      filteredRows: [],
      searchTerm: '',
    }
  },
  watch: {
    folders:{
      handler(){
        this.filteredRows = this.takeOverStatus( _.cloneDeep(this.folders))
      }
    },
  },
  computed: {
    processedRecords(){
      var records = this.filteredRows;
      // filter
      var term = this.searchTerm && this.searchTerm.toLowerCase();
      if(term){
        records = records.filter((r) => {
          return r.folder_name.toLowerCase().indexOf(term) !== -1;
        });
      }
      // sort
      switch(this.orderKey){
        case '0':// 作成順
          records.sort(function(a,b){
            if(a.created_at < b.created_at) return -1;
            if(a.created_at > b.created_at) return 1;
            return 0;
          });
          break;
        case '1':// 名前順
          records.sort(function(a,b){
            if(a.folder_name < b.folder_name) return -1;
            if(a.folder_name > b.folder_name) return 1;
            return 0;
          });
          break;
        case '2':// 更新順
          records.sort(function(a,b){
            if(a.updated_at < b.updated_at) return -1;
            if(a.updated_at > b.updated_at) return 1;
            return 0;
          });
          break;
        default:
      }
      if(term){
        this.filteredRows = records;
      }
      return records;
    }

  },
  methods:{
    openNotificationWithIcon (type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    takeOverStatus(newData){
      // 同一のデータ情報が渡された場合、チェック情報、選択情報を引き継ぐ
      if (newData == null){
        this.checkedTags = []
        return          
      }
      var newCheckedTags = []
      this.checkedFolders.forEach((c) =>{
        var checkedObj = newData.find((d)=> c.id == d.id)
        if(checkedObj){
          this.$set(checkedObj, 'checked', true)
          newCheckedTags.push(checkedObj)
        }
      })
      this.checkedFolders = newCheckedTags
      this.$emit('change-checked', this.checkedFolders)

      if(this.selectedFolder){
        var selectedObj = newData.find((d)=> this.selectedFolder.id == d.id)
        if(selectedObj === undefined){
          selectedObj = null
        }
        this.changedFolder(selectedObj)
      }
      return newData
    },
    checkedFolder(e) {
      var folder = e.target.value;
      if (e.target.checked == true) {
        this.checkedFolders.push(folder);
        this.$set(folder, 'checked', true)

      } else {
        var idx = this.checkedFolders.indexOf(folder);
        this.checkedFolders.splice(idx, 1);
        this.$set(folder, 'checked', false)
      }
      this.$emit('change-checked', this.checkedFolders)
      // console.log(this.checkedFolders.map((s)=>{return s.folder_name}));
    },
    changedFolder(folder){
      this.selectedFolder = folder;
      this.$emit('change-folder', folder)
    },
    // search
    search(value, event){
      // 選択済み解除
      this.checkedFolders = [];
      this.changedFolder(null)

      // データ初期化
      this.filteredRows = _.cloneDeep(this.folders)
      this.searchTerm = value; 
    },
  },
  mounted(){
    new SimpleBar(document.getElementById('view-scroll'))
  },
  i18n: { // `i18n` option, setup locale info for component
  messages: {
    en: { 
      message: { 
        custom_order_created: 'Created Date',
        custom_order_updated: 'Updated Date',
        custom_order_name: 'Name',
        search_placeholder: 'search',
      }
    },
    ja: { 
      message: { 
        custom_order_created: '作成順',
        custom_order_updated: '更新順',
        custom_order_name: '名前順',
        search_placeholder: 'キーワード検索',
      } 
    }
  },
},
  template:`
  <div class="mt-4" >
    <a-card :bordered="true">
      <div class="row align-items-center">
        <div class="col-12 col-lg-5">
          <h2>{{ $t('message.folder') }}</h2>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-1 mb-sm-0">
            <a-input-search :placeholder="$t('message.search_placeholder')" @search="search"/>
        </div>
        <div class="col-6 col-sm-3 col-lg-2 justify-content-center mb-1 mb-sm-0">
          <a-select style="width:120px" v-model="orderKey" >
            <a-select-option value="0">{{ $t('message.custom_order_created') }}</a-select-option>
            <a-select-option value="1">{{ $t('message.custom_order_name') }}</a-select-option>
            <a-select-option value="2">{{ $t('message.custom_order_updated') }}</a-select-option>
          </a-select>
        </div>
        <div class="col-6 col-sm-3 col-lg-2 text-right justify-content-end mb-1 mb-sm-0">
          <slot　name="createButton">
          </slot>
        </div>
      </div>
      <div id="view-scroll" class="py-3 px-0">
        <div class="row">
          <div v-for="(folder,key) in processedRecords" :key="folder.id" class="selective-folder d-flex flex-column col-4 col-md-3 col-lg-2 align-items-center px-2 mb-2" :class="{ active: folder == selectedFolder }">
            <div>
              <a-checkbox :disabled="folder.system_folder !== 0" @change="checkedFolder" :value="folder" :checked="folder.checked"></a-checkbox>
              <div class="rounded folder-image" @click="changedFolder(folder)">
                <img src="/img/tag_file.png">
              </div>
            </div>
            <div style="line-height:1.1">
              <span class="mr-auto wordbreak-all" style="font-size:0.8rem;">{{folder.folder_name}}</span>
            </div>
            <div v-if="folder.system_folder === 0" class="mt-1">
              <slot　name="editButton" :folder="folder">
              </slot>
            </div>
          </div>
        </div>
      </div>
    </a-card>
  </div>`,

})