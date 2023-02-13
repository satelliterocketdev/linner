Vue.component ('messagetarget-registerdate', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> {{$t('message.served')}} </div>
    <div v-for="(serve, key) in defaults.dates.serves">
        <!--<div class="row p-1"> Tags </div>-->
        <prepend-message-target-date-selection :values="serve.value"></prepend-message-target-date-selection>
        <div class="row justify-content-between align-items-center p-1">
            <a-date-picker v-model="serve.value.from" :placeholder="$t('message.start_date')" /><span>{{$t('message.from')}}</span><a-date-picker v-model="serve.value.to" :placeholder="$t('message.end_data')" />
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>

    <div class="row pt-2" style="font-size: 18px"> {{$t('message.exclude')}} </div>
    <div v-for="(exclude, key) in defaults.dates.excludes">
        <!--<div class="row p-1"> Tags </div>-->
        <prepend-message-target-date-selection :values="exclude.value"></prepend-message-target-date-selection>
        <div class="row justify-content-between align-items-center p-1">
            <a-date-picker v-model="exclude.value.from" :placeholder="$t('message.start_date')" /><span>{{$t('message.from')}}</span><a-date-picker v-model="exclude.value.to" :placeholder="$t('message.end_data')" />
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
                    exclude: 'Exclude',
                    start_date: 'Start Date',
                    end_data: 'End Date',
                    from: 'From'

                },
            },
            ja: {
                message: {
                    served: '配信対象',
                    exclude: '除外対象',
                    start_date: '日付を選択',
                    end_data: '日付を選択',
                    from: 'から'
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            defaults: {
                dates: {
                    serves: [{
                        value: {},
                        option: 'first'
                    }],
                    excludes: [{
                        value: {},
                        option: 'first'
                    }]
                }
            },
            dates: [],
        }
    },
    created() {
        if (this.data.dates) { //編集
            if(this.data.dates.serves[0]['value']['from']) { //登録日（配信対象）が設定されている
                Object.assign(this.defaults.dates.serves, this.data.dates.serves)
            }else{
                Object.assign(this.data.dates.serves, this.defaults.dates.serves)
            }
            if(this.data.dates.excludes[0]['value']['from']) { //登録日（除外対象）が設定されている
                Object.assign(this.defaults.dates.excludes, this.data.dates.excludes)
            }else{
                Object.assign(this.data.dates.excludes, this.defaults.dates.excludes)
            }
        } else { //新規登録
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.dates.serves.push({
                value: {},
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.defaults.dates.serves.indexOf(serve)
            this.defaults.dates.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.dates.excludes.push({
                value: {},
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.defaults.dates.excludes.indexOf(exclude)
            this.defaults.dates.excludes.splice(index, 1)
        },

        disabledStartDate (startValue, endValue) {
            if (!startValue || !endValue) {
                return false;
            }
            // console.log(startValue._d)
            // console.log(endValue._d)
            return startValue._d > endValue._d;
        },
        disabledEndDate (endValue, startValue) {
            if (!endValue || !startValue) {
                return false;
            }
            // return startValue.valueOf() >= endValue.valueOf();
        },
        handleStartOpenChange (open) {
            // if (!open) {
            //     this.endOpen = true;
            // }
        },
        handleEndOpenChange (open) {
            // this.endOpen = open;
        },
    }
});