const previewModal = {
    template:
`<div>
    <a-modal :closable="false" :centered="true" v-model="visible" :width="350" :footer="null">
        <div class="line__container" style="font-size: 12px">
            <div class="line__contents">
                <div v-if="message" class="line__right">
                    <div v-html="message" class="text"></div>
                </div>
            </div>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center align-items-center">
                <div class="col-sm align-items-center text-center">
                    <button class="btn rounded-green m-1" @click="close">{{ $t('message.close') }}</button>
                </div>
            </div>
        </div>
    </a-modal>
</div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    close: 'Close'
                }
            },
            ja: {
                message: {
                    close: '閉じる'
                }
            }
        }
    },
    data() {
        return {
            message: '',
            visible: false,
        }
    },
    methods: {
        close() {
            this.visible = false
        },
        showModal(data) {
            this.message = data
            this.visible = true
        },
        handleOk(e) {
            console.log(e);
            this.visible = false
        },
    }
};

const inclusionAnalysisTab = {
    components: {'preview-modal': previewModal},
    template: `
    <div class="mt-2">
        <preview-modal ref="preview"></preview-modal>
        <div class="row mx-0 font-size-table">
            <div class="col-3 p-1">{{ $t('message.title') }}</div>
            <div class="col p-1">{{ $t('message.timing') }}</div>
            <div class="col p-1">{{ $t('message.total_access') }}</div>
            <div class="col p-1">{{ $t('message.total_visitors') }}</div>
            <div class="col-2 p-1">&nbsp;</div>
        </div>
        <div class="row mx-0 my-2 align-items-center font-size-table" v-for="(val, key) in items">
            <div class="col-3 p-1">{{ val.title }}</div>
            <div class="col p-1">{{ val.timing }}</div>
            <div class="col p-1">{{ $t('message.record_access', { p1: val.access_count , p2: val.send_count }) }}</div>
            <div class="col p-1">{{ $t('message.record_visitors', { p1: val.visitors , p2: val.send_people }) }}</div>
            <div class="col-2 p-1">
                <button type="button" @click="showPreview(val)" class="btn btn-sm btn-secondary font-size-table">{{$t('message.preview')}}</button>
            </div>
        </div>
        <!-- ダミー行で高さ調整 -->
        <template v-if="3 > items.length">
            <div v-for="(val, key) in (3 - items.length)" class="my-2 p-1">
                <div style="line-height:1.5; padding:5px">&nbsp;</div>
            </div>
        </template>
        <div v-if="records.length > 3" class="d-flex justify-content-center">
            <div class="showmoreorless" @click.prevent="modal_tab_showall = !modal_tab_showall" v-html="modal_tab_showall ? $t('message.see_less') :  $t('message.show_more')"></div>
        </div>
        <div v-else class="d-flex justify-content-center">
            &nbsp;
        </div>
    </div>
    `,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    title: 'Title',
                    timing: 'Timing',
                    total_access: 'Total Click',
                    total_visitors: 'Total Access',
                    see_less:'See Less', 
                    show_more:'Show More',
                    preview: 'Preview',
                }
            },
            ja: {
                message: {
                    title: 'タイトル',
                    timing: '配信タイミング',
                    total_access: 'クリック数',
                    total_visitors: '訪問人数',
                    record_access: '{p1}回 / {p2}送信',
                    record_visitors: '{p1}回 / {p2}送信',
                    see_less:'表示数を戻す', 
                    show_more:'もっと見る',
                    preview: 'プレビュー',
                }
            }
        }
    },
    props: {
        records : {
            type: Array,
        }
    },
    data(){
        return {
            modal_tab_showall: false,
        }
    },
    computed: {
        items() {
            const list = this.records
            return this.modal_tab_showall ? list : list.slice(0, 3)
        }  
    },
    methods: {
        showPreview(data){
            this.$refs.preview.showModal(data.message)
        }
    }
}

const individualAnalysisTab = {
    components: {'preview-modal': previewModal},
    template: `
    <div class="mt-2">
        <preview-modal ref="preview"></preview-modal>
        <div class="row mx-0 font-size-table">
            <div class="col-3 p-1">{{ $t('message.friend_name') }}</div>
            <div class="col p-1">{{ $t('message.send_time') }}</div>
            <div class="col p-1">{{ $t('message.total_access') }}</div>
            <div class="col p-1">&nbsp;</div>
            <div class="col-2 p-1">&nbsp;</div>
        </div>
        <div class="row mx-0 my-2 align-items-center" v-for="(val, key) in items">
            <div class="col-3 p-1">{{ val.name }}</div>
            <div class="col p-1">{{ val.send_date_time }}</div>
            <div class="col p-1">{{ $t('message.record_access', { p1: val.access_count , p2: val.send_count }) }}</div>
            <div class="col p-1">&nbsp;</div>
            <div class="col-2 p-1">
                <button type="button" @click="showPreview(val)" class="btn btn-sm btn-secondary">{{$t('message.preview')}}</button>
            </div>
        </div>
        <!-- ダミー行で高さ調整 -->
        <template v-if="3 > items.length">
            <div v-for="(val, key) in (3 - items.length)" class="my-2 p-1">
            <div style="line-height:1.5; padding:5px">&nbsp;</div>
            </div>
        </template>
        <div v-if="records.length > 3" class="d-flex justify-content-center">
            <div class="showmoreorless" @click.prevent="modal_tab_showall = !modal_tab_showall" v-html="modal_tab_showall ? $t('message.see_less') :  $t('message.show_more')"></div>
        </div>
        <div v-else class="d-flex justify-content-center">
            &nbsp;
        </div>
    </div>
    `,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    friend_name: 'Friend Name',
                    send_time: 'Send',
                    total_access: 'Click Count',
                    see_less:'See Less', 
                    show_more:'Show More',
                    preview: 'Preview',
                }
            },
            ja: {
                message: {
                    friend_name: 'フレンド名',
                    send_time: '送信日時',
                    total_access: 'クリック',
                    record_access: '{p1}回 / {p2}送信',
                    see_less:'表示数を戻す', 
                    show_more:'もっと見る',
                    preview: 'プレビュー',
                }
            }
        }
    },
    props: {
        records : {
            type: Array,
        }
    },
    data(){
        return {
            modal_tab_showall: false,
        }
    },
    computed: {
        items() {
            const list = this.records
            return this.modal_tab_showall ? list : list.slice(0, 3)
        }  
    },
    methods: {
        showPreview(data){
            this.$refs.preview.showModal(data.message)
        }
    }
}

const clickrateAnalysis = {
    components: {
        'scenario-analysis-tab': inclusionAnalysisTab,
        'magazine-analysis-tab': inclusionAnalysisTab,
        'individual-analysis-tab': individualAnalysisTab,
    },
    template: `
    <div class="shadow-sm mt-2 p-0 mt-sm-4 p-sm-3">
        <div class="d-flex justify-content-between border-bottom pb-2 modal-tab-scenario">
            <div v-bind:class="{ 'active': active_tab_id == 0 }" @click.prevent="active_tab_id = 0">{{ $t('message.scenario_analysis') }}</div>
            <div v-bind:class="{ 'active': active_tab_id == 1 }" @click.prevent="active_tab_id = 1">{{ $t('message.magazine_analysis') }}</div>
            <div v-bind:class="{ 'active': active_tab_id == 2 }" @click.prevent="active_tab_id = 2">{{ $t('message.individual_analysis') }}</div>
        </div>
        <component :is="currentTab" :records="properties"></component>
    </div>
    `,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    scenario_analysis: 'Scenario',
                    magazine_analysis: 'Magazine',
                    individual_analysis: 'Individual',
                }
            },
            ja: {
                message: {
                    scenario_analysis: '配信シナリオ毎の分析',
                    magazine_analysis: '一斉配信毎の分析',
                    individual_analysis: '個別配信毎の分析',
                }
            }
        }
    },
    props: {
        scenario: {
            type: Array
        },
        magazine: {
            type: Array
        },
        individual: {
            type: Array
        }
    },
    data(){
        return {
            active_tab_id: 0,
            tabList: ['scenario-analysis-tab', 'magazine-analysis-tab', 'individual-analysis-tab']
        }
    },
    computed: {
        properties(){
            const sample = {
                title: 'アンケートLP自動返信文',
                timing: '当日 1:00後 1通目',
                total_click: '530回 / 3450送信',
                total_access: '240回 / 3450送信',
            }
            const sample2 = {
                name: 'やまだはなこ',
                send_date_time: '2019:12-10 03:45',
                click_count: '10回 / 34送信',
            }
            switch (this.active_tab_id){
                case 0:
                    return this.scenario;
                    //return [sample,sample,sample,sample,sample,sample,sample,sample]
                case 1:
                    return this.magazine;
                case 2:
                    return this.individual;
            }
        },
        currentTab(){
            let tabId = this.active_tab_id
            return this.tabList[tabId]
        },
    },
}

const chartOption = {
    legend: { display: false },
    title: { display: false},
    aspectRatio: 1,
    cutoutPercentage: 80,
    animation: { animateScale: true, animateRotate: true },
    tooltips: { enabled: false }
}

const clickrateSummary = {
    components: {'clickrate-analysis' : clickrateAnalysis },
    template: `
    <div >
        <div class="row d-flex align-items-center justify-content-between border-bottom">
            <div class="col-sm-6 wordbreak-all">
                <h2>{{ item.title }}</h2>
            </div>
            <div class="col-sm-6 d-flex text-center">
                <div class="col-6">
                    <p class="m-0" style="white-space: nowrap;">{{ $t('message.label_total_click') }}</p>
                    <p class="summary font-mediumpurple m-0">{{ item.total_access_count }}</p>
                </div>
                <div class="col-6">
                    <p class="m-0">{{ $t('message.label_total_access') }}</p>
                    <p class="summary access-count m-0">{{ item.visitors }}</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-around mt-2 mt-sm-4">
            <div class="flex-even shadow-sm border border-light mr-1 p-2 mr-sm-4 p-sm-3">
                <div class="border-bottom pb-2 mb-3">{{ $t('message.statistics_click') }}</div>
                <div class="chart-container">
                    <canvas id="chart-click"></canvas>
                    <div class="chart-parcent">
                        <div class="text-center">
                           <p class="percentage">{{ click_percentage }}％</p>
                           <p class="description">{{ $t('message.click_description') }}</p>
                        </div>
                    </div>
                    <div class="chart-legends my-2 d-flex no-gutters justify-content-around">
                        <p class="text-center small-text">
                            <span style="color:#d6f5fc">●</span>
                            {{ $t('message.legend_send_count') }}<br>{{ item.total_send_count }}
                        </p>
                        <p class="text-center small-text">
                            <span style="color:#78ddf5">●</span>
                            {{ $t('message.legend_total_click') }}<br>{{ item.total_access_count }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-even shadow-sm border border-light ml-1 p-2 ml-sm-4 p-sm-3">
                <div class="border-bottom pb-2 mb-3">{{ $t('message.statistics_people') }}</div>
                <div class="chart-container">
                    <canvas id="chart-people"></canvas>
                    <div class="chart-parcent">
                        <div class="text-center">
                            <p class="percentage">{{ people_percentage }}％</p>
                            <p class="description">{{ $t('message.people_description') }}</p>
                        </div>
                    </div>
                    <div class="chart-legends my-2 d-flex no-gutters justify-content-around text-center">
                        <p class="small-text">
                            <span style="color:#d8f7d3">●</span>
                            {{ $t('message.legend_send_people') }}<br>{{ item.send_people }}
                        </p>
                        <p class="small-text">
                            <span style="color:#7fe36f">●</span>
                            {{ $t('message.legend_total_visitors') }}<br>{{ item.visitors }}
                        </p>
                        <p class="small-text">
                            {{ $t('message.legend_total_friend') }}<br>{{ item.friends }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <clickrate-analysis :scenario="item.scenario_data" :magazine="item.magazine_data" :individual="item.individual_data"></clickrate-analysis>
    </div>
    `,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                }
            },
            ja: {
                message: {
                    statistics_click: 'クリック統計',
                    statistics_people: '人数統計',
                    
                    label_total_click: '総クリック数',
                    label_total_access: '訪問人数',
                    legend_send_count: '送信回数',
                    legend_total_click: '総クリック数',
                    legend_send_people: 'URL送信人数',
                    legend_total_visitors: '訪問人数',
                    legend_total_friend: 'フレンド総数',
                    click_description: 'クリック／送信回数',
                    people_description: '訪問人数／送信人数',
                }
            }
        }
    },
    props: {
        item: {
            type: Object
        },
    },
    computed: {
        click_percentage(){
            return Math.round((this.item.total_access_count / this.item.total_send_count) * 100)
        },
        people_percentage(){
            return Math.round((this.item.visitors / this.item.send_people) * 100)
        }
    },
    data() {
        return {
        }
    },
    mounted() {
        var clickData = {
            datasets: [{
                data: [this.item.total_access_count, this.item.total_send_count],
                backgroundColor: ['#78ddf5', '#d6f5fc'],
                hoverBackgroundColor: ['#78ddf5', '#d6f5fc'],
                borderWidth: 0
            }],
        }

        var clickChart = new Chart(document.getElementById('chart-click').getContext('2d'), {
            type: 'doughnut',
            data: clickData,
            options: chartOption
        });

        var peopleData = {
            datasets: [{
                data: [this.item.visitors, this.item.send_people],
                backgroundColor: ['#7fe36f', '#d8f7d3'],
                hoverBackgroundColor: ['#7fe36f', '#d8f7d3'],
                borderWidth: 0
            }],
        }

        var peopleChart = new Chart(document.getElementById('chart-people').getContext('2d'), {
            type: 'doughnut',
            data: peopleData,
            options: chartOption
        });
    }

};

Vue.component('clickrate-detail', {
    template:
        `<div>
        <a-modal :centered="true" title="クリック測定" v-model="visible" :width="800" :afterClose="afterClose" :footer="null">
        <div v-if="!showing"></div>
        <div v-else-if="loading">Loading...</div>
        <div v-else>
        <clickrate-summary :item="itemData" @completion="completion"></clickrate-summary>
        </div>
        </a-modal>
    </div>`,
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['loadingCount'],
    components: {
        'clickrate-summary': clickrateSummary,
    },
    data() {
        return {
            showing: true,　// modalが開いたとき〜modalが閉じた後までを管理。
            visible: false,
            itemData: null,
            loading: false,
            editMode: false,
        }
    },
    methods: {
        openNotificationWithIcon(type, message, desc) {
            this.$notification[type]({
                message: message,
                description: desc,
            });
        },
        getData(itemId) {
            // 改めて情報を取得する
            var self = this
            self.loading = true
            this.$emit('input', this.loadingCount + 1)
            axios.get("/clickrate/show_detail/" + itemId)
                .then(res => {
                    self.itemData = res.data;
                })
                .catch(e => self.openNotificationWithIcon('error', 'An Error Occurred'))
                .finally(() => {
                    self.loading = false
                    this.$emit('input', this.loadingCount - 1)
                })
        },
        showModal(itemId) {
            this.showing = true
            this.visible = true
            this.editMode = itemId != null

            this.getData(itemId)
        },
        hideModal() {
            this.visible = false
        },
        afterClose() {
            this.showing = false
        },
        completion(res) {
            this.$emit('completion', res)
            this.hideModal()
        }
    }
});
