Vue.component ('messageaction-tags', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px">{{$t('message.tag')}}</div>
    <div v-for="(serve, key) in defaults.tags.serves">
        <div class="row p-1"> {{$t('message.tags')}}</div>
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
                :placeholder="$t('message.select_tag')"
            >
                <a-select-option v-for="(tag, tagKey) in tags" :key="tag.title">
                    {{ tag.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="serve.option">
                <option value="first">{{$t('message.add')}}</option>
                <option value="second">{{$t('message.remove')}}</option>
            </select>
            <div>
                <button class="btn rounded-green" @click="addServe">+</button>
                <button v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>

    <div class="row pt-2" style="font-size: 18px">{{$t('message.new_tag')}}</div>
    <div class="row justify-content-between align-items-center">
        {{$t('message.tag_name')}}
        <input type="text" class="form-control p-1" @keyup.enter="addTag">
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    tag: 'Tag',
                    select_tag: 'Select Tag',
                    add: 'Add',
                    remove: 'Remove',
                    tag_name: 'Tag name',
                    new_tag: 'New Tag',
                    tags: 'Tags'
                },
            },
            ja: {
                message: {
                    tag: 'タグ設定',
                    select_tag: 'タグ名を選ぶ',
                    add: '追加する',
                    remove: '外す',
                    tag_name: 'タグ名',
                    new_tag: '新しいタグを作成',
                    tags: 'タグ'
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
        }
    },
    mounted() {
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
            const index = this.data.tags.serves.indexOf(serve)
            this.data.tags.serves.splice(index, 1)
        },
        addTag(el) {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('tag', {
                title: el.target.value,
                followerslist: [],
                condition: "",
                no_limit: true,
                action: "",
                limit: 1,
            })
            .then(function(response){
                el.target.value = ''
                self.reloadTag()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        reloadTag() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.get('tag/list')
            .then(function(response){
                self.tags = []
                response.data.tags.forEach(function(value, index){
                    self.tags.push({
                        id: value.id,
                        title: value.title,
                    })
                })
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        }
    }
});