Vue.component('tag-editor',{
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props:{
    // フォルダ選択肢
    folders:{
      type : Array,
    },
    // 
    defaultFolderId:{
      type: Number,
    },
    loadingCount: {
        type: Number,
        default: 0
    }
  },
  data() {
    return {
      visible: false,
      editMode: false,
      tagData: this.initialize(),

      filteredRows: [],
      searchTerm: '',
      searchOption: '0',
      loading : false,
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
    initialize(initFolderId){
      return  {
        title: '',
        limit: 1,

        tag_folder_id: initFolderId != null? initFolderId : this.defaultFolderId,
        no_limit: true,
        actions: {
            tags: {
                serves: [{
                    value: [],
                    option: 'first'
                }],
                excludes: [{
                    value: [],
                    option: 'first'
                }]
            },
            scenarios: {
              serves: [{
                  value: [],
                  option: 'first',
                  delivery: {
                      timing: '',
                      number: '',
                  },
              }],
            }
        },
      }
    },
    convertActions(tag){
      //tagDataのフォーマットに書き換える
      var res = {
        id: tag.id,
        title: tag.title,
        tag_folder_id: tag.tag_folder_id,
        no_limit: tag.no_limit == 1,
        limit: tag.limit,
        actions: {
            tags: {
              serves: [],
            },
            scenarios: {
              serves: [],
            }
        },
      }
      // actionsの展開
      tag.formatted_tag_actions.tag.forEach((serve, index) => {
        res.actions.tags.serves.push({option: serve.option , value: serve.value})
      })

      tag.formatted_tag_actions.scenario.forEach((serve, index) => {
        res.actions.scenarios.serves.push(
          {
            option: serve.option ,
            value: serve.value ,
            delivery: {
              timing: '',
              number: '',
            }
          }
        )
      })
      if(res.actions.tags.serves.length == 0){
        res.actions.tags.serves = [ {value: [], option: 'first'} ]
      }
      if(res.actions.scenarios.serves.length == 0){
        res.actions.scenarios.serves = [
          { value: [],
            option: 'first',
            delivery: {
              timing: '',
              number: '',
            }
          }
        ]
      }
      return res
    },
    getData(tagId){
      // 改めてタグ情報を取得する
      var self = this
      self.loading = true
      this.$emit('input', this.loadingCount + 1)
      axios.get("/tag/info/"+tagId)
      .then(res=> {
        self.tagData = this.convertActions(res.data[0])
      })
      .catch(e=> self.openNotificationWithIcon('error', e.message))
      .finally(() => {
          self.loading = false
          self.$emit('input', this.loadingCount - 1)
      })
    },
    showModal(tagId, initFolderId) {
      this.editMode = tagId != null
      this.tagData = this.initialize(initFolderId)

      if(tagId){
        this.getData(tagId)
      }

      this.visible = true
    },
    hideModel(){
      this.visible = false
      this.tagData = this.initialize()
    },
    createFolder() {
      this.$parent.createFolder()
    },
    validate(){
      if(_.get(this.tagData,"title") == ""){
        this.openNotificationWithIcon('error',this.$t('message.no_name'),this.$t('message.tag_name'));
        return false
      }
      return true
    },
    // タグ作成時の確定ボタン
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
      axios.post("/tag", this.tagData)
      .then((res)=>{
        self.openNotificationWithIcon("success", this.$t('message.add_tag'));
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
    // タグ編集時の確定ボタン
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
      axios.put("/tag/" + this.tagData.id, this.tagData)
      .then((res)=>{
        self.openNotificationWithIcon("success", this.$t('message.update_tag'));
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
    changeCapacityNolimit(e){
      this.tagData.no_limit = e.target.checked 
    },
    showReactionModal(){
      this.$refs.reactionModal.showModal(this.tagData.actions)
    },
    updateAction(info){
      this.tagData.actions = info
    },
  },
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
          create_mode_title: 'Create New Tag',
          edit_mode_title: 'Update Tag',
          placeholder: 'Enter a name for the new Tag.',
          register: 'Register',
          update: 'Update',
          select_folder: 'Select Folder',
          parent_folder: 'Folder',
          create_folder: 'Create Folder',
          user_capacity: 'User Capacity',
          user_capacity_nolimit: 'Unlimited',
          user_capacity_supplement: '',
          add_action: 'Add Reaction',
          setting_reaction: 'Setting Reaction',
          addAction_message: 'Add {msg}',
          removeAction_message: 'Remove {msg}',
          no_name: 'No Name',
          tag_name: 'Tag name is required',
          add_tag: 'Add tag',
          error_ocurred: 'An Error Occurred',
          update_tag: "Update Tag"
        }
      },
      ja: { 
        message: { 
          create_mode_title: '新規タグ作成',
          edit_mode_title: 'タグ編集',
          placeholder: '名前を入力してください。',
          register: '登録',
          update: '更新',
          select_folder: 'フォルダ名を選ぶ', 
          parent_folder: '管理フォルダ',
          create_folder: '新規フォルダ',
          user_capacity: '登録人数制限',
          user_capacity_nolimit: '制限なし',
          user_capacity_supplement: '人まで',
          add_action: 'リアクションを追加',
          setting_reaction: 'リアクション設定',
          addAction_message: '{msg}を追加',
          removeAction_message: '{msg}を除去',
          no_name: '名前がない',
          tag_folder_name: 'タグ名が必要',
          add_tag: 'タグー追加',
          error_ocurred: 'エラーです',
          update_tag: "タグー更新"
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
    >
      <h3>{{ this.editMode ? $t('message.edit_mode_title') : $t('message.create_mode_title') }}</h3>
      <div v-if="loading">Loading...</div>
      <div v-else>
        <div class="row p-2 mb-3 divider">
          <div class="col">
          <a-input size="large" :placeholder="$t('message.placeholder')" name="title" v-model="tagData.title" @pressEnter=""/>
          </div>
        </div>
        <hr>
        <div class="row mx-1 my-2 align-items-center">
          <div class="col-12 col-md-3">{{ $t('message.parent_folder') }}</div>
          <div class="col-12 col-md-9">
            <div class="flex-fill mb-1">
              <a-select
                style="width:100%;"
                v-model="tagData.tag_folder_id"
                :placeholder="$t('message.select_folder')">
                <a-select-option v-for="folder in folders" :key="folder.id" :value="folder.id">{{ folder.folder_name }}</a-select-option>
              </a-select>
            </div>
            <div>
              <a-button type="default" class="px-2" @click="createFolder">{{ $t('message.create_folder') }}</a-button>
            </div>
          </div>
        </div>

        <div class="row mx-1 my-2 align-items-center">
          <div class="col-12 col-md-3">{{ $t('message.user_capacity' )}}</div>
          <div class="col-12 col-md-9">
            <a-input-number :min="1" v-model="tagData.limit" :disabled="tagData.no_limit" style="width:100px;" />
            <span class="mx-2">{{ $t('message.user_capacity_supplement') }} </span>
            <a-checkbox class="ml-3" @change="changeCapacityNolimit" :checked="tagData.no_limit">{{ $t('message.user_capacity_nolimit') }}</a-checkbox>
          </div>
        </div>
        <div class="row mx-1 my-2 align-items-center">
          <div class="col-12 col-md-3">{{ $t('message.add_action') }}</div>
          <div class="col-12 col-md-9">
            <a-button @click="showReactionModal">{{$t('message.setting_reaction')}}</a-button>
            <action-modal ref="reactionModal" @completion="updateAction"></action-modal>
            <div>
              <span v-for="(serves,i) in tagData.actions.tags.serves" :key="'tag-'+i">
                <span v-for="(val, j) in serves.value" :key="'tag-'+i+'-'+j">
                  <span v-if="'tag-'+i+'-'+j!='tag-'+0+'-'+0">&nbsp; : &nbsp;</span>
                  <template v-if="serves.option == 'first'">
                  {{ $t('message.addAction_message', { msg: val }) }}
                  </template>
                  <template v-if="serves.option == 'second'">              
                  {{ $t('message.removeAction_message', { msg: val }) }}
                  </template>
                </span>
              </span>
            </div>
            <div>
              <span v-for="(serves,i) in tagData.actions.scenarios.serves" :key="'sce-'+i">
                <span v-for="(val, j) in serves.value" :key="'sce-'+i+'-'+j">
                  <span v-if="'sce-'+i+'-'+j!='sce-'+0+'-'+0">&nbsp; : &nbsp;</span>
                  <template v-if="serves.option == 'first'">
                  {{ $t('message.addAction_message', { msg: val }) }}
                  </template>
                  <template v-if="serves.option == 'second'">              
                  {{ $t('message.removeAction_message', { msg: val }) }}
                  </template>
                </span>
              </span>
            </div>
          </div>
        </div>

        <div class="p-2">
          <div class="d-flex justify-content-center">            
            <a-button v-if="editMode" class="rounded-green" @click="update">{{ $t('message.update') }}</a-button>
            <a-button v-else class="rounded-green" @click="register">{{ $t('message.register') }}</a-button>
          </div>
        </div>
      </div>
    </a-modal>
  </div>`
})
