const selectRichMenu = {
  template: 
  `<div>
  <h3 class="row">{{$t('message.title')}}</h3>
  <div v-for="(serve, key) in defaults.menu.serves">
      <div class="row p-1"> {{$t('message.rich_menu')}}</div>
      <div class="row p-1">
          <a-select
              style="width: 100%"
              v-model="serve.value"
              :placeholder="$t('message.select_rich_menu')"
          >
              <a-select-option v-for="(menu, menuKey) in menus" :key="menu.title">
                  {{ menu.title }}
              </a-select-option>
          </a-select>
      </div>
      <div class="row justify-content-between align-items-center p-1">
          <select v-model="serve.option" class="custom-select" style="width: 75%">
              <option value="first">{{$t('message.add')}}</option>
              <option value="second">{{$t('message.remove')}}</option>
          </select>
      </div>
  </div>
  </div>`,
  i18n: { // `i18n` option, setup locale info for component
      messages: {
          en: {
              message: {
                  title: 'Rich Menu',
                  select_rich_menu: 'Select Rich Menu',
                  add: 'Display',
                  remove: 'Remove',
                  finish: 'Finish',
                  rich_menu: 'Rich Menu',
              },
          },
          ja: {
              message: {
                  title: 'リッチメニュー設定',
                  select_rich_menu: 'リッチメニュー名を選ぶ',
                  add: '表示する',
                  remove: '外す',
                  finish: '完了',
                  rich_menu: 'リッチメニュー',
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
              menu: {
                  serves: [{
                      value: [],
                      option: 'first',
                  }],
              }
          },
          menus: [],
      }
  },
  mounted() {
        this.reloadTag()
        if (this.data.menu) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
  },
  methods: {
      reloadTag() {
          self = this
          this.$emit('input', this.loadingCount + 1)
          axios.get('richmenu/list')
          .then(function(response){
              self.menus = []
              response.data.richMenuItems.forEach(function(value, index){
                  self.menus.push({
                      id: value.id,
                      title: value.title,
                  })
              })
          })
          .finally(() => this.$emit('input', this.loadingCount - 1))
      },
  }
};

Vue.component('add-richmenu-modal',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
            change: 'Change', //Message Target & Message Action
            cancel: 'Cancel',
            finish: 'Finish',
            add_rich_menu: 'Add Rich Menu',
            create_rich_menu: 'Create Rich Menu',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        },
      },
      ja: {
        message: { 
            change: '追加・編集', //Message Target & Message Action
            cancel: 'キャンセル',
            finish: '完了',
            add_rich_menu: 'メニュー追加',
            create_rich_menu: 'リッチメニュー作成',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        },
      }
    }
  },
  components :{
    'select-rich-menu' : selectRichMenu
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
      let serves = this.info.menu.serves;
      var remove = serves.filter(serve => serve.option == "second")
      if (remove.length > 0)
        remove = remove[0]['value']
      
      var add = serves.filter(serve => serve.option == "first")
      if (add.length > 0)
        add = add[0]['value']

      let data = { followerIds: this.getTarget(), 
        removeMenu : remove,
        addMenu : add,
      }

      this.loading = true
      this.$emit('input', this.loadingCount + 1)
      axios.post("follower/add-rich-menu", data)
      .then(res=> this.done())
      .catch(e=> this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
      .finally(() => {
          this.loading = false
          this.$emit('input', this.loadingCount - 1)
      })
    },
    goRichMenu(){
      window.location.href = '/richmenu';
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
  <button type="button" @click="showModal" class="btn rounded-white" :disabled=disabled>{{ $t('message.add_rich_menu') }}</button>
  <a-modal :centered="true" v-model="visible" :width="600" :footer="null" :maskClosable="false" :destroyOnClose="true">
      <div id="messagetarget-content" class="p-2">
          <select-rich-menu :data="info" v-model:loading-count="loadingCountData"></select-rich-menu>
      </div>
      <div class="d-flex justify-content-center m-2">
        <div class="">
          <button class="pl-5 pr-5 btn mx-1 btn-info small-text" @click="goRichMenu" >{{ $t('message.create_rich_menu') }}</button>
        </div>
      </div>
      <div class="footer mt-4">
          <div class="row justify-content-center">
              <button type="button" class="btn m-1 rounded-red" @click="cancel"> {{$t('message.cancel')}} </button>
              <button type="button" class="btn m-1 rounded-green" @click="confirm"> {{$t('message.finish')}} </button>
          </div>
      </div>
  </a-modal>
  </div>`,
});

