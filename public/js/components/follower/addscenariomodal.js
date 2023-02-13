/** magazineのmessageaction-scenarioのtemplateをベースに  */
const selectScenarios = {
  template: 
  `<div>
  <h3 class="row">{{$t('message.title')}}</h3>
  <div v-for="(serve, key) in defaults.scenarios.serves">
      <div class="row p-1"> {{$t('message.scenario')}}</div>
      <div class="row p-1">
          <a-select
              mode="multiple"
              style="width: 100%; min-width: 180px"
              v-model="serve.value"
              :placeholder="$t('message.select_scenario')"
          >
              <a-select-option v-for="(scenario, scenarioKey) in scenarios" :key="scenario.title">
                  {{ scenario.title }}
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
                  title: 'Scenario',
                  select_scenario: 'Select Scenario',
                  add: 'Add',
                  remove: 'Remove',
                  finish: 'Finish',
                  scenario: 'Scenario'
              },
          },
          ja: {
              message: {
                  title: 'シナリオ設定',
                  select_scenario: 'シナリオ名を選ぶ',
                  add: '追加する',
                  remove: '外す',
                  finish: '完了',
                  scenario: 'シナリオ'
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
              scenarios: {
                  serves: [{
                      value: [],
                      option: 'first',
                  }],
              }
          },
          scenarios: [],
      }
  },
  mounted() {
        this.reloadTag()
        if (this.data.scenarios) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
  },
  methods: {
      reloadTag() {
          self = this
          this.$emit('input', this.loadingCount + 1)
          axios.get('stepmail/lists')
          .then(function(response){
              self.scenarios = []
              response.data.forEach(function(value, index){
                  self.scenarios.push({
                      id: value.id,
                      title: value.name,
                  })
              })
          })
          .finally(() => this.$emit('input', this.loadingCount - 1))
      },
  }
};

Vue.component('add-scenario-modal',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
            change: 'Change', //Message Target & Message Action
            cancel: 'Cancel',
            finish: 'Finish',
            add_scenario: 'Add Scenario',
            getting_data: "Getting Data",
            fetch_fail: "Fail to fetch data"
        },
      },
      ja: {
        message: { 
            change: '追加・編集', //Message Target & Message Action
            cancel: 'キャンセル',
            finish: '完了',
            add_scenario: 'シナリオ追加',
            getting_data: "データの取得",
            fetch_fail: "データの取得に失敗しました。"
        },
      }
    }
  },
  components :{
    'select-scenarios' : selectScenarios
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
      let serves = this.info.scenarios.serves;
      var remove = serves.filter(serve => serve.option == "second")
      if (remove.length > 0)
        remove = remove[0]['value']
      
      var add = serves.filter(serve => serve.option == "first")
      if (add.length > 0)
        add = add[0]['value']

      let data = { followerIds: this.getTarget(), 
        removeScenarios : remove,
        addScenarios : add,
      }

      this.loading = true
      this.$emit('input', this.loadingCount + 1)
      axios.post("follower/add-scenarios", data)
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
  <button type="button" @click="showModal" class="btn rounded-white" :disabled=disabled>{{ $t('message.add_scenario') }}</button>
  <a-modal :centered="true" v-model="visible" :width="600" :footer="null" :maskClosable="false" :destroyOnClose="true">
      <div id="messagetarget-content" class="p-2">
          <select-scenarios :data="info" v-model:loading-count="loadingCountData"></select-scenarios>
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

