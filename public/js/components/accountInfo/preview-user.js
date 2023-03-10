Vue.component ('preview-user', {
    template:
    `<div>
    <button @click="showModal" class="btn mx-1 btn-success small-text">{{$t('message.preview')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
        <form id="accountinfo_edit_user">
            <div>
                <div class="text-center" style="font-size: 24px">
                    <b>{{$t("message.title")}}</b>
                </div>
                <div>
                    <div>
                        <label>{{$t("message.name")}}</label>
                        <input id="name" name="name" class="form-control" type="text" v-model="name">
                    </div>
                    <div>
                        <label>{{$t("message.email")}}</label>
                        <input readonly id="email" name="email" class="form-control" type="text" v-model="email">
                    </div>
                    <div>
                        <label>{{$t("message.password")}}</label>
                        <input id="password" name="password" class="form-control" type="password" v-model="password">
                    </div>
                    <div>
                        <label>{{$t("message.password_confirmation")}}</label>
                        <input id="password_confirmation" name="password_confirmation" class="form-control" type="password" v-model="password_confirmation">
                    </div>
                </div>
                <div class="my-3 px-0 px-md-5">
                    <div class="row">
                        <div class="col">
                            <h5>{{$t("message.authentication")}}</h5>
                        </div>
                    </div>
                    <div class="row border rounded-top">
                        <div class="col-4 py-3 border-right text-center">
                            <a-checkbox @change="onFriend">{{$t("message.friend")}}</a-checkbox>
                        </div>
                        <div class="col-8 py-3">
                            <a-checkbox-group :options="friendRoles" v-model="friendValues"></a-checkbox-group>
                        </div>
                    </div>
                    <div class="row border border-top-0">
                        <div class="col-4 py-3 border-right text-center">
                            <a-checkbox @change="onMessage">{{$t("message.message")}}</a-checkbox>
                        </div>
                        <div class="col-8 py-3">
                            <a-checkbox-group :options="messageRoles" v-model="messageValues"></a-checkbox-group>
                        </div>
                    </div>
                    <div class="row border border-top-0 rounded-bottom">
                        <div class="col-4 py-3 border-right text-center">
                            <a-checkbox @change="onOther">{{$t("message.other")}}</a-checkbox>
                        </div>
                        <div class="col-8 py-3">
                            <a-checkbox-group :options="otherRoles" v-model="otherValues"></a-checkbox-group>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </a-modal>
    </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    title: "Add New User",
                    name: "Name",
                    mail: "Email",
                    admin: "Admin",
                    password: 'Password',
                    password_confirmation: 'Password Confirmation',
                    authentication: "Authentication",

                    friend: "Friend",
                    friend_ope: "Friend Operation",
                    message_change: "Possible Send Message $ Possible Change users",
                    name_change: "Possible Change name & Possible Change Display Of Supported Mark",
                    memo_change: "Possible Change Individual Memo",
                    tag_change: "Possible Change Tags & Possible Change Friend's Information",
                    rich_menus_change: "Possible Change Rich Menus",
                    action_app: "Applicable Action",
                    csv_export: "Export CSV",
                    csv_import: "Import CSV",

                    message: "Message",
                    scenario: "Scenario",
                    send_email: "Send Bulk Email",
                    automatic_ans: "Automatic Answering",
                    template: "Template",
                    answer_form: "Answer Form",
                    reminder: "Reminder",
                    setting: "Setting Add Friends",
                    action_manage: "Action Management",
                    error_message: "Process Error Message",

                    other: "Other",
                    manage_tag: "Management Tag",
                    conversion: "Conversion Settings",
                    url_click: "Setting Click URL",
                    rich_menus: "Rich Menus",

                    preview: 'Preview',
                }
            },
            ja: {
                message: {
                    title: "??????????????????",
                    name: "?????????",
                    mail: "?????????????????????",
                    password: '???????????????',
                    password_confirmation: '???????????????????????????',
                    authentication: "??????",

                    friend: "??????",
                    friend_ope: "????????????",
                    message_change: "????????????????????????????????????????????????",
                    name_change: "???????????????????????????????????????????????????",
                    memo_change: "?????????????????????",
                    tag_change: "???????????????????????????????????????",
                    rich_menus_change: "??????????????????????????????",
                    action_app: "????????????????????????",
                    csv_export: "CSV??????????????????",
                    csv_import: "CSV???????????????",

                    message: "???????????????",
                    scenario: "??????????????????",
                    send_email: "????????????",
                    automatic_ans: "????????????",
                    template: "??????????????????",
                    answer_form: "??????????????????",
                    reminder: "???????????????",
                    setting: "?????????????????????",
                    action_manage: "?????????????????????",
                    error_message: "??????????????????????????????",

                    other: "?????????",
                    manage_tag: "????????????",
                    conversion: "???????????????????????????",
                    url_click: "URL??????????????????",
                    rich_menus: "?????????????????????",

                    preview: '??????',
                }
            }
        }
    },
    props: ['userId', 'reloadUser'],
    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            visible: false,
            roles: [],
            friendValues: [],
            messageValues: [],
            otherValues:[],
            // TODO: ?????????????????????????????????????????????
            friendRoles: [
                {value: 1010060, label: this.$t('message.friend_ope')},
                {value: 1010000, label: this.$t("message.message_change")},
                {value: 1010020, label: this.$t("message.name_change")},
                {value: 1010040, label: this.$t("message.memo_change")},
                {value: 1010050, label: this.$t("message.tag_change")},
                {value: 1010070, label: this.$t("message.rich_menus_change")},
                {value: 1010080, label: this.$t("message.action_app")},
                {value: 1020010, label: this.$t("message.csv_export")},
                {value: 1020020, label: this.$t("message.csv_import")},
            ],
            // TODO: ?????????????????????????????????????????????
            messageRoles: [
                {value: 2010010, label: this.$t("message.scenario")},
                {value: 2010020, label: this.$t("message.send_email")},
                {value: 2030010, label: this.$t("message.automatic_ans")},
                {value: 2040010, label: this.$t("message.template")},
                {value: 2050010, label: this.$t("message.answer_form")},
                {value: 2060010, label: this.$t("message.reminder")},
                {value: 2070010, label: this.$t("message.setting")},
                {value: 2080010, label: this.$t("message.action_manage")},
                {value: 2090010, label: this.$t("message.error_message")},
            ],
            // TODO: ?????????????????????????????????????????????
            otherRoles: [
                {value: 3010010, label: this.$t("message.manage_tag")},
                {value: 4020010, label: this.$t("message.conversion")},
                {value: 4010010, label: this.$t("message.url_click")},
                {value: 3040010, label: this.$t("message.rich_menus")},
            ]
        }
    },
    methods: {
        render() {
            axios.get('/accountinfo/' + this.userId)
            .then(res => {
                let user = res.data['user'];
                this.id = user.id
                this.name = user.name;
                this.email = user.email;
                this.password = '';
                this.password_confirmation = '';
                this.friendValues = res.data['friendValues'];
                this.messageValues = res.data['messageValues'];
                this.otherValues = res.data['otherValues'];
            });
        },
        showModal() {
            this.render();
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        onFriend(e) {
            let checked = e.target.checked;
            if (checked) {
                this.friendValues = this.friendRoles.map(r => r.value);
            } else {
                this.friendValues = [];
            }
        },
        onMessage(e) {
            let checked = e.target.checked;
            if (checked) {
                this.messageValues = this.messageRoles.map(r => r.value);
            } else {
                this.messageValues = [];
            }
        },
        onOther(e) {
            let checked = e.target.checked;
            if (checked) {
                this.otherValues = this.otherRoles.map(r => r.value);
            } else {
                this.otherValues = [];
            }
        },
        register() {
            $.validator.addMethod('same', function (val, elem) {
                if (this.optional(elem)) {
                    return true;
                }
                return $('#password_confirmation').val() === val;
            }, this.$t('message.validation_same'));

            const form = $("#accountinfo_edit_user")

            if (this.password !== '') {
                form.validate({
                    rules: {
                        name: "required",
                        email: "required",
                        password: {
                                same: true,
                                min: 6
                        }
                    }
                });
            } else {
                form.validate({
                    rules: {
                        name: "required",
                        email: "required",
                        password: 'same'
                    }
                });
            }


            if (!form.valid()) {
                return
            }

            axios.put('accountinfo/' + this.userId, {
                name: this.name,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
                roles: this.friendValues.concat(this.messageValues).concat(this.otherValues),
            })
            .then((res) => {
                this.reloadUser()
                this.visible = false
                console.log(res);
            }).catch(error => {
                Object.keys(error.response.data).forEach(key => {
                    this.$message.error(error.response.data[key]);
                });
            });
        },
    }
});
