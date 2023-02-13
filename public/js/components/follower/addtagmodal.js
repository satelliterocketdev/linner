/** magazineのmessageaction-tagsのtemplateをベースに  */
const selectTags = {
  template: 
  `<div>
  <h3 class="row">{{$t('message.tag')}}</h3>
  <div v-for="(serve, key) in defaults.tags.serves">
      <div class="row p-1"> {{$t('message.tags')}}</div>
      <div class="row p-1">
          <a-select
              mode="multiple"
              style="width: 100%"
              v-model="serve.value"
              :placeholder="$t('message.select_tag')"
          >
            <a-select-option v-for="(tag, tagKey) in tags" :key="tag.title">
              {{ tag.title }}
            </a-select-option>
        </a-select>
      </div>
      <div class="row justify-content-between align-items-center p-1">
          <select class="custom-select" style="width: 75%" v-model="serve.option">
              <option value="first">{{$t('message.add')}}</option>
              <option value="second">{{$t('message.remove')}}</option>
          </select>
      </div>
  </div>
  <h3 class="pt-2 row">{{$t('message.new_tag')}}</h3>
  <div class="row justify-content-between align-items-center">
      {{$t('message.tag_name')}}
      <input type="text" class="form-control p-1" @keyup.enter="addTag">
  </div>
  </div>`,
  i18n: { // `i18n` option, setup locale info for component
      messages: {
          en: {
              message: {
                  tag: 'Tag',
                  select_tag: 'Select Tag',
                  add: 'Add',
                  remove: 'Remove',
                  tag_name: 'Tag name',
                  new_tag: 'New Tag',
                  tags: 'Tags'
              },
          },
          ja: {
              message: {
                  tag: 'タグ設定',
                  select_tag: 'タグ名を選ぶ',
                  add: '追加する',
                  remove: '外す',
                  tag_name: 'タグ名',
                  new_tag: '新しいタグを作成',
                  tags: 'タグ'
              }
          }
      }
  },
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props: ['data', 'loadingCount'],
  data() {
      return {
          defaults: {
              tags: {
                  serves: [{
                      value: [],
                      option: 'first'
                  }],
                  excludes: [{
                      value: [],
                      option: 'first'
                  }]
              }
          },
          tags: [],
      }
  },
  mounted() {
      this.reloadTag()
      if (this.data.tags) {
          Object.assign(this.defaults, this.data)
      } else {
          Object.assign(this.data, this.defaults)
      }
  },
  methods: {
      addTag(el) {
          self = this
          this.$emit('input', this.loadingCount + 1)
          axios.post('tag', {
              title: el.target.value,
              followerslist: [],
              condition: "",
              no_limit: true,
              action: "",
              limit: 1,
          })
          .then(function(response){
              el.target.value = ''
              self.reloadTag()
          })
          .catch((e) => {
              console.log(e)
          })
          .finally(() => this.$emit('input', this.loadingCount - 1))
      },
      reloadTag() {
          self = this
          this.$emit('input', this.loadingCount + 1)
          axios.get('tag/list')
          .then(function(response){
              self.tags = []
              response.data.tags.forEach(function(value, index){
                  self.tags.push({
                      id: value.id,
                      title: value.title,
                  })
              })
          })
          .finally(() => this.$emit('input', this.loadingCount - 1))
      },
  }
};

Vue.component('add-tag-modal',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
            change: 'Change', //Message Target & Message Action
            cancel: 'Cancel',
            finish: 'Finish',
            tag: 'Tag',
            add_tag: 'Add Tag',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        },
      },
      ja: {
        message: { 
            change: '追加・編集', //Message Target & Message Action
            cancel: 'キャンセル',
            finish: '完了',
            tag: 'タグ',
            add_tag: 'タグ追加',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        },
      }
    }
  },
  components :{
    'select-tags' : selectTags
  },
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props: {
    targets: {
      type: Array,
      required: true,
    },
    disabled: {
      type: Boolean,
    },
    loadingCount: {
        type: Number,
        default: 0
    }
  },
  data(){
    return{
      visible: false,
      loading: false,
      info: [],
    }
  },
  methods:{
    openNotificationWithIcon(type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    showModal() {
      this.visible = true
    },
    confirm(){
      this.updateInfo()
    },
    getTarget(){
      return this.targets.map( follower => {
        return follower.id
      })
    },
    updateInfo(){
      let serves = this.info.tags.serves;
      var remove = serves.filter(serve => serve.option == "second")
      if (remove.length > 0)
        remove = remove[0]['value']
      
      var add = serves.filter(serve => serve.option == "first")
      if (add.length > 0)
        add = add[0]['value']

      let data = { followerIds: this.getTarget(), 
        removeTags : remove,
        addTags : add,
      }

      this.loading = true
      this.$emit('input', this.loadingCount + 1)
      axios.post("follower/add-tags", data)
      .then(res=> this.done())
      .catch(e=> this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
      .finally(() => {
          this.loading = false
          this.$emit('input', this.loadingCount - 1)
      })
    },
    cancel(){
      this.info = []
      this.visible = false
    },
    done(){
      this.$emit('updated')
      this.cancel()
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
  template:
  `<div>
  <button type="button" @click="showModal" class="btn rounded-white" :disabled=disabled>{{ $t('message.add_tag') }}</button>
  <a-modal :centered="true" v-model="visible" :width="600" :footer="null" :maskClosable="false" :destroyOnClose="true">
      <div id="messagetarget-content" class="p-2">
          <select-tags :data="info" v-model:loading-count="loadingCountData"></select-tags>
      </div>
      <div class="footer pt-4">
          <div class="row justify-content-center">
              <button type="button" class="btn m-1 rounded-red" @click="cancel"> {{$t('message.cancel')}} </button>
              <button type="button" class="btn m-1 rounded-green" @click="confirm"> {{$t('message.finish')}} </button>
          </div>
      </div>
  </a-modal>
  </div>`,
});

