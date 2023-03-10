Vue.component ('messagetarget-scenario-many', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> {{$t('message.served')}} </div>
    <div v-for="(serve, key) in defaults.scenarios.serves">
        <div class="row p-1"> {{$t('message.scenarios')}} </div>
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
            >
                <a-select-option v-for="(scenario, scenarioKey) in scenarios" :key="scenario.title">
                    {{ scenario.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="serve.option">
                <option value="first">{{$t('message.includeFirst')}}</option>
                <option value="second">{{$t('message.includeSecond')}}</option>
            </select>
            <div v-if="serve.option=='third'" class="justify-content-between align-items-center pt-1">
                <input v-model="serve.day" type="number" class="form-control" placeholder="Day here" min="1" /> Day
            </div>
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>

    <div class="row pt-2" style="font-size: 18px"> {{$t('message.exclude')}} </div>
    <div v-for="(exclude, key) in defaults.scenarios.excludes">
        <div class="row p-1"> {{$t('message.scenarios')}} </div>
        <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="exclude.value"
            >
                <a-select-option v-for="(scenario, scenarioKey) in scenarios" :key="scenario.title">
                    {{ scenario.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="exclude.option">
                <option value="first">{{$t('message.excludeFirst')}}</option>
                <option value="second">{{$t('message.excludeSecond')}}</option>
            </select>
            <div v-if="exclude.option=='third'" class="justify-content-between align-items-center pt-1">
                <input v-model="exclude.day" type="number" min="1" class="form-control" placeholder="Day here" /> Day
            </div>
            <div>
                <button type="button" class="btn rounded-green" @click="addExclude">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteExclude(exclude)">-</button>
            </div>
        </div>
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    served: 'Served',
                    scenarios: 'Scenarios',
                    includeFirst: 'Include those who have one or more of above scenarios',
                    includeSecond: 'Include those who have all of above scenarios',
                    includeThird: 'Include those who are on day? the scenario',
                    excludeFirst: 'Exclude those who have one or more of the above scenarios',
                    excludeThird: 'Exclude those who are on day? the scenario',
                    exclude: 'Exclude',
                },
            },
            ja: {
                message: {
                    served: '????????????',
                    scenarios: '????????????',
                    includeFirst: '??????????????????????????????????????????????????????????????????',
                    includeSecond: '???????????????????????????????????????????????????????????????',
                    exclude: '????????????',
                    excludeFirst: '?????????????????????????????????????????????????????????????????????',
                    excludeSecond: '??????????????????????????????????????????????????????????????????',
                }
            }
        }
    },
    props: ['data', 'type', 'account_id'],
    data() {
        return {
            defaults: {
                scenarios: {
                    serves: [{
                        value: [],
                        option: 'first',
                        day: 0,
                    }],
                    excludes: [{
                        value: [],
                        option: 'first',
                        day: 0,
                    }]
                }
            },
            scenarios: [],
            getPath:'',
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
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.data.scenarios.serves.indexOf(serve)
            this.data.scenarios.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.scenarios.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.scenarios.excludes.indexOf(exclude)
            this.data.scenarios.excludes.splice(index, 1)
        },
        reloadTag() {
            let self = this
            if (this.type == 'New') {
                this.getPath = 'stepmail/all-account?type='+this.type
            } else {
                this.getPath = 'stepmail/all-account?type='+this.type+'&account_id='+this.account_id
            }
            axios.get(this.getPath)
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