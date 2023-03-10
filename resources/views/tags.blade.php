@extends('layouts.app')

@section('content')
<div id="tags" v-cloak>
  <loading :visible="loadingCount > 0"></loading>
  <a-card :bordered="false">
    <div class="row" id="head">
      <div class="col-sm-6">
        <h2>@{{ $t('message.tags') }}</h2>
      </div>
    </div>
    <!-- end top -->
    <div class="row d-flex justify-content-end">
      <button class="btn rounded-red m-1" :disabled="!hasChecked.general" @click="dialogVisible = true">@{{ $t('message.delete') }}</button>
      <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
        <p>@{{ $t('message.confirm_text') }}</p>
      </a-modal>
    </div>
  </a-card>
  <tags-folder ref="folderList" :folders="dataSource" @change-folder="changeFolder" @change-checked="changeFolderChecked">
    <template slot="createButton">
       <a-button type="default" size="large" class="px-2" @click="createFolder">@{{ $t('message.new_folder') }}</a-button>
    </template>
    <template slot="editButton" slot-scope="slotProps">
      <a-button size="small" class="px-1" @click="editFolder(slotProps.folder)">@{{ $t('message.edit')}}</a-button>
    </template>
  </tags-folder>
  <folder-editor ref="editor" @completion="editCompletion" v-model:loading-count="loadingCount"></folder-editor>

  <tags-body ref="tagList" :folder="currentFolder" @change-checked="changeTagChecked">
    <template slot="createButton">
       <a-button type="default" size="large" class="mt-1 px-2" @click="createTag($event, currentFolder)">@{{ $t('message.create_new_tag') }}</a-button>
    </template>
    <template slot="editButton" slot-scope="slotProps">
      <button size="small" class="btn mx-1 btn-success small-text font-size-table" @click="editTag($event, slotProps.tag)">@{{ $t('message.edit')}}</button>
    </template>
  </tags-body>
  <tag-editor ref="tagEditor" @completion="editCompletion" :folders="dataSource" :default-folder-id="defaultFolderId" v-model:loading-count="loadingCount"></tag-editor>
</div>
@endsection

@section('footer-scripts')
<!-- magazine -->
<script src="{{ asset('js/components/magazine/custom-components/prepend-message-target-selection.js') }}"></script>
<script src="{{ asset('js/components/magazine/message-action/messageaction-tag.js') }}"></script>
<script src="{{ asset('js/components/magazine/message-action/messageaction-scenario.js') }}"></script>

<!-- modal -->
<script src="{{ asset('js/components/tags/action-modal.js') }}"></script>
<script src="{{ asset('js/components/tags/folder-editor.js') }}"></script>
<script src="{{ asset('js/components/tags/tag-editor.js') }}"></script>
<!-- content -->
<script src="{{ asset('js/components/tags/tagsfolder.js') }}"></script>
<script src="{{ asset('js/components/tags/tagsbody.js') }}"></script>
<script>

//TODO ???????????? resources/lang/tags.php????????????????????????
const messages = {
    en: {
        message: {
            tags: 'Tags',
            folder: 'Folder',
            folder_tag: '?????? folder - tag',
            create_new_tag: 'New Tag',
            edit: 'Edit',
            analysis: 'Analysis',
            send_message: 'Send Message',
            send_senario: 'Send Senario',
            send_survey: 'Send Survey',
            delete: 'Delete',
            tag_name: 'Tag Name',
            amount_of_people: 'Amount of people',
            action: 'Action',
            csv_import: 'Import CSV',
            csv_export: 'Export CSV',
            add_friend: 'Add friend',
            search: 'Search',
            name: 'Name',
            registered: 'Registered',
            scenario: 'Scenario',
            other_tag: 'Other tag',
            source: 'Source',
            aut_add_tag: 'Conditions for automatically adding tag',
            registed_data: 'Registered date',
            survey: 'Survey',
            add_action: 'Add Action',
            menu: 'Menu',
            add_limit_of_users: 'Add limit to amount of users per tag',
            add_no_restriction_number_users: 'Check to indicate no restriction to number of users',
            register: 'Register',
            update: 'Update',
            folder_management: 'Folder Management',
            new_folder: 'New Folder',
            confirm_title: 'Confirm',
            confirm_text: 'Are you sure?'
        }
    },
    ja: {
        message: {
            tags: '????????????',
            folder: '????????????',
            folder_tag: '???????????? - ??????',
            create_new_tag: '????????????',
            edit: '??????',
            analysis: '??????',
            send_message: '????????????',
            send_senario: '??????????????????',
            send_survey: '???????????????',
            delete: '???????????????????????????',
            tag_name: '??????',
            amount_of_people: '??????????????????',
            action: '???????????????',
            csv_import: 'CSV???????????????',
            csv_export: 'CSV??????????????????',
            add_friend: '???????????????',
            search: '??????',
            name: '??????',
            registered: '?????????',
            scenario: '????????????',
            other_tag: '?????????',
            source: '?????????',
            aut_add_tag: '??????????????????',
            registed_data: '????????????',
            survey: '???????????????',
            add_action: '????????????????????????',
            menu: '????????????',
            add_limit_of_users: '??????????????????',
            add_no_restriction_number_users: '????????????',
            register: '??????',
            update: '??????',
            folder_management: '??????????????????',
            new_folder: '??????????????????',
            confirm_title: '??????',
            confirm_text: '???????????????????????????????????????'
        }
    }
}
const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', // locale form config/app.php
    messages, // set locale messages
})

Vue.config.devtools = true
var tags = new Vue({
  i18n,
  el: '#tags',
  data(){
    return{
      loadingCount: 0,
      dataSource: null, // folder-tag
      currentFolder: null,
      hasChecked : {general: false, folder:false, tag:false },
      defaultFolderId: -1,
      dialogVisible: false,
      confirmLoading: false
    }
  },
  watch:{
    dataSource:{
      handler(){
        if(this.dataSource==null){
          return
        }
        
        this.defaultFolderId = -1
        let defaultFolder = this.dataSource.find((f)=> f.system_folder == 1)
        if(defaultFolder){
          this.defaultFolderId = defaultFolder.id
        }
      },
      immediate: true,
    },
  },
  methods: {
    openNotificationWithIcon (type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    changeFolder(folder){
      var current = null
      if(folder){
        current = this.dataSource.find((d)=> d.id == folder.id )
        if(current === undefined){
          current = null
        }
      }
      // ?????????????????????????????????root?????????????????????????????????????????????????????????????????????
      this.changeTagChecked(null)
      this.currentFolder = current
    },
    // ????????????????????????????????????????????????????????????????????????????????????
    getData(){
      self = this
      this.loadingCount++
      axios.get("/tag/folders?with=tags")
      .then(res=> self.dataSource = res.data)
      .catch(e=> self.openNotificationWithIcon('error','An Error Occurred'))
      .finally(() => this.loadingCount--)
    },
    createFolder(){
      this.$refs.editor.showModal()
    },
    editFolder(folder){
      this.$refs.editor.showModal(folder)
    },
    editCompletion(){
      this.getData()
    },
    createTag(el, folder){
      this.$refs.tagEditor.showModal(null, folder.id)
    },
    editTag(el, tag){
      this.$refs.tagEditor.showModal(tag.id)
    },
    changeCheckd(key, val){
      this.hasChecked[key] = val
      this.hasChecked.general = (this.hasChecked.folder > 0 || this.hasChecked.tag > 0)
    },
    changeFolderChecked(checkedFolder){
      this.changeCheckd('folder', (checkedFolder!=null && checkedFolder.length > 0))
    },
    changeTagChecked(checkedTag){
      this.changeCheckd('tag', (checkedTag!=null && checkedTag.length > 0))
    },
    deleteFolderAndTag(){
      var folders = this.$refs.folderList.$data.checkedFolders
      var tags = this.$refs.tagList.$data.checkedTags
      let data = {folder_ids: folders.map((f)=>f.id), tag_ids: tags.map((t)=>t.id)}
      self = this
      this.loadingCount++
      axios.post("tag/batch-delete", data)
      .then((res)=>{ 
        // self.openNotificationWithIcon("success")
        self.getData()
        self.confirmLoading = false
        self.handleCancel()
      })
      .catch(e=> {
        console.error(e);
        self.openNotificationWithIcon('error','An Error Occurred')
      })
      .finally(() => this.loadingCount--)
    },
    handleOk() {
      this.confirmLoading = true
      this.deleteFolderAndTag()
    },
    handleCancel() {
      this.dialogVisible = false
    },
  },
  beforeMount(){
    // call axios
    this.getData();
  },
})
</script>
@endsection


@section('css-styles')
<style>
  .folder-image img{
    height: auto;
    max-width: 100px;
    width: 100%;
  }
  .selective-folder {
    background-color: #fff;
  }
  .selective-folder .folder-image{
    border: 1px solid transparent;
  }

 .selective-folder.active .folder-image{
    border: 1px solid #40a9ff;
  }

.selective-folder button {
    padding: 0 7px !important;
    font-size: 0.8rem;
}

.selective-folder:hover {
    cursor: pointer;
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
    border-bottom: 4px solid rgba(0,0,0,.65);
  }

  .arrow.desc {
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid rgba(0,0,0,.65);
  }

  .header-colmun-sortable {
  }

  @media (max-width: 576px) {
    .ant-checkbox-inner {
        width: 100%;
        height: auto;
        min-width: 14px;
        min-height: 14px;
    }
  }
</style>
@endsection