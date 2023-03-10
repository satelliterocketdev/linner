Vue.component ('messageaction-tags', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> {{$t('message.tag')}} </div>
    <div v-for="(serve, key) in defaults.tags.serves">
        <!--<div class="row p-1"> Tag set</div>-->
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
                :placeholder="$t('message.choose_tag')"
            >
                <a-select-option v-for="(tag, tagKey) in tags" :key="tag.title">
                    {{ tag.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="serve.option">
                <option value="first">{{$t('message.add_above_tag')}}</option>
                <option value="second">{{$t('message.remove_above_tag')}}</option>
            </select>
            <div>
                <button class="btn rounded-green" @click="addServe">+</button>
                <button v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>
    </div>

    <div class="row pt-2" style="font-size: 18px">{{$t('message.create_tags')}}</div>
    <div class="row justify-content-between align-items-center">
        {{$t('message.name')}}
        <input type="text" class="form-control p-1" @keyup.enter="addTag">
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    tag: 'Tag',
                    add_above_tag: 'Add above tag',
                    remove_above_tag: 'Remove above tag',
                    name: 'Name',
                    create_tags: 'Create Tags',
                    choose_tag: 'Choose Tag'
                },
            },
            ja: {
                message: {
                    tag: '????????????',
                    add_above_tag: '????????????',
                    remove_above_tag: '?????????',
                    name: '?????????',
                    create_tags: '????????????????????????',
                    choose_tag: '??????????????????'
                }
            }
        }
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
            this.loadingCount++
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
            .finally(() => this.loadingCount--)
        },
        reloadTag() {
            self = this
            this.loadingCount++
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
            .finally(() => this.loadingCount--)
        }
    }
});