Vue.component ('new-magazine-many', {
    template:
    `<div>
    <button v-if="type == 'New'" @click="showModal" v-bind:class="buttonclass">{{$t('message.new')}}</button>
    <button v-else @click="showModal" v-bind:class="buttonclass" class="mb-1 font-size-table">{{$t('message.edit')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" width="100%" style="max-width: 900px" :footer="null" :destroyOnClose="true">

    <form id="magazineForm" method="post" v-on:submit.prevent>
        <div style="margin:0 0 20px; font-size:1.2rem;">
            <span v-if="type == 'New'">{{$t("message.new_magazine_title")}}</span>
            <span v-else>{{$t("message.edit_magazine_title")}}</span>
            <b><input name="title" v-model:value="title" class="borderless-input form-control" type="text" :placeholder="$t('message.title')" style="font-size: 24px"></b>
        </div>

        <div v-if="type == 'New'" class="row" style="margin:0 0 20px;">
            <span>対象アカウント</span>
            <template >
                <a-select mode="multiple" style="width: 100%" v-model="selectAccount" >
                    <a-select-option v-for="(account, tagKey) in accountsList" :key="account.id">
                        {{ account.name }}
                    </a-select-option>
                </a-select>
            </template>
        </div>

        <!-- 配信日時 -->
        <div class="row" style="margin:0 0 10px;">
            <div class="col-sm-2 px-0 mb-1">{{$t('message.delivery_date')}}</div>
            <div class="col-sm-3 px-0 mr-1 mb-1"><a-date-picker format="YYYY-MM-DD" v-model="schedule_at" :placeholder="$t('message.date')" /></div>
            <div class="col-sm-3 px-0"><a-time-picker v-model="schedule_at" format="HH:mm" :placeholder="$t('message.time')" /></div>
        </div>

        <div class="row align-items-center mb-1">
            <div class="col-12 col-md-6 align-items-center">
                {{$t('message.delivery_completion')}}
            </div>
            <div class="col-10 col-md-4 align-items-center">
                <span v-for="(targetServe, key) in targetServes">
                    <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ targetServe }} {{ key+1 }}
                </span>
                <span v-if="date">&nbsp; {{ date }} &nbsp;</span>
            </div>
            <div class="col-2 align-items-center">
                <div class="row justify-content-end">
                    <message-target-many v-if="type == 'New'" :data="target" :reset="clearTarget" :type="type" :update-data="updateTarget"> </message-target-many>
                    <message-target-many v-else :data="target" :reset="clearTarget" :type="type" :account_id="data.account_id" :update-data="updateTarget"> </message-target-many>
                    <!-- <button type="button" class="btn rounded-white" @click="sendToAll = !sendToAll">{{$t('message.send_to_all')}}</button> -->
                </div>
            </div>
        </div>
        <div v-if="type == 'Edit'" class="row align-items-center">
            <div class="col-12 col-md-6 align-items-center">
                {{$t('message.delivery_completion_action')}}
            </div>
            <div class="col-8 col-md-4 align-items-center">
                <span v-for="(actionServe, key) in actionServes">
                    <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ actionServe }} {{ key+1 }}
                </span>
            </div>
            <div class="col-4 col-md-2 align-items-center">
                <div class="row justify-content-end">
                    <message-action-many :data="action" :reset="clearAction" :type="type" :account_id="data.account_id" :update-data="updateAction"></message-action-many>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center p-1">
            <button v-bind:class="{ 'rounded-white': currentComponent !== buttons[0].name, 'rounded-blue': currentComponent === buttons[0].name }" type="button" v-on:click="switchComponent(0)" class="btn rounded-blue m-1">{{$t('message.normal_delivery_statement')}}</button>
            <!--<button type="button" @click="switchComponent(1)" class="btn rounded-blue m-1">{{$t('message.carousel')}}</button>-->
            <button v-if="type == 'Edit'" v-bind:class="{ 'rounded-white': currentComponent !== buttons[2].name, 'rounded-blue': currentComponent === buttons[2].name }" type="button" v-on:click="switchComponent(2)" class="btn m-1">{{$t('message.questionnaire')}}</button>
            <!--<button type="button" @click="switchComponent(3)" class="btn rounded-blue m-1">{{$t('message.map')}}</button>-->
            <!--<button type="button" v-on:click="switchComponent(4)" class="btn rounded-blue m-1">{{$t('message.introduction')}}</button> -->
        </div>
        <!--CONTENT PANEL-->
        <div id="content-panel" class="p-1">
            <keep-alive>
                <component
                    :survey-contents="content.surveyQuestionnaire"
                    :data="content"
                    :type="type"
                    ref="aliveComponent"
                    @updateTextInput="getContents"
                    v-bind:is="currentComponent"
                    urlclick-action="true"
                ></component> 
            </keep-alive>
        </div>
        <div class="footer pt-1">
            <div class="row align-items-end">
                <div class="col-sm-8">
                    <div class="d-flex justify-content-start">
                        <!--button type="button" class="btn rounded-white mx-1" @click="close">Save and Exit</button>-->
                        <button type="button" class="btn rounded-blue mx-1" @click="register">{{$t('message.delivery_registration')}}</button>
                        <!--<button type="button" class="btn rounded-green mx-1" @click="saveAsDraft">{{$t('message.save_as_draft')}}</button>-->
                        <!--<confirmation-test :data="data" v-bind:btnclass="RoundedRed"></confirmation-test>-->
                    </div>
                </div>
                 <div class="col-sm-4">
                    <!--<select-draft :data="data" :message="message" @update-from-draft="updateFromDraft"></select-draft>-->
                    <!--<button type="button" class="btn rounded-white btn-block my-1" @click="saveTemplate">{{$t('message.save_as_template')}}</button>-->
                </div>
            </div>
        </div>
    </form>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    new: 'New',
                    edit: 'Edit',
                    new_magazine_title: "New Magazine Title",
                    edit_magazine_title: "Edit Magazine Title",
                    title: "Title",
                    delivery_completion: 'Delivery Completion',
                    delivery_completion_action: 'Delivery Completion Action',
                    send_to_all: 'Send to All',
                    // 配信対象
                    target_tag: 'Tag',
                    target_scenario: 'Scenario',
                    target_follower: 'Follower',
                    target_date: 'Register Date',
                    // メッセージ
                    normal_delivery_statement: 'Create Message',
                    carousel: 'Create Carousel',
                    questionnaire: 'Questionnaire',
                    map: 'Add Map',
                    delivery_registration: 'Send / Schedule send',
                    save_as_draft: 'Save as draft',
                    save_as_template: 'Save as template',
                    register: "Register",
                    deliveryTiming: 'Delivery Timing',
                    immediatelyAfterDelivery: 'Immediately after delivery',
                    specifiedTime: 'Specified Time',
                    message_number: 'Message Number',
                    date: 'Date',
                    time: 'Time',
                    delivery_date: 'Delivery date',
                    att: 'Attachment'
                }
            },
            ja: {
                message: {
                    new: '新規',
                    edit: '編集',
                    new_magazine_title: '一斉送信メッセージ新規登録',
                    edit_magazine_title: '一斉送信メッセージ編集',
                    title: "タイトル",
                    delivery_completion: '配信対象設定追加',
                    delivery_completion_action: '配信完了時アクション',
                    send_to_all: '全員',
                    // 配信対象
                    target_tag: 'タグ',
                    target_scenario: 'シナリオ',
                    target_follower: '友達',
                    target_date: '登録日',
                    // メッセージ
                    normal_delivery_statement: '通常配信文', //Create Message
                    carousel: 'カルーセル',
                    questionnaire: 'アンケート',
                    map: 'マップ',
                    delivery_registration: '配信登録', //Send / Schedule send
                    save_as_draft: '下書きを保存',
                    save_as_template: 'テンプレート保存',
                    register: "登録",
                    deliveryTiming: '配信タイミング',
                    immediatelyAfterDelivery: '配信直後',
                    specifiedTime: '日時指定',
                    message_number: '通目',
                    date: '日にち',
                    time: '時間',
                    attachment: '添付',
                    delivery_date: '配信日時',
                    att: '添付'
                }
            }
        }
    },
    model: {
        prop : 'loadingCount',
        event : 'input'
    },
    props: ['data', 'btnclass', 'reloadMagazines', 'type', 'loadingCount', 'accounts-list'],
    data() {
        return {
            // contentpanel-createmessageデータ受け取り用
            content: {
                title: null,
                content_message: '',
                is_active: true,
                is_draft: false,
                attachments: [],
                content_type: 'message',
                url_actions: {},
                surveyQuestionnaire:
                {
                    text: '',
                    notification_message:'',
                    type_select_restriction:'no_limit',
                    actions:[
                        {
                            action_no: '1',
                            type: 'postback',
                            behavior:'none',
                            label:'',
                            data: '',
                            auto_reply:'',
                            tag_add:[],
                            tag_delete:[],
                            select: true
                        }
                    ]
                }
            },
            sendToAll: false,
            selectAccount:[],
            title: '',
            target: {},
            action: {},
            message: [],
            schedule_at: null,
            message_type: 'New',
            message_selected: [],
            visible: false,
            buttonclass: "btn mx-1 " + this.btnclass,
            RoundedGreen: "rounded-green",
            RoundedWhite: "rounded-white",
            targetServes: [],
            actionServes: [],
            messageServes: [],
            date: '',
            currentComponent: 'contentpanel-createmessage',
            RoundedRed: "rounded-red",
            show: false,
            option: 'option1',
            buttons: [
                { index:0, name:'contentpanel-createmessage'},
                { index:1, name:'addcontent-main'},
                { index:2, name:'survey-questionnaire'}
            ]
        }
    },
    created() {
        this.render()
    },
    updated() {
        this.render()
    },
    methods: {
        close() {},
        render() {},
        updateTarget() {
            tmp = this.title
            this.title = ' '
            this.title = tmp
            magazine = this
            magazine.targetServes = []
            $.each(this.target, function(section, temp){
                $.each(temp.serves, function(key, serve){
                    serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function(key, serve_child){
                            magazine.targetServes.push(serve_child)
                        })
                    } else {
                        if (section != 'dates') {
                            magazine.targetServes.push(serve_value)
                        } else {
                            if (typeof serve_value.from !== 'undefined' || typeof serve_value.to !== 'undefined') {
                                magazine.date = serve_value.from.format('YYYY-MM-DD') + ':' + serve_value.to.format('YYYY-MM-DD');
                            }
                        }
                    }
                })
            })
        },
        saveAsDraft() {

            form = $("#magazineForm")
            form.validate({
                rules: {
                    //title: "required",
                    message: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            if (this.id) {
                this.update()
                return
            }

            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("magazine/draft_messages", {
                title: this.title,
                content_type: this.content.content_type,
                content_message: this.content.content_message,
                url_actions: this.content.url_actions,
                is_active: this.content.is_active,
                is_draft: this.content.is_draft,
                schedule_at: this.schedule_at,
                attachments: this.content.attachments,
                target: JSON.stringify(this.target),
                action: JSON.stringify(this.action),
                survey_questionnaire: this.content.surveyQuestionnaire,
                is_draft: 1,
            })
            .then(function(response){
                self.reloadMagazines()
                self.reset()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        saveTemplate() {
            form = $("#magazineForm")

            form.validate({
                rules: {
                    title: "required",
                    message: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            this.content.title = this.title

            this.$emit('input', this.loadingCount + 1)
            axios.post("magazine/template", this.content)
            .then(response => {
                this.visible = false
            })
            .finally(() => this.$emit('input' , this.loadingCount - 1))
        },
        getContents(content) {
            this.content.content_message = content.content_message;
            this.content.attachments = content.attachments;
            this.content.content_type = content.content_type;
            this.content.url_actions = content.url_actions;
        },
        updateAction() {
            // this will update the tags
            tmp = this.title
            this.title = ' '
            this.title = tmp
            magazine = this
            magazine.actionServes = []
            $.each(this.action, function(section, temp){
                $.each(temp.serves, function(key, serve){
                    serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function(key, serve_child){
                            magazine.actionServes.push(serve_child)
                        })
                    } else {
                        if (section !== 'dates') {
                            magazine.actionServes.push(serve_value)
                        }
                    }
                })
            })
        },
        clearTarget() {
            this.target = {}
        },
        clearAction() {
            this.action = {}
        },
        reset() {
            this.id = 0
            this.title = ''
            this.target = {}
            this.action = {}
            this.message = []
            this.message_selected = []
            this.schedule_at = null
            this.targetServes = []
            this.actionServes = []
        },
        showModal() {
            this.visible = true
            if (this.data) {
                switch(this.data.content_type) {
                    case 'survey':
                        this.currentComponent = 'survey-questionnaire';
                        break;
                    default:
                        this.currentComponent = 'contentpanel-createmessage';
                }

                this.id = this.data.id
                this.title = this.data.title

                if (this.data.schedule_at) {
                    this.schedule_at = moment(new Date(this.data.schedule_at))
                }
                if (this.data.target.dates.serves.length > 0) {
                    for (let i = 0; i < this.data.target.dates.serves.length; i++) {
                        if (this.data.target.dates.serves[i].value.hasOwnProperty('from')) {
                            this.data.target.dates.serves[i].value.from = moment(new Date(this.data.target.dates.serves[i].value.from))
                            this.data.target.dates.serves[i].value.to = moment(new Date(this.data.target.dates.serves[i].value.to))
                        }
                    }
                    for (let i = 0; i < this.data.target.dates.excludes.length; i++) {
                        if (this.data.target.dates.excludes[i].value.hasOwnProperty('from')) {
                            this.data.target.dates.excludes[i].value.from = moment(new Date(this.data.target.dates.excludes[i].value.from))
                            this.data.target.dates.excludes[i].value.to = moment(new Date(this.data.target.dates.excludes[i].value.to))
                        } 
                    }
                }
                this.target = this.data.target
                this.action = this.data.action
                this.content.content_message = this.data.content_message // JSON.parse(this.data.message)
                this.content.attachments = this.data.attachments
                this.content.url_actions = this.data.url_actions
                this.content.content_type = this.data.content_type
                this.content.surveyQuestionnaire = this.data.surveyQuestionnaire
                this.message_selected = []

                this.updateTarget()
                this.updateAction()
            }
        },
        handleOk(e) {
            this.visible = false
        },
        register() {

            form = $("#magazineForm")

            let accountsListValidate = this.accountsList ? "required" : ""

            form.validate({
                rules: {
                    title: "required",
                    message: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            if (this.id) {
                this.update()
                return
            }

            let postUrl = this.accountsList == null ? "magazine" : "magazine/store_many" // magazine = 一斉配信、magazine/store_many = 全アカウント配信
            
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post(postUrl, {
                title: this.title,
                content_type: this.content.content_type,
                content_message: this.content.content_message,
                url_actions: this.content.url_actions,
                is_active: this.content.is_active,
                is_draft: this.content.is_draft,
                schedule_at: this.schedule_at,
                attachments: this.content.attachments,
                target: JSON.stringify(this.target),
                action: JSON.stringify(this.action),
                survey_questionnaire: this.content.surveyQuestionnaire,
                select_account: this.selectAccount
            })
            .then(function(response){
                self.reloadMagazines()
                self.reset()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        update() {
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("magazine/" + self.id, {
                title: this.title,
                content_type: this.content.content_type,
                content_message: this.content.content_message,
                url_actions: this.content.url_actions,
                is_active: this.content.is_active,
                is_draft: this.content.is_draft,
                schedule_at: this.schedule_at,
                attachments: this.content.attachments,
                target: JSON.stringify(this.target),
                action: JSON.stringify(this.action),
                survey_questionnaire: this.content.surveyQuestionnaire,
                is_many:true,
                account_id: this.data.account_id
            })
            .then(function(response){
                self.reloadMagazines()
                self.reset()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        addMessage(event) {
            alert(event)
        },
        switchComponent(componentIndex)
        {
            this.currentComponent = this.buttons[componentIndex].name
            if(this.currentComponent == 'contentpanel-createmessage'){
                this.content.content_type = 'message'
            }else if(this.currentComponent == 'survey-questionnaire'){
                this.content.content_type = 'survey'
            }
        },
        changeOption(value) {
            this.option = 'option' + value,
            this.show = true
            if (value == '2') {
                $(".datetimepicker").datetimepicker()
            }
            Object.assign(this.content, {
                schedule_type: value
            })
        },
        updateFromTemplate(template) {
            this.title = template.title
            this.content.content_message = template.content_message;
            this.content.attachments = template.attachment;
            this.content.content_type = template.content_type;
            // TODO: 将来、currentComponentを調べる必要があると思う
            this.$refs.aliveComponent.updateFromTemplate(this.content);
        },
        updateFromDraft(content) {
            this.id = content.id
            this.title = content.title

            if (content.schedule_at) {
                this.schedule_at = moment(new Date(content.schedule_at))
            }

            this.content.content_type = content.content_type
            this.target = content.target
            this.action = content.action
            this.content.url_actions = content.url_actions
            this.message_selected = []

            this.updateTarget()
            this.updateAction()

            switch (this.content.content_type) {
                case 'survey':
                    this.currentComponent = 'survey-questionnaire'
                    this.content.surveyQuestionnaire = content.surveyQuestionnaire
                    this.$refs.aliveComponent.updateFromDraft(this.content.surveyQuestionnaire)
                    break
                default:
                    this.currentComponent = 'contentpanel-createmessage'
                    this.content.content_message = content.content_message
                    this.content.attachments = content.attachments
                    this.$refs.aliveComponent.updateFromTemplate(this.content)
            }
        }
    }
});
