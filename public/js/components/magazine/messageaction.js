Vue.component ('message-action', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn rounded-white">{{$t('message.change')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null" :destroyOnClose="true" :afterClose="updateOnClose">
        <div class="row justify-content-center pt-4">
            <a-radio-group>
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 0 }" v-on:click="switchComponent(0)">{{$t('message.tag')}}</button>
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 1 }" v-on:click="switchComponent(1)">{{$t('message.scenario')}}</button>
                <!-- <a-radio-button v-on:click="switchComponent(2)">Survey</a-radio-button> -->
                <!-- <a-radio-button v-on:click="switchComponent(3)">Menu</a-radio-button> -->
            </a-radio-group>
        </div>
        <hr>
        <div id="messagetarget-content" class="p-2">
            <component v-bind:is="currentComponent" :data="data"></component>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center">
                <button type="button" class="btn m-1 rounded-red" @click="handleReset">{{$t('message.reset')}}</button>
                <button type="button" class="btn m-1 rounded-green" @click="confirm">{{$t('message.finish')}}</button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    change: 'Change', //Message Target & Message Action
                    reset: 'Reset',
                    finish: 'Finish',
                    tag: 'Tag',
                    scenario: 'Scenario'
                } 
            },
            ja: {
                message: { 
                    change: '追加・編集', //Message Target & Message Action
                    reset: 'リセット',
                    finish: '完了',
                    tag: 'タグ',
                    scenario: 'シナリオ'
                } 
            }
        }
    },    
    props: ['data', 'reset', 'updateData'],
    data() {
        return {
            currentComponent: "messageaction-tags",
            visible: false,
            currentIndex: 0
        }
    },
    methods: {
        updateOnClose() {
            this.updateData()
        },
        switchComponent(componentIndex)
        {
            this.currentIndex = componentIndex;
           switch(componentIndex) {
                case 0:
                    this.currentComponent = 'messageaction-tags'; 
                    break;
                case 1:
                    this.currentComponent = 'messageaction-scenario';
                    break;
                case 2:
                    this.currentComponent = 'messageaction-survey';
                    break;
                case 3:
                    this.currentComponent = 'messageaction-menu';
                    break;
                default:
                    this.currentComponent = 'messagetarget-tags'; 
                    break;
           }
        },
        showModal() {
          this.visible = true
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