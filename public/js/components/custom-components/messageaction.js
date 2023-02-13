Vue.component('message-action', {
    template:
    `<div>
        <button type="button" @click="showModal" class="btn rounded-white">{{$t('message.change')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null" :destroyOnClose="true" :afterClose="onAfterClose">
            <div class="row justify-content-center pt-4">
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 0 }" v-on:click="switchComponent(0)">{{$t('message.tag')}}</button>
                <button type="button" class="btn m-1 rounded-white" v-bind:class="{ 'active-filter' : currentIndex == 1 }" v-on:click="switchComponent(1)">{{$t('message.scenario')}}</button>
                <!-- <a-radio-button v-on:click="switchComponent(2)">Survey</a-radio-button> -->
                <!-- <button type="button"class="btn m-1 rounded-blue" v-on:click="switchComponent(3)">{{$t('message.menu')}}</button> -->
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
                    change: 'Change', //Message Target & Message Action
                    tag: 'Tag',
                    scenario: 'Scenario',
                    menu: 'Rich menu',
                    finish: 'Finish/Confirm',
                    reset: 'Reset'
                }
            },
            ja: {
                message: {
                    change: '変更', //Message Target & Message Action
                    tag: 'タグ',
                    scenario: 'シナリオ',
                    menu: 'リッチメニュー',
                    finish: '完了',
                    reset: 'リセット'
                }
            }
        }
    },
    props: ['data', 'reset', 'updateData', 'loadingCount', 'restore', 'setText'],
    data() {
        return {
            currentComponent: "messageaction-tags",
            confirmed: false,
            visible: false,
            currentIndex: 0
        }
    },
    methods: {
        /**
         * 画面終了時の処理。
         * @returns {void}
         */
        onAfterClose() {
            if (!this.confirmed) {
                this.restore()
            }
            this.setText()
        },
        switchComponent(componentIndex) {
            this.currentIndex = componentIndex
            switch (componentIndex) {
                case 0:
                    this.currentComponent = 'messageaction-tags';
                    break;
                case 1:
                    this.currentComponent = 'messageaction-scenario';
                    break;
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
            this.currentIndex = 0
            this.switchComponent(this.currentIndex)
            this.confirmed = false
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
            this.updateData()
            this.confirmed = true
            this.visible = false
        },
    }
});
