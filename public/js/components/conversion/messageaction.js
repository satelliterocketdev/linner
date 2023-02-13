Vue.component ('message-action', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn rounded-white disabled">{{$t('message.select_action')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null" :destroyOnClose="true" :afterClose="updateOnClose">
        <div class="row justify-content-center pt-4">
            <button type="button" class="btn m-1 rounded-blue" v-on:click="switchComponent(0)">{{$t('message.tag')}}</button>
            <button type="button" class="btn m-1 rounded-blue disabled" v-on:click="switchComponent(1)">{{$t('message.scenario')}}</button>
            <button type="button"class="btn m-1 rounded-blue disabled" v-on:click="switchComponent(3)">{{$t('message.menu')}}</button>
        </div>
        <hr>
        <div id="messagetarget-content" class="p-2">
            <component v-bind:is="currentComponent" :data="data" v-model:loading-count="loadingCount"></component>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center">
                <button type="button" class="btn m-1 rounded-red" @click="handleReset"> {{$t('message.reset')}} </button>
                <button type="button" class="btn m-1 rounded-green" @click="confirm"> {{$t('message.finish')}} </button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: {
                    select_action: 'Select Action', //Message Target & Message Action
                    tag: 'Tag',
                    scenario: 'Scenario',
                    menu: 'Rich menu',
                    finish: 'Finish/Confirm',
                    reset: 'Reset'
                } 
            },
            ja: {
                message: {
                    select_action: 'アクションを選択する', //Message Target & Message Action
                    tag: 'タグ',
                    scenario: 'シナリオ',
                    menu: 'リッチメニュー',
                    finish: '完了',
                    reset: 'リセット'
                } 
            }
        }
    },    
    props: ['data', 'reset', 'updateData', 'loadingCount'],
    data() {
        return {
            currentComponent: "messageaction-tags",
            visible: false,
        }
    },
    methods: {
        updateOnClose() {
            this.updateData()
        },
        switchComponent(componentName)
        {
           switch(componentName) {
                case 0:
                    this.currentComponent = 'messageaction-tags'; 
                    break;
                // case 1:
                //     this.currentComponent = 'messageaction-scenario';
                //     break;
                // case 2:
                //     this.currentComponent = 'messageaction-survey';
                //     break;
                // case 3:
                //     this.currentComponent = 'messageaction-menu';
                //     break;
                default:
                    this.currentComponent = 'messageaction-tags'; 
                    break;
           }
        },
        showModal() {
        //   this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
        handleReset() {
            this.reset()
            this.visible = false
        },
        confirm() {
            this.visible = false
        },
      }
});