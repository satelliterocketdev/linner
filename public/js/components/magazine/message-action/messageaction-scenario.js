Vue.component ('messageaction-scenario', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px">{{$t('message.scenario')}}</div>
    <div v-for="(serve, key) in defaults.scenarios.serves">
        <div class="row p-1"> {{$t('message.scenario')}}</div>
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
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
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>

        <div class="p-3">
            <div class="row align-items-center p-1">
                <span>{{$t('message.delivery_set')}}</span>
                <select class="custom-select ml-2" style="width: 50%" v-model="serve.delivery.timing" :disabled="serve.option == 'second'">
                    <option value="0">{{$t('message.now')}}</option>
                    <option value="1">{{$t('message.one_hour')}}</option>
                    <option value="3">{{$t('message.three_hours_late')}}</option>
                    <option value="12">{{$t('message.twelf_hours_late')}}</option>
                    <option value="24">{{$t('message.twenty_four_hours')}}</option>
                </select>
            </div>
            <div class="row align-items-center p-1">
                <input type="number" v-model="serve.delivery.number" min="1" class="form-control mr-2" style="width: 15%" :disabled="serve.option == 'second'">
                <span>{{$t('message.delivery_start_number')}}</span>
            </div>
        </div>
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    scenario: 'Scenario',
                    select_scenario: 'Select Scenario',
                    add: 'Add',
                    remove: 'Remove',
                    now: 'Now',
                    one_hour: '1 hour late',
                    three_hours_late: '3 hours late',
                    twelf_hours_late: '12 hours late',
                    twenty_four_hours: '24 hours late',
                    reset: 'Reset',
                    finish: 'Finish',
                    delivery_start_number: 'Delivery start number',
                    delivery_set: 'Delivery Set',
                    scenario: 'Scenario'
                },
            },
            ja: {
                message: {
                    scenario: 'シナリオ設定',
                    select_scenario: 'シナリオ名を選ぶ',
                    add: '追加する',
                    remove: '外す',
                    now: '即時',
                    one_hour: '１時間後',
                    three_hours_late: '２時間後',
                    twelf_hours_late: '１２時間後',
                    twenty_four_hours: '２４時間後',
                    reset: 'リセット',
                    finish: '完了',
                    delivery_start_number: '通目から配信する',
                    delivery_set: '配信設定',
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
            isActive: false,
            defaults: {
                scenarios: {
                    serves: [{
                        value: [],
                        option: 'first',
                        delivery: {
                            timing: '',
                            number: '',
                        },
                    }],
                }
            },
            scenarios: [],
        }
    },
    created() {
        this.reloadTag()
        if (this.data.scenarios) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.scenarios.serves.push({
                value: [],
                option: 'first',
                delivery: {
                    timing: '',
                    number: '',
                },
            })
        },
        deleteServe(serve) {
            const index = this.data.scenarios.serves.indexOf(serve)
            this.data.scenarios.serves.splice(index, 1)
        },
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
        }
    }
});