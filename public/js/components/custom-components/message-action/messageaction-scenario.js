Vue.component ('messageaction-scenario', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> {{$t('message.scenario')}} </div>
    <div v-for="(serve, key) in defaults.scenarios.serves">
        <!--<div class="row p-1"> Tag set</div>-->
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
                :placeholder="$t('message.choose_tag')"
            >
                <a-select-option v-for="(scenario, scenarioKey) in scenarios" :key="scenario.title">
                    {{ scenario.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select v-model="serve.option" class="custom-select" style="width: 75%">
                <option value="first">{{$t('message.add_above_tag')}}</option>
                <option value="second">{{$t('message.remove_above_tag')}}</option>
            </select>
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    scenario: 'Scenario',
                    add_above_tag: 'Add above tag',
                    remove_above_tag: 'Remove above tag',
                    choose_tag: 'Choose Tag',
                    delivery_set: 'Delivery set',
                    delivery_start: 'Delivery start number',
                    immediate: 'Immediate',
                    one_hour_late: '1 hour later',
                    three_hours_late: '3 hour later',
                    twelf_hours_late: '12 hour later',
                    twenty_four_hours_late: '24 hour later'
                },
            },
            ja: {
                message: {
                    scenario: 'シナリオ設定',
                    add_above_tag: '追加する',
                    remove_above_tag: 'はずす',
                    choose_tag: 'シナリオ名を選ぶ',
                    delivery_set: '配信設定',
                    delivery_start: '通目から配信する',
                    immediate: '即時',
                    one_hour_late: '１時間後',
                    three_hours_late: '３時間後',
                    twelf_hours_late: '１２時間後',
                    twenty_four_hours_late: '２４時間後'
                }
            }
        }
    },
    props: ['data'],
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
        }
    }
});