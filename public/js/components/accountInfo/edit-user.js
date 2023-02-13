Vue.component ('edit-user', {
    template:
    `<div>
    <button @click="showModal" class="btn mx-1 mb-1 btn-success font-size-table">{{$t('message.edit')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
        <form id="accountinfo_edit_user">
            <div>
                <div class="text-center" style="font-size: 24px">
                    <b>{{$t("message.title")}}</b>
                </div>
                <div>
                    <div>
                        <label>{{$t("message.name")}}</label>
                        <input id="name" name="name" class="form-control" type="text" v-model="user.name">
                    </div>
                    <div>
                        <label>{{$t("message.email")}}</label>
                        <input readonly id="email" name="email" class="form-control" type="text" v-model="user.email">
                    </div>
                    <div>
                        <label>{{$t("message.password")}}</label>
                        <input id="password" name="password" class="form-control" type="password" v-model="password">
                    </div>
                    <div>
                        <label>{{$t("message.password_confirmation")}}</label>
                        <input id="password_confirmation" name="password_confirmation" class="form-control" type="password" v-model="confirmPassword">
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
        
                <div class="footer">
                    <div class="row justify-content-center">
                        <button type="button" class="btn rounded-green m-1" @click.prevent="register">{{$t('message.save')}}</button>
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

                    edit: 'Edit',
                    save: 'Save',

                    validation_same: "Password and password confirm must match",
                    validation_alpha_numeric: "Please enter alphanumeric characters",
                }
            },
            ja: {
                message: {
                    title: "ユーザー編集",
                    name: "お名前",
                    mail: "メールアドレス",
                    password: 'パスワード',
                    password_confirmation: 'パスワード（確認）',
                    authentication: "権限",

                    friend: "友達",
                    friend_ope: "友達操作",
                    message_change: "メッセージ送信可　シナリオ変更可",
                    name_change: "名前変更可　対応マーク・表示変更可",
                    memo_change: "個別メモ変更可",
                    tag_change: "タグ変更可　友達情報変更可",
                    rich_menus_change: "リッチメニュー変更可",
                    action_app: "アクション適用可",
                    csv_export: "CSVエクスポート",
                    csv_import: "CSVインポート",
                    mail_invite: "メール送信",
                    talk_list: "トーク一覧",

                    message: "メッセージ",
                    scenario: "シナリオ配信",
                    send_email: "一斉配信",
                    automatic_ans: "自動応答",
                    template: "テンプレート",
                    answer_form: "回答フォーム",
                    reminder: "リマインダ",
                    setting: "友達追加時設定",
                    action_manage: "アクション管理",
                    error_message: "エラーメッセージ処理",

                    other: "その他",
                    manage_tag: "タグ管理",
                    conversion: "コンバージョン設定",
                    url_click: "URLクリック設定",
                    rich_menus: "リッチメニュー",

                    edit: '編集',
                    save: '登録する',

                    validation_same: "パスワードとパスワード（確認）は一致しません",
                    validation_alpha_numeric: "半角英数字を入力してください",
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['user', 'reloadUser', 'loadingCount'],
    data() {
        return {
            password: '',
            confirmPassword: '',
            visible: false,
            roles: [],
            friendValues: [],
            messageValues: [],
            otherValues:[],
            friendRoles: [
                {value: 1010010, label: this.$t('message.friend_ope')},
                {value: 1020010, label: this.$t("message.message_change")},
                {value: 1040010, label: this.$t("message.name_change")},
                {value: 1060010, label: this.$t("message.memo_change")},
                {value: 1070010, label: this.$t("message.tag_change")},
                {value: 1080010, label: this.$t("message.rich_menus_change")},
                {value: 1090010, label: this.$t("message.action_app")},
                // {value: 1100010, label: this.$t("message.csv_export")},
                // {value: 1110010, label: this.$t("message.csv_import")},
                {value: 1120010, label: this.$t("message.mail_invite")},
                {value: 1130010, label: this.$t("message.talk_list")},
            ],
            messageRoles: [
                {value: 2010010, label: this.$t("message.scenario")},
                {value: 2020010, label: this.$t("message.send_email")},
                {value: 2030010, label: this.$t("message.automatic_ans")},
                {value: 2040010, label: this.$t("message.template")},
                {value: 2050010, label: this.$t("message.answer_form")},
                {value: 2060010, label: this.$t("message.reminder")},
                {value: 2070010, label: this.$t("message.setting")},
                {value: 2080010, label: this.$t("message.action_manage")},
                // {value: 2090010, label: this.$t("message.error_message")},
            ],
            otherRoles: [
                {value: 3010010, label: this.$t("message.manage_tag")},
                {value: 3040010, label: this.$t("message.conversion")},
                {value: 3050010, label: this.$t("message.url_click")},
                {value: 3060010, label: this.$t("message.rich_menus")},
            ]
        }
    },
    methods: {
        showModal() {
            this.password = "";
            this.confirmPassword = "";
            this.visible = true;
            this.checkCheckeds();
        },
        handleOk(e) {
            this.visible = false
        },
        checkCheckeds() {
            var allRoles = []
            allRoles.push(...this.friendRoles)
            allRoles.push(...this.messageRoles)
            allRoles.push(...this.otherRoles)
        
            let rolesIds = []
            for (let i = 0; i < this.user.roles.length; i++) {
                if (this.user.roles[i] != null) {
                    rolesIds.push(this.user.roles[i].id)
                }
            }

            var result = allRoles.filter(function(role, index) {
                return rolesIds.includes(role.value)
            })

            for (let i = 0; i < result.length; i++) {
                switch(Number(String(result[i].value).charAt(0))) {
                    case 1:
                        this.friendValues.push(result[i].value)
                    break
                    case 2:
                        this.messageValues.push(result[i].value)
                    break
                    case 3:
                        this.otherValues.push(result[i].value)
                    default:
                    break
                }
            }
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

            //TODO::不要なら削除する inatomi
            $.validator.addMethod('same', function (val, elem) {
                if (this.optional(elem)) {
                    return true;
                }
                return $('#password_confirmation').val() === val;
            }, this.$t('message.validation_same'));

            // 半角英数字のバリデーションを追加する。
            $.validator.addMethod(
                "alphanum",
                function (val, elem) {
                    return this.optional(elem) || /^([a-zA-Z0-9]+)$/.test(val);
                },
                this.$t("message.validation_alpha_numeric")
            );

            const form = $("#accountinfo_edit_user")

            if (this.password !== '') {
                form.validate({
                    rules: {
                        name: "required",
                        email: "required",
                        password: {
                            minlength: 6,
                            "alphanum": true,
                        },
                        password_confirmation: {
                            minlength: 6,
                            equalTo: "#password"
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

            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put('accountinfo/' + this.user.id, {
                name: this.user.name,
                email: this.user.email,
                password: this.password,
                roles: this.friendValues.concat(this.messageValues).concat(this.otherValues),
            })
            .then((res) => {
                // リロード時にデータが残るのを回避するため、権限の配列をリセット
                this.friendValues.length  = 0
                this.messageValues.length = 0
                this.otherValues.length   = 0
                self.reloadUser()
                self.visible = false
            }).catch(error => {
                Object.keys(error.response.data).forEach(key => {
                    self.$message.error(error.response.data[key]);
                });
            })
            .finally(() => this.$emit('input', this.loadingCount - 1));
        },
    }
});
