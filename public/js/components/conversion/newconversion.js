Vue.component('conversion-delete-button',{
    i18n: { // `i18n` option, setup locale info for component
      messages: {
        en: { 
          message: { 
            button_name: 'Delete',
            delete_confirm_message1: 'The data measured so far is also deleted.',
            delete_confirm: 'Are you sure you want to delete?',
            confirm_no : 'Cancel',
            confirm_yes: 'OK',
          },
        },
        ja: {
          message: { 
            button_name: 'コンバージョンを削除',
            delete_confirm_message1: '過去に計測されたデータも表示されなくなります。',
            delete_confirm: '本当に削除してよろしいですか？',
            confirm_no : 'Cancel',
            confirm_yes: 'ＯＫ',
          },
        }
      }
    },
    data(){
      return{
        visible: false,
        buttonName: this.$i18n.t('message.button_name')
      }
    },
    methods:{
      showModal() {
        this.visible = true
      },
      confirm(){
        this.$emit('confirm')
        this.visible = false
      },
      cancel(){
        this.$emit('cancel')
        this.visible = false
      },
    },
    template:
    `<div>
    <a-button @click="showModal" class="delete-button font-size-table">{{ buttonName }}</a-button>
    <a-modal :centered="true" v-model="visible" :width="450" :footer="null" :maskClosable="false" :destroyOnClose="true">
        <div id="message-content" class="p-2" style="text-align:center; ">
          <div class="m-3">
            <span style="white-space: pre;">{{ $t('message.delete_confirm_message1') }}</span>
          </div>
          <div>
            <span>{{ $t('message.delete_confirm') }}</span>
          </div>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center">
                <button type="button" class="btn m-2 px-5 rounded-white" @click="cancel"> {{$t('message.confirm_no')}} </button>
                <button type="button" class="btn m-2 px-5 rounded-red" @click="confirm"> {{$t('message.confirm_yes')}} </button>
            </div>
        </div>
    </a-modal>
    </div>`,
});

const conversionForm = {
    template: 
    `<form id="conversionForm" type="post" v-on:submit.prevent>
        <div class="row m-2">
            <span v-if="editMode">{{$t('message.edit_conversion')}}</span>
            <span v-else>{{$t('message.register_conversion')}}</span>
        </div>
        <div class="form-group m-2">
            <input name="title" type="text" class="borderless-input form-control" placeholder="Title" v-model="title" style="font-size: 24px">
        </div>
        <hr>
        <div class="row m-2">
            <span>{{$t('message.url')}}</span>
        </div>
        <div class="row m-2">
            <input v-model="url" type="text" class="form-control" readonly>
        </div>
        <div class="row m-2">
            <span>{{$t('message.redirect_url')}}</span>
        </div>
        <div class="row m-2">
            <input name="redirect_url" v-model="redirect_url" type="text" class="form-control">
        </div>
        <div class="m-2">
            <a-button @click="showReactionModal">{{$t('message.setting_reaction')}}</a-button>
            <action-modal ref="reactionModal" @completion="updateAction" v-model:loading-count="loadingCountData"></action-modal>
        </div>
        <div class="form-group m-2">
            <input name="reaction" type="hidden" class="form-control" :value="hasReaction">
            <ul style="list-style-position : inside;">
            <li v-if="showTagAction">{{ $t('message.setting_tag') }}
                <div>
                    <span v-for="(serves,i) in actions.tags.serves" :key="'tag-'+i">
                        <span v-for="(val, j) in serves.value" :key="'tag-'+i+'-'+j">
                        <span v-if="'tag-'+i+'-'+j!='tag-'+0+'-'+0">,</span>
                        <template v-if="serves.option == 'first'">
                        {{ $t('message.addAction_message', { msg: val }) }}
                        </template>
                        <template v-if="serves.option == 'second'">              
                        {{ $t('message.removeAction_message', { msg: val }) }}
                        </template>
                        </span>
                    </span>
                </div>
            </li>
            <li v-if="showScenarioAction">{{ $t('message.setting_scenario') }}
                <div>
                    <span v-for="(serves,i) in actions.scenarios.serves" :key="'sce-'+i">
                        <span v-for="(val, j) in serves.value" :key="'sce-'+i+'-'+j">
                        <span v-if="'sce-'+i+'-'+j!='sce-'+0+'-'+0">,</span>
                        <template v-if="serves.option == 'first'">
                        {{ $t('message.addAction_message', { msg: val }) }}
                        </template>
                        <template v-if="serves.option == 'second'">              
                        {{ $t('message.removeAction_message', { msg: val }) }}
                        </template>
                        </span>
                    </span>
                </div>
            </li>
            </ul>
        </div>
        <div v-if="editMode" class="d-flex flex-row-reverse">
            <conversion-delete-button @confirm="remove"></conversion-delete-button>
        </div>  
        <hr>
        <div class="footer">
            <div class="row justify-content-center mx-1">
                <a-button class="rounded-red mr-1" @click="reset">{{$t('message.reset')}}</a-button>
                <a-button v-if="editMode" class="rounded-green" @click="update">{{ $t('message.update') }}</a-button>
                <a-button v-else class="rounded-green" @click="register">{{ $t('message.register') }}</a-button>
            </div>
        </div>
    </form>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
          en: { 
            message: { 
                new: 'New',
                register_conversion: 'Register Conversion',
                edit_conversion: 'Edit Conversion',
                url: 'URL',
                redirect_url: 'Redirect URL',
                tag: 'Tag',
                scenario: 'Scenario',
                survey: 'Survey',
                menu: 'Menu',
                register: 'Register',
                update: 'Update',
                delete: 'Delete',
                reset: 'Reset',
                show: 'Show',
                setting_reaction: 'Setting Reaction',
                addAction_message: 'Add {msg}',
                removeAction_message: 'Remove {msg}',
                setting_tag :'Tag',
                setting_scenario:'Scenario',
            } 
          },
          ja: {
            message: { 
                new: '新規',
                register_conversion: 'コンバージョン新規登録',
                edit_conversion: 'コンバージョン編集',
                url: 'URL',
                redirect_url: 'リダイレクト先URL',
                tag: 'タグ',
                scenario: 'シナリオ',
                survey: 'アンケート',
                menu: 'メニュー',
                register: '登録',
                update: '更新',
                delete: 'コンバージョンを削除',
                reset: 'リセット',
                show: '表示',
                setting_reaction: 'リアクションを選択する',
                addAction_message: '{msg}を追加',
                removeAction_message: '{msg}を除去',
                setting_tag :'タグ設定',
                setting_scenario:'シナリオ設定',
            }
          }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props:{ 
        initialData: {
            type: Object,
        },
        editMode: false,
        loadingCount: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            showTagAction: false,
            showScenarioAction: false,
            
            id: null,
            title: '',
            conversion_token: null,
            url: '',
            redirect_url: '',
            actions: null,
        }
    },
    watch: {
        initialData: {
            handler(){
                this.reset()
            },
            immediate: true,
        },
    },
    computed: {
        hasReaction(){
            return (this.showTagAction || this.showScenarioAction) ? '1' : ''
        },
        loadingCountData: {
            get() {
                return this.loadingCount
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    },
    mounted(){
        let form = $("#conversionForm")
        // validationの設置 
        form.validate({
            ignore: [],// hiddenの項目チェックも行うように上書き
            rules: {
                title: "required",
                redirect_url: {required : true, url : true},
                reaction: "required",
            }
        })
    },
    methods:{
        // リアクション設定関連
        updateLayout(){
            this.showTagAction = this.layoutCheck(this.actions.tags)
            this.showScenarioAction = this.layoutCheck(this.actions.scenarios)
        },
        layoutCheck(item){
            return item.serves.findIndex((s) => s.value.length > 0 ) !== -1
        },
        showReactionModal(){
            this.$refs.reactionModal.showModal(this.actions)
        },
        updateAction(info){
            this.actions = info
            this.updateLayout()
        },
        // 登録、更新、削除
        register() {
            let form = $("#conversionForm")
            if (!form.valid()) {
                return
            }    
    
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("conversion", this.$data)
            .then(function(res){
                self.$emit('completion', res)
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        update() {
            let form = $("#conversionForm")
            if (!form.valid()) {
                return
            }

            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("conversion/" + this.id, this.$data)
            .then(function(res){
                self.$emit('completion', res)
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        remove(){
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.delete("conversion/" + this.id)
            .then(function(res){
                self.$emit('completion', res)
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        // リセットボタン
        reset() {
            Object.assign(this.$data , _.cloneDeep(this.initialData))

            this.updateLayout()
        },
    }
}

Vue.component ('new-conversion', {
    template: 
    `<div>
        <a-modal :centered="true" v-model="visible" :width="600" :afterClose="afterClose" :footer="null">
        <div v-if="!showing"></div>
        <div v-else-if="loading">Loading...</div>
        <div v-else>
           <conversion-form :initial-data="convData" :edit-mode="editMode" @completion="completion" v-model:loading-count="loadingCountData"></conversion-form>
        </div>
        </a-modal>
    </div>`,
    components: {
        'conversion-form' : conversionForm,
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['loadingCount'],
    data() {
        return {
            showing: true,　// modalが開いたとき〜modalが閉じた後までを管理。
            visible: false,
            convData: null,
            loading : false,
            editMode: false,
        }
    },
    methods: {
        openNotificationWithIcon (type, message, desc) {
            this.$notification[type]({
                message: message,
                description: desc,
            });
        },
        initialData(){
            return { 
                title: '',
                conversion_token: null,
                url: '',
                redirect_url: '',
                actions: {
                    tags: {
                        serves: [{
                            value: [],
                            option: 'first'
                        }],
                        excludes: [{
                            value: [],
                            option: 'first'
                        }]
                    },
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
            }
        },
        convertActions(conv){
            //convDataのフォーマットに書き換える
            var res = {
              id: conv.id,
              title: conv.title,
              conversion_token: conv.conversion_token,
              url: conv.url,
              redirect_url: conv.redirect_url,
              actions: {
                  tags: {
                    serves: [],
                  },
                  scenarios: {
                    serves: [],
                  }
              },
            }

            // actionsの展開
            conv.formatted_conversion_actions.tag.forEach((serve, index) => {
              res.actions.tags.serves.push({option: serve.option , value: serve.value})
            })
      
            conv.formatted_conversion_actions.scenario.forEach((serve, index) => {
              res.actions.scenarios.serves.push(
                {
                  option: serve.option ,
                  value: serve.value ,
                  delivery: {
                    timing: '',
                    number: '',
                  }
                }
              )
            })
            if(res.actions.tags.serves.length == 0){
              res.actions.tags.serves = [ {value: [], option: 'first'} ]
            }
            if(res.actions.scenarios.serves.length == 0){
              res.actions.scenarios.serves = [
                { value: [],
                  option: 'first',
                  delivery: {
                    timing: '',
                    number: '',
                  }
                }
              ]
            }
            return res
        },
        getData(convId){
            // 改めてコンバージョン情報を取得する
            var self = this
            self.loading = true
            this.$emit('input', this.loadingCount + 1);
            axios.get("/conversion/edit/"+convId)
            .then(res=> {
                self.convData = this.convertActions(res.data)
            })
            .catch(e=> self.openNotificationWithIcon('error','An Error Occurred'))
            .finally(() => {
                self.loading = false
                this.$emit('input', this.loadingCount - 1)
            })
        },
        // 新規時のトークン取得
        generateToken() {
            var self = this
            self.loading = true
            this.$emit('input', this.loadingCount + 1)
            axios.get("conversion/generate_token")
            .then(response => {
                var data = this.initialData()
                data.conversion_token = response.data.conversion_token
                data.url = response.data.url
                self.convData = data
            })
            .catch(e=> self.openNotificationWithIcon('error','An Error Occurred'))
            .finally(() => {
                self.loading = false
                this.$emit('input', this.loadingCount - 1)
            })
        },
        showModal(convId) {
            this.showing = true
            this.visible = true
            this.editMode = convId != null
      
            this.convData = this.initialData()
            if(convId){
                this.getData(convId)
            } else {
                this.generateToken()
            }
        },
        hideModal() {
            this.visible = false
        },
        afterClose(){
            this.showing = false
        },
        completion(res){
            this.$emit('completion', res)
            this.hideModal()
        }
    },
    computed: {
        loadingCountData: {
            get() {
                return this.loadingCount
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    }
});
