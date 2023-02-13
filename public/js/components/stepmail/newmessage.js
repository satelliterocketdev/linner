Vue.component('new-message', {
    template:
    `<div>
        <button v-if="type == 'New'" type="button" @click="showModal" v-bind:class="buttonclass">{{$t('message.add_message')}}</button>
        <button v-if="type == 'Edit'" type="button" @click="showModal" v-bind:class="buttonclass" class="small-text font-size-table px-small">{{$t('message.edit_message')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true" :afterClose="onAfterClose">
            <form id="messageForm" type="post" v-on:submit.prevent>
                <div>
                    {{$t('message.delivery_statement_setting')}}
                    <b><h5><input class="borderless-input form-control" name="title" type="text" :placeholder="$t('message.title')" v-model="content.title"></h5></b>
                </div>
                <div class="row p-1">
                    <keep-alive>
                        <newmessage-slidebox :data="content" v-model:loading-count="loadingCountData"> </newmessage-slidebox>
                    </keep-alive>
                </div>
                <hr>
                <div class="row justify-content-center p-1">
                    <button v-bind:class="{ 'rounded-white': currentComponent !== buttons[0].name, 'rounded-blue': currentComponent === buttons[0].name }" type="button" v-on:click="switchComponent(0)" class="btn rounded-blue m-1">{{$t('message.normal_delivery_statement')}}</button>
                    <!-- <button type="button" v-on:click="switchComponent(1)" class="btn rounded-blue m-1">{{$t('message.carousel')}}</button> -->
                    <button v-bind:class="{ 'rounded-white': currentComponent !== buttons[2].name, 'rounded-blue': currentComponent === buttons[2].name }" type="button" v-on:click="switchComponent(2)" class="btn m-1">{{$t('message.questionnaire')}}</button>
                    <!-- <button type="button" v-on:click="switchComponent(3)" class="btn rounded-blue m-1">{{$t('message.map')}}</button> -->
                    <!-- <button type="button" v-on:click="switchComponent(4)" class="btn rounded-blue m-1">{{$t('message.introduction')}}</button> -->
                </div>
                <!--CONTENT PANEL-->
                <div id="content-panel" class="p-1">
                    <keep-alive>
                        <component :survey-contents="content.surveyQuestionnaire" ref="aliveComponent" v-bind:is="currentComponent" :data="content" :type="type" @updateTextInput="getContents" urlclick-action="true" v-model:loading-count="loadingCountData"></component>
                    </keep-alive>
                </div>
                <div class="footer pt-1">
                    <div class="row align-items-end">
                        <div class="col-sm-8">
                            <div class="d-flex justify-content-start">
                                <!-- <button type="button" class="btn rounded-white mx-1" @click="close">Save and Exit</button> -->
                                <button type="button" class="btn rounded-blue mx-1" v-on:click="register">{{$t('message.delivery_registration')}}</button>
                                <button type="button" class="btn rounded-green mx-1" @click="saveAsDraft">{{$t('message.save_as_draft')}}</button>
                                <!-- <confirmation-test :data="content" v-bind:btnclass="RoundedRed"></confirmation-test> -->
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <select-draft :data="content" :message="message" @update-from-draft="updateFromDraft" v-model:loading-count="loadingCountData"></select-draft>
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
                    delivery_statement_setting: 'Delivery Statement Setting',
                    title: "Title",
                    delivery_timing: '',
                    immediately_after_delivery: 'Immediately after delivery',
                    specified_time: ' Specified Time',
                    normal_delivery_statement: 'Create Message',
                    carousel: 'Create Carousel',
                    questionnaire: 'Add Survey',
                    map: 'Add Map',
                    introduction: 'Add Contact',
                    save_as_draft: 'Save as draft',
                    create_from_draft: 'Select draft',
                    save_as_template: 'Save as template',
                    delivery_registration: 'Send / Schedule send',
                    save_and_exit: 'Save and Exit',
                    add_message: 'Add Message', // New
                    edit_message: 'Edit Message', // Edit
                    att: 'Attachment',

                    required_content_message: 'This field is required.',
                }
            },
            ja: {
                message: {
                    delivery_statement_setting: '配信文設定',
                    delivery_timing: '',
                    immediately_after_delivery: '配達直後',
                    specified_time: ' 時間指定',
                    normal_delivery_statement: '通常配信文', //Create Message
                    carousel: 'カルーセル',
                    questionnaire: 'アンケート', //Add Survey
                    map: 'マップ',
                    introduction: '紹介', //Add Contact
                    save_as_draft: '下書きを保存',
                    create_from_draft: '下書きから作成', //Select draft
                    save_as_template: 'テンプレート保存',
                    delivery_registration: '配信登録', //Send / Schedule send
                    save_and_exit: '保存して終了',
                    add_message: "メッセージを追加", // New
                    edit_message: '編集', // Edit
                    att: '添付',

                    required_content_message: 'このフィールドは必須です。',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'type', 'loadingCount'],
    data() {
        return {
            content: {
                id: 0,
                title: '',
                content_type: 'message',
                content_message: '',
                url_actions: {},
                is_active: 1,
                is_edit:false,
                attachments: [],
                schedule_type: 0,
                schedule_date: null,
                schedule_time: null,
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
                            is_label_error: false,
                            data: '',
                            is_data_error: false,
                            auto_reply:'',
                            tag_add:[],
                            tag_delete:[],
                            select: true
                        }
                    ]
                }
            },
            /**
             * 編集前データ
             * @type {object}
             */
            preEditContent: {},
            temp: [],
            visible: false,
            /**
             * 登録、保存ボタンをクリックして画面を終了したかを判断する。
             * @type {boolean}
             */
            confirmed: false,
            currentComponent: 'contentpanel-createmessage',
            buttonclass: "btn mx-1 " + this.btnclass,
            message: {},
            RoundedRed: "rounded-red",
            buttons: [
                { index:0, name:'contentpanel-createmessage'},
                { index:1, name:'addcontent-main'},
                { index:2, name:'survey-questionnaire'}
            ]

        }
    },
    /**
     * インスタンス生成後処理
     * @returns {void}
     */
    created() {
        // メッセージのバリデーションを追加する。
        $.validator.addMethod(
            "requiredContentMessage",
            function (val, elem) {
                if (this.optional(elem) == true) {
                    return true
                }

                if (val != "") {
                    // 空文字でなければOKにする。
                    return true
                }
                if (!((elem.innerHTML == "<br>") || (elem.innerHTML == "<br/>"))) {
                    // 絵文字のみの場合、valが空文字と判定される場合があるため、
                    // elem.innerHTMLが空ではない、または改行タグのみではない場合はOKにする。
                    return true
                }

                return false
            },
            this.$t("message.required_content_message")
        )
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
    },
    methods: {
        /**
         * 画面終了時の処理。
         * @returns {void}
         */
        onAfterClose() {
            if (!this.confirmed) {
                this.restoreContent()
            }
        },
        /**
         * 配信登録処理。
         * @returns {void}
         */
        register() {
            let form = $("#messageForm")

            if (this.currentComponent == 'survey-questionnaire') {
                form.validate({
                    rules: {
                        title: "required",
                        question: "required",
                        message: "requiredContentMessage",
                        notice_msg: "required"
                    }
                })
            } else {
                form.validate({
                    rules: {
                        title: "required",
                        message: "requiredContentMessage"
                    }
                })
            }

            if (this.currentComponent === 'survey-questionnaire') {
                this.content.surveyQuestionnaire.actions.forEach(a => {
                    a.is_label_error = false
                    a.is_data_error = false
                });
            }

            if (!form.valid()) {
                return
            }

            if (this.currentComponent === 'survey-questionnaire') {
                let is_label_error = false;
                let is_data_error = false;
                for (let i = 0; this.content.surveyQuestionnaire.actions.length > i; i++) {
                    let action = this.content.surveyQuestionnaire.actions[i];
                    // ラベル入力チェック
                    if (action.label === undefined || action.label === "" || action.label === null) {
                        action.is_label_error = true;
                        is_label_error = true
                    }
                    // アクション入力チェック
                    // なし（postback）の場合、チェックしない
                    if (action.type !== 'postback') {
                        if (action.data === undefined || action.data === "" || action.data === null) {
                            action.is_data_error = true;
                            is_data_error = true
                        }
                    }
                }

                if (is_label_error || is_data_error) {
                    this.content.surveyQuestionnaire.actions.splice()
                    return
                }

            }

            if (this.type === 'New') {
                this.$emit('pushMessage', this.content)
            }

            if (this.type === 'Edit') {
                this.content.is_edit = false
            }

            this.confirmed = true
            this.visible = false
        },
        reset() {
            this.content = {
                id: 0,
                title: '',
                content_type: 'message',
                content_message: '',
                url_actions: {},
                is_active: 1,
                is_edit: false,
                attachments: [],
                schedule_type: 0,
                schedule_date: null,
                schedule_time: null,
                time_after: null,
                surveyQuestionnaire:
                {
                    text: '',
                    notification_message: '',
                    type_select_restriction: 'no_limit',
                    actions: [
                        {
                            action_no: '1',
                            type: 'postback',
                            behavior: 'none',
                            label: '',
                            data: '',
                            auto_reply: '',
                            tag_add: [],
                            tag_delete: [],
                            select: true
                        }
                    ]
                }
            }

            this.preEditContent = $.extend(true, {}, this.content)
        },
        saveAsDraft() {
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("stepmail/draft_messages", {
                message: self.content,
            })
            .then(function (response) {
                self.visible = false
            })
            .finally(() => self.$emit('input', self.loadingCount - 1))
        },
        saveTemplate() {

        },
        showModal() {
            this.confirmed = false

            if (this.data) {
                this.content = this.data
                // this.content = $.extend(true, {}, this.data)
                // load current contentpanel
                switch (this.content.content_type) {
                    case 'carousel':
                        this.currentComponent = 'contentpanel-createcarousel';
                        break;
                    case 'location':
                        this.currentComponent = 'contentpanel-addmap';
                        break;
                    case 'survey':
                        this.currentComponent = 'survey-questionnaire';
                        break;
                    default:
                        this.currentComponent = 'contentpanel-createmessage';
                }

                let schedule_type = this.data.schedule_type
                setTimeout(function () {
                    $('#typeBox' + schedule_type).focus()
                }, 500, schedule_type);
            } else {
                this.currentComponent = 'contentpanel-createmessage';
                this.reset()
            }

            this.preEditContent = $.extend(true, {}, this.content)
            this.visible = true

            if (this.$parent.$parent.$parent.$parent.$parent.$parent.$parent &&
                typeof this.$parent.$parent.$parent.$parent.$parent.$parent.$parent.changeTutorialState === 'function') {
                this.$parent.$parent.$parent.$parent.$parent.$parent.$parent.changeTutorialState(1)
            }
        },
        handleOk(e) {
            Object.assign(this.data, this.content);
            this.visible = false
        },
        switchComponent(componentIndex) {
            this.currentComponent = this.buttons[componentIndex].name
            if (this.currentComponent == 'contentpanel-createmessage') {
                this.content.content_type = 'message'
            } else if (this.currentComponent == 'survey-questionnaire') {
                this.content.content_type = 'survey'
            }
        },
        updateFromTemplate(template) {
            this.content.title = template.title
            this.content.content_message = template.content_message;
            this.content.attachments = template.attachment;
            this.content.content_type = template.content_type;
            // TODO: 将来、currentComponentを調べる必要があると思う
            this.$refs.aliveComponent.updateFromTemplate(this.content);
        },
        getContents(content) {
            this.content.content_message = content.content_message;
            this.content.attachments = content.attachments;
            this.content.content_type = content.content_type;
            this.content.url_actions = content.url_actions;
        },
        updateFromDraft(content) {
            console.log(content)
            let id = this.content.id
            Object.assign(this.content, content);
            this.content.id = id;

            switch (this.content.content_type) {
                case 'survey':
                    this.currentComponent = 'survey-questionnaire'
                    this.content.surveyQuestionnaire = content.surveyQuestionnaire
                    this.$refs.aliveComponent.updateFromDraft(this.content.surveyQuestionnaire)
                    break
                default:
                    this.currentComponent = 'contentpanel-createmessage'
                    // this.content.content_message = content.content_message
                    // this.content.attachments = content.attachments
                    this.$refs.aliveComponent.updateFromTemplate(this.content)
            }
        },
        /**
         * データを元に戻す。
         * @returns {void}
         */
        restoreContent() {
            Object.assign(this.content, this.preEditContent)
        },
    }
});
