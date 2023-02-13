Vue.component('folder-editor',{
  model: {
    prop: 'loadingCount',
    event: 'input'
  },
  props: ['loadingCount'],
  data() {
    return {
      visible: false,
      editMode: false,
      targetFolder: null,
      folderData: this.initialize(),

      filteredRows: [],
      searchTerm: '',
      searchOption: '0',
      requesting: false,
    }
  },
  methods: {
    openNotificationWithIcon (type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    initialize(){
      return  { 'folder_name' : '' }
    },
    showModal(folder) {
      this.editMode = folder != null
      if(folder){
        this.targetFolder = folder
        this.folderData = _.cloneDeep(folder)
      } else {
        this.folderData = this.initialize()
      }
      this.visible = true
    },
    hideModel(){
      this.visible = false
      this.folderData = this.initialize()
    },
    validate(){
      if(_.get(this.folderData,"folder_name") == ""){
        this.openNotificationWithIcon('error',this.$t('message.no_name'),this.$t('message.tag_folder_name'));
        return false
      }
      return true
    },
    // フォルダ作成時の確定ボタン
    register(e){
      if(this.requesting){
        return
      }
      if(this.validate() != true){
        return;
      }
      var self = this
      self.requesting = true
      this.$emit('input', this.loadingCount + 1)
      axios.post("/tag/folders", this.folderData)
      .then((res)=>{
        self.openNotificationWithIcon("success", this.$t('message.add_folder'));
        self.$emit('completion', res)
        self.hideModel()
      })
      .catch((e)=>{
        if(e.response && self.errorResponse(e.response)){
          return
        }
        self.openNotificationWithIcon('error',this.$t('message.error_ocurred'))
      })
      .finally(() => {
          self.requesting = false
          self.$emit('input', this.loadingCount - 1)
      })
    },
    // フォルダ編集時の確定ボタン
    update(e){
      if(this.requesting){
        return
      }
      if(this.validate() != true){
        return;
      }
      var self = this
      self.requesting = true
      this.$emit('input', this.loadingCount + 1)
      axios.put("/tag/folders/" + this.folderData.id, this.folderData)
      .then((res)=>{
        self.openNotificationWithIcon("success", this.$t('message.update_folder'));
        self.$emit('completion', res)
        self.hideModel()
      })
      .catch((e)=>{
        if(e.response && self.errorResponse(e.response)){
          return
        }
        self.openNotificationWithIcon('error',this.$t('message.error_ocurred'))
      })
      .finally(() => {
          self.requesting = false
          self.$emit('input', this.loadingCount - 1)
      })
    },
    errorResponse(response){
      if(response.data){
        Object.keys(response.data).forEach((key)=>{
          this.openNotificationWithIcon('error',response.data[key])
        })
        return true
      }
      return false
    },
  },
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
          create_mode_title: 'Create New Tag Folder',
          edit_mode_title: 'Update Tag Folder',
          placeholder: 'Enter a name for the new folder.',
          register: 'Register',
          update: 'Update',
          no_name: 'No Name',
          tag_folder_name: 'Tag folder name is required',
          add_folder: 'Add Folder',
          error_ocurred: 'An Error Occurred',
          update_folder: "Update Folder"
        }
      },
      ja: { 
        message: { 
          create_mode_title: '新規フォルダ作成',
          edit_mode_title: 'フォルダ編集',
          placeholder: '名前を入力してください。',
          register: '登録',
          update: '更新',
          no_name: '名前がない',
          tag_folder_name: 'タグフォルダー名が必要',
          add_folder: 'フォルダー追加',
          error_ocurred: 'エラーです',
          update_folder: "フォルダー更新"
        } 
      }
    }
  },
  template:`<div>
    <a-modal
      :visible="visible"
      :footer="null"
      :centered="true"
      v-model="visible"
      width="80%"
    >
      <h3>{{ this.editMode ? $t('message.edit_mode_title') : $t('message.create_mode_title') }}</h3>
      <div class="row p-2 mb-3 divider">
        <div class="col">
        <a-input size="large" :placeholder="$t('message.placeholder')" name="folder_name" v-model="folderData.folder_name" @pressEnter=""/>
        </div>
      </div>
      <div class="p-2">
        <div class="d-flex justify-content-center">            
          <a-button v-if="editMode" class="rounded-green" @click="update">{{ $t('message.update') }}</a-button>
          <a-button v-else class="rounded-green" @click="register">{{ $t('message.register') }}</a-button>
        </div>
      </div>
      <!-- upper row 5 end -->
    </a-modal>
  </div>`
})
