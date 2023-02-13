Vue.component ('invitation-email', {
  template: 
  `<div class="inlineblock">
    <button id="tutorialBtn1" v-if="type == 'New'" type="button" @click="showModal" v-bind:class="btnclass" class="mb-1">{{$t('message.create_invitation')}}</button>
    <button v-if="type == 'Edit'" type="button" @click="showModal" v-bind:class="btnclass" class="font-size-table mb-1">{{$t('message.edit')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
            <form class="mt-4" id="invitationForm" type="post" v-on:submit.prevent>
                <div class="form-group">
                    <a-select mode="tags" style="width: 100%" v-model="defaults.destination" :tokenSeparators="[',']" :placeholder="$t('message.emails')">
                        <a-select-option v-for="(email, key) in defaults.destination" :key="email">
                            {{ email }}
                        </a-select-option>
                    </a-select>
                </div>
                <div class="form-group">
                    <input class="form-control" name="title" type="text" :placeholder="$t('message.title')" v-model="defaults.title">
                </div>
                <div class="form-group">
                    <textarea class="form-control" name="message" :placeholder="$t('message.content')" v-model="defaults.content_message" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" @click="register">{{$t('message.register')}}</button>
            </form>
        </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: {
                    title: 'Title',
                    content: 'Content',
                    edit: 'Edit',
                    register: 'Register',
                    create_invitation: 'Create invitation text',
                    emails: 'Emails'
                }
            },
            ja: {
                message: {
                    title: 'タイトル',
                    content: '内容',
                    edit: '編集',
                    register: '登録',
                    create_invitation: '招待文を作成',
                    emails: '送信先'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'type', 'reloadInvitations', 'loadingCount'],
    data() {
        return {
            defaults: {
                id: 0,
                title: '',
                content_message: '',
                destination: []
            },
            visible: false
        }
    },
    methods: {
        register() {
            form = $("#invitationForm")
            
            form.validate({
                rules: {
                    title: "required",
                    message: "required"
                }
            })
            
            if (!form.valid()) {
                return
            }

            if (!this.emailInput(this.defaults.destination)){
                window.alert('Invalid Email Inserted');
                return
            }

            if (this.defaults.id) {
                this.update()
                return
            }

            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("invitation", {
                title: this.defaults.title,
                content_message: this.defaults.content_message,
                destination: JSON.stringify(this.defaults.destination)
            })
            .then(function(response){
                self.handleOk()
                self.reset()
                self.reloadInvitations()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        update() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("invitation/" + this.defaults.id, {
                title: this.defaults.title,
                content_message: this.defaults.content_message,
                destination: JSON.stringify(this.defaults.destination)
            })
            .then(function(response){
                self.handleOk()
                self.reset()
                self.reloadInvitations()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
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
        reset() {
            this.defaults = {
                id: 0,
                title: '',
                content_message: '',
                destination: []
            }
        },
        emailInput(emails) {
            var re = /\S+@\S+\.\S+/;
            for (let i = 0; i < emails.length; i++) {
                if (re.test(emails[i]) == false) {
                    return false
                }
            }
            return true
        }
    }
});