Vue.component('new-scenario', {
    template:
    `<div>
        <button id="tutorialBtn1" v-if="type == 'New'" @click="showModal" v-bind:class="buttonclass">{{$t('message.new')}}</button>
        <button v-if="type == 'Edit'" @click="showModal" v-bind:class="buttonclass" class="font-size-table mb-1">{{$t('message.edit')}}</button>

        <a-modal :centered="true" v-model="visible" @ok="handleOk" width="100%" style="max-width: 900px;" :footer="null" :destroyOnClose="true">
            <form id="scenarioForm" method="post" v-on:submit.prevent>
                <div>
                    {{$t("message.scenario_distribution_setting")}}
                    <b><input name="title" class="borderless-input form-control" type="text" :placeholder="$t('message.title')" style="font-size: 24px" v-model="name"></b>
                </div>
                <div class="row align-items-center">
                    <div class="col align-items-center">
                        {{$t('message.delivery_completion')}}
                    </div>
                    <div class="col align-items-center">
                        <span v-for="(targetServe, key) in targetServes">
                            <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ targetServe }} {{ key+1 }}
                        </span>
                        <span v-if="date">&nbsp; {{ date }} &nbsp;</span>
                        <!-- <span v-if="sendToAll">&nbsp;{{$t('message.send_to_all')}}</span> -->
                    </div>
                    <div class="col">
                        <div class="row justify-content-end align-items-center">
                            <message-target :data="target" :reset="clearTarget" :update-data="updateTarget" :restore="restoreTarget" :setText="setTargetListText"> </message-target>
                            <!-- <button type="button" class="btn rounded-white m-1" @click="sendToAll = !sendToAll">{{$t('message.send_to_all')}}</button> -->
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col align-items-center">
                        {{$t('message.delivery_completion_action')}}
                    </div>
                    <div class="col align-items-center">
                        <span v-for="(actionServe, key) in actionServes">
                            <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ actionServe }} {{ key+1 }}
                        </span>
                    </div>
                    <div class="col align-items-center">
                        <div class="row justify-content-end">
                            <message-action :data="action" :reset="clearAction" :update-data="updateAction" :restore="restoreAction" :setText="setActionListText"></message-action>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row align-items-center">
                    <div class="col-sm-6 align-items-center">
                        {{$t('message.delivery_statement')}}
                    </div>
                    <div class="col-sm-6 align-items-center text-right">
                        <div class="row justify-content-end">
                            <new-message :message="message" v-bind:btnclass="RoundedWhite" @pushMessage="pushMessage" :type="message_type" v-model:loading-count="loadingCountData"></new-message>
                            <confirmation-delete class="mr-1 ml-1" :selected="message_selected" :data="message" :id="id" :reload-message="reloadMessage"></confirmation-delete>
                        </div>
                    </div>
                </div>

                <div class="fixed-container py-2 my-1">
                    <scenario-messages-list :data="message" :selected="message_selected" event="1" v-model:loading-count="loadingCountData"></scenario-messages-list>
                </div>

                <div class="footer">
                    <div class="row justify-content-center">
                        <button type="button" class="btn rounded-green m-1" @click="register">{{$t('message.register')}}</button>
                    </div>
                </div>
            </form>
        </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    hello: 'hello component1',
                    scenario_distribution_setting: "Scenario Distribution Setting",
                    title: "Title",
                    register: "Register",
                    delivery_completion: 'Delivery Completion',
                    delivery_completion_action: 'Delivery Completion Action',
                    delivery_statement: 'Delivery Statement',
                    send_to_all: 'Send to All',
                    new_message: 'New Message',
                    delete_selected: 'Delete Selected',
                    new: 'New',
                    edit: 'Edit',
                    active: 'Active',
                    inactive: 'Inactive'
                }
            },
            ja: {
                message: {
                    hello: 'こんにちは、component1',
                    scenario_distribution_setting: 'シナリオ配信設定',
                    title: "タイトル",
                    register: "登録",
                    delivery_completion: '配信対象',
                    delivery_completion_action: '配信完了時アクション',
                    delivery_statement: '配信文',
                    send_to_all: '全員',
                    new_message: 'メッセージ追加',
                    delete_selected: '選択したものを削除',
                    new: '新規',
                    edit: '編集',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'reloadScenario', 'type', 'loadingCount'],
    data() {
        return {
            sendToAll: false,
            id: 0,
            name: '',
            /**
             * 配信対象
             * @type {object}
             */
            target: {},
            /**
             * 配信対象(編集前データ)
             * @type {object}
             */
            preEditTarget: {},
            /**
             * 配信完了時アクション
             * @type {object}
             */
            action: {},
            /**
             * 配信完了時アクション(編集前データ)
             * @type {object}
             */
            preEditAction: {},
            message: [],
            message_type: 'New',
            message_selected: [],
            visible: false,
            buttonclass: "btn mx-1 " + this.btnclass,
            RoundedGreen: "rounded-green",
            RoundedWhite: "rounded-white",
            targetServes: [],
            actionServes: [],
            messageServes: [],
            date: ''
        }
    },
    methods: {
        /**
         * 配信対象を更新する。
         * 編集前オブジェクトに編集結果をコピーする。
         * @returns {void}
         */
        updateTarget() {
            this.preEditTarget = $.extend(true, {}, this.target)
            this.setTargetListText()
        },
        /**
         * 配信対象の編集情報を、編集前データを使用して起動時に戻す。
         * @returns {void}
         */
        restoreTarget() {
            this.target = $.extend(true, {}, this.preEditTarget)
        },
        /**
         * 配信対象一覧の文字列を設定する。
         * @returns {void}
         */
        setTargetListText() {
            // this will update the tags
            let tmp = this.name
            this.name = ' '
            this.name = tmp
            let scenario = this
            scenario.targetServes = []
            $.each(this.target, function (section, temp) {
                $.each(temp.serves, function (key, serve) {

                    serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function (key, serve_child) {
                            scenario.targetServes.push(serve_child)
                        })
                    } else {
                        if (section != 'dates') {
                            scenario.targetServes.push(serve_value)
                        } else {
                            if (typeof serve_value.from !== 'undefined' || typeof serve_value.to !== 'undefined') {
                                scenario.date = serve_value.from.format('YYYY-MM-DD') + ':' + serve_value.to.format('YYYY-MM-DD');
                            }
                        }
                    }

                })
            })
        },
        /**
         * 配信完了時アクションを更新する。
         * 編集前オブジェクトに編集結果をコピーする。
         * @returns {void}
         */
        updateAction() {
            this.preEditAction = $.extend(true, {}, this.action)
            this.setActionListText()
        },
        /**
         * 配信完了時アクションの編集情報を、編集前データを使用して起動時に戻す。
         * @returns {void}
         */
        restoreAction() {
            this.action = $.extend(true, {}, this.preEditAction)
        },
        /**
         * 配信完了時アクション一覧の文字列を設定する。
         * @returns {void}
         */
        setActionListText() {
            // this will update the tags
            let tmp = this.name
            this.name = ' '
            this.name = tmp
            let scenario = this
            scenario.actionServes = []
            $.each(this.action, function (section, temp) {
                $.each(temp.serves, function (key, serve) {

                    let serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function (key, serve_child) {
                            scenario.actionServes.push(serve_child)
                        })
                    } else {
                        if (section != 'dates') {
                            scenario.actionServes.push(serve_value)
                        }
                    }

                })
            })
        },
        pushMessage(defaults) {
            this.message.push(defaults)
        },
        clearTarget() {
            this.target = {}
        },
        clearAction() {
            this.action = {}
        },
        reset() {
            this.id = 0
            this.name = ''
            this.target = {}
            this.preEditTarget = {}
            this.action = {}
            this.preEditAction = {}
            this.message = []
            this.message_selected = []
            this.targetServes = []
            this.actionServes = []
        },
        showModal() {
            this.reset()
            this.visible = true
            if (this.data) {
                this.id = this.data.id
                this.name = this.data.name
                if (this.data.target.dates.serves.length > 0) {
                    this.data.target.dates.serves.forEach(serve => {
                        if (serve.value.hasOwnProperty('from')) {
                            serve.value.from = moment(new Date(serve.value.from))
                            serve.value.to = moment(new Date(serve.value.to))
                        }
                    })

                    this.data.target.dates.excludes.forEach(exclude => {
                        if (exclude.value.hasOwnProperty('from')) {
                            exclude.value.from = moment(new Date(exclude.value.from))
                            exclude.value.to = moment(new Date(exclude.value.to))
                        }
                    })
                }
                this.target = this.data.target
                this.preEditTarget = $.extend(true, {}, this.target)
                this.action = this.data.action
                this.preEditAction = $.extend(true, {}, this.action)
                this.message = this.data.messages // JSON.parse(this.data.message)
                this.message_selected = []
                this.setTargetListText()
                this.setActionListText()
            }
        },
        handleOk(e) {
            this.visible = false
        },
        register() {
            let form = $("#scenarioForm")

            form.validate({
                rules: {
                    title: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            if (this.$parent && typeof this.$parent.changeTutorialState === 'function') {
                this.$parent.changeTutorialState(2)
            }

            if (this.id) {
                this.update()
                return
            }
            let self = this
            const message = JSON.parse(JSON.stringify(self.message));
            message.forEach(m => {
                m.schedule_date = m.schedule_date + " " + m.schedule_time;
            });

            this.$emit('input', this.loadingCount + 1);
            axios.post("stepmail", {
                name: self.name,
                target: JSON.stringify(self.target),
                action: JSON.stringify(self.action),
                message: JSON.stringify(message),
                is_active: 1
                // TODO
                // send_to_all: this.sendToAll,
            })
                .then(function (response) {
                    self.reloadScenario()
                    self.reset()
                    self.visible = false
                })
                .finally(() => self.$emit('input', self.loadingCount - 1))
        },
        update() {
            let self = this;
            const message = JSON.parse(JSON.stringify(self.message));
            message.forEach(m => {
                    m.schedule_date = m.schedule_date + " " + m.schedule_time;
                });

            this.$emit('input', this.loadingCount + 1)
            axios.put("stepmail/" + this.id, {
                name: self.name,
                target: JSON.stringify(self.target),
                action: JSON.stringify(self.action),
                message: JSON.stringify(message),
                is_active: 1
                // TODO
                // send_to_all: this.sendToAll,
            })
                .then(function (response) {
                    self.reloadScenario()
                    self.reset()
                    self.visible = false
                })
                .finally(() => self.$emit('input', self.loadingCount - 1))
        },
        reloadMessage() {
            let self = this
            self.message = []
            this.$emit('input', self.loadingCount + 1)
            axios.get("stepmail/" + self.id)
                .then(function (response) {
                    self.message = response.data.messages
                })
                .finally(() => self.$emit('input', self.loadingCount - 1))
        },
        addMessage(event) {
            alert(event)
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
