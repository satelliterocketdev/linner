Vue.component('message-target', {
    template:
    `<div>
        <button type="button" @click="showModal" class="btn rounded-white">{{$t('message.change')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null" :destroyOnClose="true" :afterClose="onAfterClose">
            <div class="row justify-content-center pt-4">
                <a-radio-group>
                    <button type="button" class="btn m-1" :class="tagBtn()" v-on:click="switchComponent(0)">{{$t('message.tag')}}</button>
                    <button type="button" class="btn m-1" :class="scenarioBtn()" v-on:click="switchComponent(1)">{{$t('message.scenario')}}</button>
                    <!-- <a-radio-button v-on:click="switchComponent(2)">Survey</a-radio-button> -->
                    <!-- <a-radio-button v-on:click="switchComponent(3)">{{$t('message.source')}}</a-radio-button> -->
                    <!-- <a-radio-button v-on:click="switchComponent(4)">Conversion</a-radio-button> -->
                    <!-- <button type="button" class="btn m-1 rounded-blue disabled" v-on:click="switchComponent(5)">{{$t('message.name')}}</button> -->
                    <button type="button" class="btn m-1" :class="dateBtn()" v-on:click="switchComponent(6)">{{$t('message.registerDate')}}</button>
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
                    change: '??????', //Message Target & Message Action
                    tag: '??????',
                    scenario: '????????????',
                    source: '?????????',
                    name: '??????',
                    registerDate: '?????????',
                    reset: '????????????',
                    confirm: '??????',
                }
            }
        }
    },
    props: ['data', 'reset', 'updateData', 'restore', 'setText'],
    data() {
        return {
            currentComponent: 'messagetarget-tags',
            /**
             * ??????????????????????????????????????????????????????????????????????????????
             * @type {boolean}
             */
            confirmed: false,
            visible: false,
            currentIndex: 0
        }
    },
    methods: {
        /**
         * ???????????????????????????
         * @returns {void}
         */
        onAfterClose() {
            if (!this.confirmed) {
                this.restore()
            }
            this.setText()
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
        switchComponent(componentIndex) {
            this.currentIndex = componentIndex;
            switch (componentIndex) {
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
        tagBtn() {
            if (this.currentIndex == 0) {
                return 'rounded-blue'
            }
            if (this.data.tags) {
                let tags = this.data.tags
                if (tags.serves[0].value.length > 0 || tags.excludes[0].value.length > 0) {
                    return 'rounded-cyan'
                }
            }
            return 'rounded-white'
        },
        scenarioBtn() {
            if (this.currentIndex == 1) {
                return 'rounded-blue'
            }
            if (this.data.scenarios) {
                let scenarios = this.data.scenarios
                if (scenarios.serves[0].value.length > 0 || scenarios.excludes[0].value.length > 0) {
                    return 'rounded-cyan'
                }
            }
            return 'rounded-white'
        },
        dateBtn() {
            if (this.currentIndex == 6) {
                return 'rounded-blue'
            }
            if (this.data.dates) {
                let dates = this.data.dates
                if (Object.keys(dates.serves[0].value).length > 0 || Object.keys(dates.excludes[0].value).length > 0) {
                    return 'rounded-cyan'
                }
            }
            return 'rounded-white'
        }
    }
});
