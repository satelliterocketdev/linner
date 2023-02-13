Vue.component ('survey-questionnaire', {
    template:
    `<div>
        <div class="row p-2">{{$t("message.questionnaire_contents")}}</div>
        <div class="row">

            <div class="col-sm-5">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        {{$t('message.question')}}
                        <textarea id="question" name="question" v-model:value="surveyQuestionnaire.text" class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
                    </div>
                    <!-- 回答リスト start -->
                    <div v-for="action in surveyQuestionnaire.actions" class="card-body action_list" @click="selectAction(action.action_no)" :class="{ selectAction: action.select }">
                        {{$t('message.action')}}{{action.action_no}}
                        <p>{{action.label}}</p>
                        <p v-if="action.is_label_error" class="error">回答ラベルは必須です。</p>
                        <p v-if="action.is_data_error" class="error">アクション先を入力してください</p>
                        <div><button type="button" class="btn rounded-red float-right" @click="deleteAction(action)">-</button></div>
                    </div>
                    <!-- 回答リスト end -->
                    <div v-if="addButton" class="card-footer bg-light">
                        <div class="row justify-content-center">
                            <button type="button" class="btn rounded-green" @click="addAction(surveyQuestionnaire.actions.length)">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-7">
                <div v-for="(action, index) in filteraction" class="card shadow" v-if="'actionCard'+index">
                    <input type="hidden" v-model="action.type">
                    <div class="card-header">
                        {{$t('message.action')}} {{ action.action_no }}
                        <textarea v-model="action.label" class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
                    </div>
                    <div class="card-body">
                        <div class="row" >
                            <div class="col-sm-5">
                                <p>{{$t('message.actions')}}</p>
                                <!-- ビヘイビア start -->
                                    <label 
                                        v-for="(behavior_item, index) in behavior_list" 
                                        :class=" action.behavior == behavior_item ? 'radio_select' : '' " 
                                        class="actions_btn btn btn-block btn-outline-dark" 
                                        :for="'behavior_' + index + action.action_no"
                                        @click="showModalBehavior(behavior_item, action.action_no - 1)"
                                    >
                                        <input 
                                            type="radio" 
                                            :name="'behavior_' + action.action_no" 
                                            :id="'behavior_' + index + action.action_no" 
                                            v-model="action.behavior" 
                                            :value="behavior_item"
                                        >
                                        {{$t('message.'+ behavior_item)}}
                                    </label>
                                <!-- ビヘイビア end -->
                                <div>
                                    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="500" :footer="null">
                                        <div class="row justify-content-center" style="font-size: 20px">
                                            <input name="title" v-model:value="action.data" class="borderless-input form-control" type="text" :placeholder="$t('message.please_enter')" style="font-size: 24px">
                                        </div>
                                        <div class="row p-4">
                                            
                                        </div>
                                        <div class="footer">
                                            <div class="row justify-content-center pt-2">
                                                <button @click="handleOk" class="btn rounded-green">完了</button>
                                            </div>
                                        </div>
                                    </a-modal>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="row mb-3">
                                    <addcontent-template @updateFromTemplate="updateFromTemplate"></addcontent-template>
                                    <textarea 
                                        :name="'auto_reply['+ action.action_no +']'" 
                                        :id="'auto_reply_'+action.action_no" 
                                        v-model="action.auto_reply" 
                                        class="form-control auto_reply" 
                                        maxlength="1000" 
                                        rows="3" 
                                        style="resize: none" 
                                        :placeholder="$t('message.auto_reply_text')"
                                    ></textarea>
                                </div>
                                <p>{{$t('message.action')}}</p>
                                <!--追加タグ-->
                                <div class="row mb-3">
                                    <div>{{$t('message.add_tags')}}</div>
                                    <div style="width:100%;">
                                        <div v-for="(serve, key) in defaults.tags.serves">
                                            <a-select
                                                mode="multiple"
                                                style="width: 100%"
                                                v-model="action.tag_add"
                                            >
                                                <a-select-option v-for="(tag, tagKey) in tags" :key="tag.id">
                                                    {{ tag.title }}
                                                </a-select-option>
                                            </a-select>
                                        </div>
                                    </div>
                                </div>
                                <!--除外タグ-->
                                <div class="row">
                                    {{$t('message.released_tags')}}
                                    <div style="width:100%;">
                                        <div v-for="(serve, key) in defaults.tags.serves">
                                            <a-select
                                                mode="multiple"
                                                style="width: 100%"
                                                v-model="action.tag_delete"
                                            >
                                                <a-select-option v-for="(tag, tagKey) in tags" :key="tag.id">
                                                    {{ tag.title }}
                                                </a-select-option>
                                            </a-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <p>{{$t('message.actions_target')}}</p>
                                <p>{{action.data}}</p>
                            </div>
                        </div>
                    </div>
                    <!--
                        <div class="footer">
                            <div class="row justify-content-center p-2">
                                <button type="button" class="btn btn-outline-dark">完了</button>
                            </div>
                        </div>
                    -->
                </div>
            </div>
            <div class="col-sm-12">
                <p>{{$t('message.selection_restriction')}}</p>
                <div class="type_select_restriction">
                    <!-- 選択制限 start -->
                        <label
                            v-for="(restriction_item,index) in type_select_restriction_list" 
                            :class="surveyQuestionnaire.type_select_restriction == restriction_item ? 'radio_select' : '' " 
                            class="actions_btn btn btn-block btn-outline-dark" 
                            :for="'option'+index"
                        >
                            <input
                                type="radio" 
                                v-model="surveyQuestionnaire.type_select_restriction" 
                                :value="restriction_item" 
                                name="type_select_restriction" 
                                :id="'option'+index" 
                                autocomplete="off" checked
                            >
                            {{$t('message.' + restriction_item)}}
                        </label>
                    <!-- 選択制限 end -->
                </div>
    
                {{$t('message.notification_message')}}
                <textarea
                    name="notice_msg" 
                    v-model:value="surveyQuestionnaire.notification_message"
                    class="form-control" 
                    maxlength="1000"
                    rows="3"
                    style="resize: none"
                ></textarea>
            </div>
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    questionnaire_contents: 'Questionnaire contents',
                    question: 'Question',
                    action: 'action',
                    none: 'NONE',
                    url_open: 'URL OPEN',
                    call_tel: 'call_tel',
                    send_mail: 'Send The Message',
                    reply_form: 'Reply Form',
                    scenario_transition: 'Scenario Transition',
                    actions: 'Actions',
                    actions_target: 'Action Target',
                    auto_reply: 'Reply',
                    auto_reply_text: 'Reply Text',
                    template: 'Template',
                    template_select: 'Template Select',
                    add_tags: 'Add Tags',
                    released_tags: 'Released Tags',
                    selection_restriction : 'Selection restriction',
                    no_limit: 'No limit',
                    per_choice: 'Per Choice',
                    per_questionnaire: 'Per Questionnaire',
                    notification_message: 'Notification Message',
                    please_enter: 'Please enter',
                    comment_1:'If the action “None” is selected, the reply text will be displayed on the LINE screen.'
                }
            },
            ja: {
                message: { 
                    questionnaire_contents: 'アンケート内容',
                    question: '質問',
                    action: '回答',
                    none : 'なし',
                    url_open : 'URLを開く',
                    call_tel: '電話を発信',
                    send_mail: 'メールを送信',
                    reply_form: '回答フォーム',
                    scenario_transition: 'シナリオ移動',
                    actions: 'アクション',
                    actions_target: 'アクション先',
                    auto_reply: '返信',
                    auto_reply_text: 'アクションが「なし」の時の返信テキスト',
                    template: 'テンプレート',
                    template_select: 'テンプレートを選択',
                    add_tags: '追加するタグ',
                    released_tags: '解除するタグ',
                    selection_restriction : '選択制限',
                    no_limit: '制限なし',
                    per_choice: '各選択肢につき1回',
                    per_questionnaire: '全アンケートで1つ',
                    notification_message: '通知文面',
                    please_enter: '入力してください',
                    comment_1:'アクション「なし」を選択した場合、返信テキストはLINEの画面上に表示されます。'
                }
            }
        }
    },
    props: ['surveyContents'],
    data() {
        return {
            message: {},
            currentaction: 1,
            behavior_list: [
                'none',      //なし
                'url_open',  //URLを開く
                'call_tel',  //電話を発信
                'send_mail' //メール送信
            ],
            type_select_restriction_list:[
                'no_limit',         //制限なし
                'per_choice',       //各選択肢につき1回
                'per_questionnaire' //アンケートにつき1回
            ],
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
            visible: false,
            surveyQuestionnaire: this.surveyContents ? this.surveyContents : {
                actions: [{
                    action_no: 1,
                    type: 'postback',
                    behavior: 'none',
                    label: null,
                    select: false,
                    auto_reply: null
                }]
            }
        }
    },
    computed: {
        actionCount: function () {
            return this.surveyQuestionnaire.actions.length
        },
        filteraction: function () {
            let currentaction = this.currentaction;
            return this.surveyQuestionnaire.actions.filter(function (action) {
                return action.action_no == currentaction;
            })
        },
        addButton: function () {
            if(this.actionCount == 4){
                return false
            }else{
                return true
            }
        }
    },
    created() {
        this.reloadTag()
    },
    methods: {
        addAction(length) {
            this.surveyQuestionnaire.actions.push({
                action_no: length+1,
                type: 'postback',
                behavior: 'none',
                label: null,
                select: false,
                auto_reply: null
            })

            this.currentaction = length+1
            if(length == 4){
                this.addButton = false
            }

            this.surveyQuestionnaire.actions.forEach(function( value ) {
                if (length+1 == value.action_no ) {
                    value.select = true
                }else{
                    value.select = false
                }
           })
        },
        deleteAction(action) {
            const index = this.surveyQuestionnaire.actions.indexOf(action)
            this.surveyQuestionnaire.actions.splice(index, 1)
            if(this.surveyQuestionnaire.actions.length <= 3){
                this.addButton = true
            }
        },
        selectAction(action_no){
            this.currentaction = action_no
            this.surveyQuestionnaire.actions.forEach(function( value ) {
                if (action_no == value.action_no ) {
                    value.select = true
                }else{
                    value.select = false
                }
           })
        },
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
        addExclude() {
            this.defaults.tags.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.tags.excludes.indexOf(exclude)
            this.data.tags.excludes.splice(index, 1)
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
                response.data.tags.forEach(function(value, index){
                    if (self.tags) {
                        self.tags.push({
                            id: value.id,
                            title: value.title,
                        })
                    }
                })
            })
        },
        updateFromTemplate(template) {
            //配列キーを指定だと、削除等で順序が変わった際に問題が発生するので、action_noを使って値を入れる方法にしています。inatomi
            this.surveyQuestionnaire.actions.forEach(function(value,index){
                if(this.currentaction == value.action_no){
                    this.surveyQuestionnaire.actions[index].auto_reply = template.content_message
                }
            } ,this)
        },
        showModalBehavior(behavior_item, index) {
            if (behavior_item != 'none') {
                this.visible = true
                this.surveyQuestionnaire.actions[index].type = 'uri'
            } else {
                this.surveyQuestionnaire.actions[index].type = 'postback'
            }
        },
        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
        updateFromDraft(content) {
            Object.assign(content, this.surveyQuestionnaire)
        }
    }
});