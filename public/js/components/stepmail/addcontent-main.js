Vue.component ('addcontent-main', {
    template: 
    `<div>
        <button type="button" @click="showModal" class="btn my-1 btn-block btn-primary"><i class="fas fa-inbox mr-1"></i>{{$t('message.attachment')}}</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="550" :footer="null" :destroyOnClose="true">
            <div class="row justify-content-center align-items-center">
                <addcontent-image :data="data" :content="content"></addcontent-image>
                <addcontent-video :data="data" :content="content"></addcontent-video>
                <addcontent-audio :data="data" :content="content"></addcontent-audio>
            </div>
            <div class="row justify-content-center align-items-center">
                <!-- <addcontent-template :data="data" :content="content"></addcontent-template> -->
                <addcontent-other :data="data" :content="content"></addcontent-other>
                <addcontent-stamp :data="data" :content="content" :write="write"></addcontent-stamp>
            </div>
            <div class="footer pt-4">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-4 align-items-center">
                    </div>
                    <div class="col-sm-4 align-items-center">
                        <button class="btn rounded-green m-1" @click="close">{{$t('message.done')}}</button>
                    </div>
                    <div class="col-sm-4 text-right align-items-center">
                        <button @click="handleOk" class="btn m-1"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    attachment: 'Attachment',
                    done: 'Done',
                    send_image: 'Send an Image',
                    send_video: 'Send a Video',
                    send_voice: 'Send a Voice clip',
                    choose_a_template: 'Choose a template',
                    send_file: 'Send a file',
                    send_a_stamp: 'Send a Stamp'
                } 
            },
            ja: { 
                message: { 
                    attachment: '添付',
                    done: '完了',
                    send_image: '画像を送る',
                    send_video: '動画を送る',
                    send_voice: '音声を送る',
                    choose_a_template: 'テンプレートを選ぶ',
                    send_file: 'ファイルを送る',
                    send_a_stamp: 'スタンプを送る'
                } 
            }
        }
    },
    props: ['data'],
    data() {
        return {
            length: 0,
            maxlength: 2000,
            content: {
                content_type: 'message',
                attachment: this.data.attachment,
                // content_message: this.data.content_message,
            },
            tinymce,
            tmp: null,
            selected: {},
            visible: false
        }
    },
    watch: {
        "data.content_message"(value) {
            this.length = value.length
        },
    },
    methods: {
        write(e) {
            value = $(e.target).html()
            this.updateMessage(value)
        },
        writemessage(value) {
            msg = $("#message").html()
            updatedValue = msg + value
            $("#message").html(updatedValue)
            this.updateMessage(updatedValue)
        },
        updateMessage(value) {
            this.content.content_message = value
            this.length = value.length
            // $.extend(this.data, this.content)
            // this.data.content_message = this.content.content_message
            // this.m = this.content.content_message
            Object.assign(this.data, this.content)
        },
        removeAttachment(attachment) {
            key = this.data.attachment.indexOf(attachment)
            this.data.attachment.splice(key, 1)
        },
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        close() {
            this.visible = false
        }
    }
});