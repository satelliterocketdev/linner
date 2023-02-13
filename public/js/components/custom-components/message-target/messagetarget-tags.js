Vue.component ('messagetarget-tags', {
    template: 
    `<div>
        <div class="row" style="font-size: 18px"> {{$t('message.served')}} </div>
        <div v-for="(serve, key) in defaults.tags.serves">
            <div class="row p-1"> {{$t('message.tags')}} </div>
            <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    mode="multiple"
                    style="width: 100%"
                    v-model="serve.value"
                >
                    <a-select-option v-for="(tag, tagKey) in tags" :key="tag.title">
                        {{ tag.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="serve.option" :taggable="true">
                    <option value="first">{{$t('message.includeFirst')}}</option>
                    <option value="second">{{$t('message.includeSecond')}}</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addServe">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
                </div>
            </div>
        </div>

        <div class="row pt-2" style="font-size: 18px"> {{$t('message.exclude')}} </div>
        <div v-for="(exclude, key) in defaults.tags.excludes">
            <div class="row p-1"> {{$t('message.tags')}} </div>
            <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    mode="multiple"
                    style="width: 100%"
                    v-model="exclude.value"
                >
                    <a-select-option v-for="(tag, tagKey) in tags" :key="tag.title">
                        {{ tag.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="exclude.option" :taggable="true">
                    <option value="first">{{$t('message.excludeFirst')}}</option>
                    <option value="second">{{$t('message.excludeSecond')}}</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addExclude">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteExclude(exclude)">-</button>
                </div>
            </div>
       </div>

        <div class="row pt-2" style="font-size: 18px"> {{$t('message.createTags')}} </div>
        <div class="row justify-content-between align-items-center">
            {{$t('message.tagName')}}
            <a-input class="form-control p-1" @pressEnter="addTag" v-model="tag" />
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    served: 'Served',
                    tags: 'Tags',
                    includeFirst: 'Include those who have one or more of above tags',
                    includeSecond: 'Include those who have all of above tags',
                    excludeFirst: 'Exclude those who have one or more of the above tags',
                    excludeSecond: 'Exclude those who have all the above tags',
                    exclude: 'Exclude',
                    createTags: 'Create Tags',
                    tagName: 'Tag name',
                },
            },
            ja: {
                message: {
                    served: '配信対象',
                    tags: 'タグ',
                    includeFirst: '上記のタグを１つ以上持っている人を含める',
                    includeSecond: '上記のタグをすべて持っている人を含める',
                    exclude: '除外対象',
                    excludeFirst: '上記のタグを１つ以上持っている人を除外する',
                    excludeSecond: '上記のタグをすべて持っている人を除外する',
                    createTags: '新しいタグを作成する',
                    tagName: 'タグ名',
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            defaults: {
                tags: {
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
            tags: [],
            tag: '',
        }
    },
    created() {
        this.reloadTag()
        if (this.data.tags) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.tags.serves.push({
                value: [],
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.defaults.tags.serves.indexOf(serve)
            this.defaults.tags.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.tags.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.defaults.tags.excludes.indexOf(exclude)
            this.defaults.tags.excludes.splice(index, 1)
        },
        addTag(el) {
            self = this
            axios.post('tag', {
                title: self.tag,
                followerslist: [],
                condition: "",
                no_limit: true,
                action: "",
                limit: 1,
            })
            .then(function(response){
                self.tag = ''
                self.tags = []
                self.reloadTag()
            })
        },
        reloadTag() {
            self = this
            axios.get('tag/list')
            .then(function(response){
                self.tags = []
                response.data.tags.forEach(function(value, index){
                    if (self.tags) {
                        self.tags.push({
                            id: value.id,
                            title: value.title,
                        })
                    }
                })
            })
        }
    }
});