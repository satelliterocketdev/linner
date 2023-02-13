
Vue.component('action-modal',{
  i18n: { // `i18n` option, setup locale info for component
    messages: {
      en: { 
        message: { 
          change: 'Change', //Message Target & Message Action
          cancel: 'Cancel',
          finish: 'Finish',
          tag: 'Tag',
          scenario: 'Scenario',
          add_tag: 'Add Tag',
        },
      },
      ja: {
        message: { 
          change: '追加・編集', //Message Target & Message Action
          cancel: 'キャンセル',
          finish: '完了',
          tag: 'タグ',
          scenario: 'シナリオ',
          add_tag: 'タグを追加',
        },
      }
    }
  },
  model: {
      prop: 'loadingCount',
      event: 'input'
  },
  props: {
    disabled: {
      type: Boolean,
    },
    loadingCount: {type: Number}
  },
  created(){
    this.switchComponent('tag')
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
  data(){
    return{
      visible: false,
      loading: false,
      info: {},
      currentComponent: 'messageaction-tags',
      buttonName: this.$i18n.t('message.add_tag')
    }
  },
  methods:{
    openNotificationWithIcon(type, message, desc) {
      this.$notification[type]({
        message: message,
        description: desc,
      });
    },
    switchComponent(componentName)
    {
      switch(componentName) {
        case 'tag':
            this.currentComponent = 'messageaction-tags'; 
            this.buttonName = this.$i18n.t('message.add_tag')
            break;
        case 'scenario':
          this.currentComponent = 'messageaction-scenario'; 
          this.buttonName = this.$i18n.t('message.add_scenario')
          break;
        }
    },
    showModal(data) {
      this.info = _.cloneDeep(data);
      this.visible = true
    },
    cancel(){
      this.visible = false
    },
    confirm(){
      this.$emit('completion',this.info)
      this.cancel()
    }
  },
  template:
  `<div>
    <a-modal :centered="true" v-model="visible" :width="600" :footer="null" :maskClosable="false" :destroyOnClose="true">
    <div class="row justify-content-center pt-4">
      <a-radio-group>
        <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentComponent == 'messageaction-tags' }" v-on:click="switchComponent('tag')">{{$t('message.tag')}}</button>
        <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentComponent == 'messageaction-scenario' }" v-on:click="switchComponent('scenario')">{{$t('message.scenario')}}</button>
        <!-- <a-radio-button v-on:click="switchComponent(2)">Survey</a-radio-button> -->
        <!-- <a-radio-button v-on:click="switchComponent(3)">Menu</a-radio-button> -->
      </a-radio-group>
      </div>
      <hr>
      <div id="messagetarget-content" class="p-2">
          <component ref="comp" :is="currentComponent" :data="info" v-model:loading-count="loadingCountData"></component>
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
