Vue.component('new-template', {
    template:
    `<div>
        <button v-if="type == 'New'" type="button" @click="showModal" v-bind:class="buttonclass" class="link-pointer">{{$t('message.add_template')}}</button>
        <button v-if="type == 'Edit'" type="button" @click="showModal" v-bind:class="buttonclass" style="font-size:12px" class="font-size-table link-pointer">{{$t('message.edit_template')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
            <form id="templateForm" type="post" v-on:submit.prevent>
                {{$t('message.delivery_statement_setting')}}
                <b><h5><input class="borderless-input form-control" name="title" type="text" :placeholder="$t('message.title')" v-model="defaults.title"></h5></b>
                <div class="row justify-content-center p-1">
                    <button v-bind:class="{ 'rounded-white': currentComponent !== buttons[0].name, 'rounded-blue': currentComponent === buttons[0].name }" type="button" v-on:click="switchComponent(0)" class="btn rounded-blue m-1">{{$t('message.normal_delivery_statement')}}</button>
                </div>
                <div id="content-panel" class="p-1">
                    <component v-bind:is="currentComponent" :data="defaults" :message="message" :type="type" @updateTextInput="getContents" urlclick-action="false"></component>
                </div>
                <div class="footer pt-1">
                    <div class="row align-items-end">
                        <div class="col-sm">
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn rounded-green mx-1" v-on:click="register">{{$t('message.register')}}</button>
                            </div>
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
                    delivery_statement_setting: 'Template',
                    normal_delivery_statement: 'Create Message',
                    add_template: 'Add', // New
                    edit_template: 'Edit', // Edit
                    att: 'Attachment',
                    register: 'Register',
                    title: 'Title',

                    required_content_message: 'This field is required.',
                }
            },
            ja: {
                message: {
                    delivery_statement_setting: '??????????????????????????????',
                    normal_delivery_statement: '???????????????', //Create Message
                    add_template: "??????", // New
                    edit_template: '??????', // Edit
                    att: '??????',
                    register: '??????',
                    title: '????????????',

                    required_content_message: '???????????????????????????????????????',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'type', 'reloadTemplate', 'loadingCount'],
    data() {
        return {
            defaults: {
                id: 0,
                title: '',
                content_message: '',
                is_active: 1,
                attachment: []
            },
            temp: [],
            visible: false,
            currentComponent: 'contentpanel-createmessage',
            buttonclass: "btn mx-1 " + this.btnclass,
            message: {},
            RoundedRed: "rounded-red",
            buttons: [
                { index: 0, name: 'contentpanel-createmessage' },
                { index: 1, name: 'addcontent-main' }
            ]

        }
    },
    /**
     * ?????????????????????????????????
     * @returns {void}
     */
    created() {
        // ?????????????????????????????????????????????????????????
        $.validator.addMethod(
            "requiredContentMessage",
            function (val, elem) {
                if (this.optional(elem) == true) {
                    return true
                }

                if (val != "") {
                    // ????????????????????????OK????????????
                    return true
                }
                if (!((elem.innerHTML == "<br>") || (elem.innerHTML == "<br/>"))) {
                    // ???????????????????????????val??????????????????????????????????????????????????????
                    // elem.innerHTML?????????????????????????????????????????????????????????????????????OK????????????
                    return true
                }

                return false
            },
            this.$t("message.required_content_message")
        )
    },
    methods: {
        close() {
            this.visible = false
        },
        register() {
            let form = $("#templateForm")

            form.validate({
                rules: {
                    title: "required",
                    message: "requiredContentMessage"
                }
            })

            if (!form.valid()) {
                return
            }

            if (this.defaults.id) {
                this.update()
                return
            }

            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("template", {
                title: self.defaults.title,
                content_message: self.defaults.content_message,
                attachment: self.defaults.attachment,
                is_active: self.defaults.is_active,
                is_draft: 0
            })
                .then(function (response) {
                    self.reloadTemplate()
                    self.reset()
                    self.visible = false
                })
                .finally(() => self.$emit('input', self.loadingCount - 1))
        },
        update() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("template/" + this.defaults.id, {
                title: this.defaults.title,
                content_message: this.defaults.content_message,
                attachment: this.defaults.attachment,
                is_active: this.defaults.is_active,
                is_draft: 0
            })
                .then(function (response) {
                    self.reloadTemplate()
                    self.reset()
                    self.visible = false
                })
                .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        getContents(content) {
            this.defaults.content_message = content.content_message
            this.defaults.attachment = content.attachments
        },
        showModal() {
            if (this.data) {
                this.defaults = this.data
            }
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        switchComponent(componentIndex) {
            this.currentComponent = this.buttons[componentIndex].name
        },
        reset() {
            this.defaults = {
                id: 0,
                title: '',
                content_message: '',
                is_active: 1,
                attachment: []
            }
        }
    }
});
