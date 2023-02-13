Vue.component ('messagetarget-name', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> {{$t('message.served')}} </div>
    <div v-for="(serve, key) in defaults.names.serves">
        <!--<div class="row p-1"> Tags </div>-->
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
                :placeholder="$t('message.write_name')"
            >
                <a-select-option v-for="(name, nameKey) in names" :key="name.title">
                    {{ name.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <a-radio-group v-model="serve.option" name="messagetarget-name" :defaultValue="0">
                <a-radio value="first"><span class="ml-2">{{$t('message.show_exact_matches')}}</span></a-radio>
                <a-radio value="second"><span class="ml-2">{{$t('message.include_similar_results')}}</span></a-radio>
            </a-radio-group>
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>

    <div class="row pt-2" style="font-size: 18px"> {{$t('message.exclude')}} </div>
    <div v-for="(exclude, key) in defaults.names.excludes">
        <!--<div class="row p-1"> Tags </div>-->
        <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="exclude.value"
                :placeholder="$t('message.write_name')"
            >
                <a-select-option v-for="(name, nameKey) in names" :key="name.title">
                    {{ name.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <a-radio-group v-model="exclude.option" name="messagetarget-name" :defaultValue="0">
                <a-radio value="first"><span class="ml-2">{{$t('message.show_exact_matches')}}</span></a-radio>
                <a-radio value="second"><span class="ml-2">{{$t('message.include_similar_results')}}</span></a-radio>
            </a-radio-group>
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
                    show_exact_matches: 'Show exact matches only',
                    include_similar_results: 'Include similar results',
                    write_name: 'Write Name'

                },
            },
            ja: {
                message: {
                    served: '配信対象',
                    exclude: '除外対象',
                    show_exact_matches: '一致した結果のみ表示',
                    include_similar_results: '曖昧検索',
                    write_name: '名前を入力'
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            defaults: {
                names: {
                    serves: [{
                        value: [],
                        option: 'first'
                    }],
                    excludes: [{
                        value: [],
                        option: 'first'
                    }]
                }
            },
            names: [],
        }
    },
    created() {
        this.reloadTag()
        if (this.data.names) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.names.serves.push({
                value: [],
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.data.names.serves.indexOf(serve)
            this.data.names.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.names.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.names.excludes.indexOf(exclude)
            this.data.names.excludes.splice(index, 1)
        },
        reloadTag() {
            self = this
            axios.get('follower/lists')
            .then(function(response){
                response.data.forEach(function(value, index){
                    self.names.push({
                        id: value.id,
                        title: value.display_name,
                    })
                })
            })
        },
    }
});