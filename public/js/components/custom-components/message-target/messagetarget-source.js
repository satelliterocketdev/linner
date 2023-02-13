Vue.component ('messagetarget-source', {
    template: 
    `<div>
        <div class="row" style="font-size: 18px"> {{$t('message.served')}} </div>
        <div v-for="(serve, key) in defaults.sources.serves">
            <!--<div class="row p-1"> Tags </div>-->
            <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    mode="multiple"
                    style="width: 100%"
                    v-model="serve.value"
                    :placeholder="$t('message.choose_source')"
                >
                    <a-select-option v-for="(source, sourceKey) in sources" :key="source.title">
                        {{ source.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="serve.option">
                    <option value="first">{{$t('message.include_first')}}</option>
                    <option value="second">{{$t('message.include_second')}}</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addServe">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
                </div>
            </div>
        </div>

        <div class="row pt-2" style="font-size: 18px"> Exclude </div>
        <div v-for="(exclude, key) in defaults.sources.excludes">
            <!--<div class="row p-1"> Tags </div>-->
            <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    mode="multiple"
                    style="width: 100%"
                    v-model="exclude.value"
                    :placeholder="$t('message.choose_source')"
                >
                    <a-select-option v-for="(source, sourceKey) in sources" :key="source.title">
                        {{ source.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="exclude.option">
                    <option value="first">{{$t('message.include_first')}}</option>
                    <option value="second">{{$t('message.include_second')}}</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addExclude">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteExclude(exclude)">-</button>
                </div>
            </div>
        </div>

        <div class="row pt-2" style="font-size: 18px">{{$t('message.add_new_source')}}</div>
        <div class="row justify-content-between align-items-center">
            {{$t('message.via_original_name')}}
            <a-input class="form-control p-1" @pressEnter="addSourceName" v-model="name" />
            </div>
        <div class="row justify-content-between align-items-center">
            {{$t('message.via_original_url')}}
            <a-input class="form-control p-1" @pressEnter="addSourceUrl" v-model="url" />
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    served: 'Served',
                    exclude: 'Exclude',
                    choose_source: 'Choose Source',
                    include_first: 'Include those who have one or more of above sources',
                    include_second: 'Include those who have all of above sources',
                    add_new_source: 'Add new URL through source',
                    via_original_name: 'Via the original name',
                    via_original_url: 'Via the original URL'
                },
            },
            ja: {
                message: {
                    served: '配信対象',
                    exclude: '除外対象',
                    choose_source: '経由元を選ぶ',
                    include_first: '1つ以上含む人',
                    include_second: '全て含む人',
                    add_new_source: '新しい経由元を追加',
                    via_original_name: '経由元名',
                    via_original_url: '経由元URL'
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            defaults: {
                sources: {
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
            sources: [],
            name: '',
            url: '',
        }
    },
    created() {
        this.reloadTag()
        if (this.data.sources) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.sources.serves.push({
                value: [],
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.data.sources.serves.indexOf(serve)
            this.data.sources.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.sources.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.sources.excludes.indexOf(exclude)
            this.data.sources.excludes.splice(index, 1)
        },
        addSourceName(el) {
            this.addSource(el, 1)
        },
        addSourceUrl(el) {
            this.addSource(el, 2)
        },
        addSource(el, type) {
            self = this
            axios.post('source', {
                type: type,
                value: (type==1) ? self.name : self.url,
            })
            .then(function(response){
                if (type == 1) {
                    self.name = ''
                } else {
                    self.url = ''
                }

                self.sources = []
                self.reloadTag()
            })
        },
        reloadTag() {
            self = this
            axios.get('source/lists')
            .then(function(response){
                response.data.forEach(function(value, index){
                    self.sources.push({
                        id: value.id,
                        title: value.value,
                    })
                })
            })
        }
    },
});