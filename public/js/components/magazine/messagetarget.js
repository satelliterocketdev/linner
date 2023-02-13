Vue.component ('message-target', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn rounded-white">{{$t('message.change')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null" :destroyOnClose="true" :afterClose="updateOnClose">
        <div class="row justify-content-center pt-4">
            <a-radio-group>
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 0 }" v-on:click="switchComponent(0)">{{$t('message.tag')}}</button>
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 1 }" v-on:click="switchComponent(1)">{{$t('message.scenario')}}</button>
                <!-- <a-radio-button v-on:click="switchComponent(2)">Survey</a-radio-button> -->
                <!-- <a-radio-button v-on:click="switchComponent(3)">{{$t('message.source')}}</a-radio-button> -->
                <!-- <a-radio-button v-on:click="switchComponent(4)">Conversion</a-radio-button> -->
                <!-- <a-radio-button v-on:click="switchComponent(5)">{{$t('message.name')}}</a-radio-button> -->
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 6 }" v-on:click="switchComponent(6)">{{$t('message.registerDate')}}</button>
            </a-radio-group>
        </div>
        <hr>
        <div id="messagetarget-content" class="p-2">
            <component v-bind:is="currentComponent" :data="data"></component>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center">
                <button type="reset" class="btn m-1 rounded-red" @click="handleReset"> {{$t('message.reset')}} </button>
                <button type="button" class="btn m-1 rounded-green" @click="confirm"> {{$t('message.confirm')}} </button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    change: 'Change', //Message Target & Message Action
                    tag: 'Tag',
                    scenario: 'Scenario',
                    source: 'Source',
                    name: 'Name',
                    registerDate: 'RegisterDate',
                    reset: 'Reset',
                    confirm: 'Finish/Confirm',
                }
            },
            ja: {
                message: { 
                    change: '追加・編集', //Message Target & Message Action
                    tag: 'タグ',
                    scenario: 'シナリオ',
                    source: 'ソース',
                    name: '名前',
                    registerDate: '登録日',
                    reset: 'リセット',
                    confirm: '完了',
                }
            }
        }
    },    
    props: ['data', 'reset', 'updateData'],
    data() {
        return {
            currentComponent: 'messagetarget-tags',
            visible: false,
            currentIndex: 0
        }
    },
    methods: {
        updateOnClose() {
            this.updateData()
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
        switchComponent(componentIndex)
        {
            this.currentIndex = componentIndex;
            switch(componentIndex) 
            {
                case 0:
                    this.currentComponent = 'messagetarget-tags'; 
                    break;
                case 1:
                    this.currentComponent = 'messagetarget-scenario';
                    break;
                // case 2:
                //     this.currentComponent = 'messagetarget-survey';
                //     break;
                // case 3:
                //     this.currentComponent = 'messagetarget-source';
                //     break;
                // case 4:
                //     this.currentComponent = 'messagetarget-conversion';
                //     break;
                // case 5:
                //     this.currentComponent = 'messagetarget-name';
                //     break;
                case 6:
                    this.currentComponent = 'messagetarget-registerdate';
                    break;
                default:
                    this.currentComponent = 'messagetarget-tags'; 
                    break;
            }
        },
    }

});