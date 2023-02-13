Vue.component ('addcontent-main', {
    template: 
    `<div>
        <button type="button" @click="showModal" class="btn btn-sm btn-outline-info m-2" style="min-width: 120px;"><i class="fas fa-inbox mr-1"></i>{{$t('message.attachment')}}</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="550" :footer="null" :destroyOnClose="true">
            <div class="row justify-content-center align-items-center">
                <addcontent-image :content="data.attachments" v-model:loading-count="loadingCountData"></addcontent-image>
                <addcontent-video :content="data.attachments" v-model:loading-count="loadingCountData"></addcontent-video>
                <addcontent-audio :content="data.attachments" v-model:loading-count="loadingCountData"></addcontent-audio>
            </div>
            <div class="row justify-content-center align-items-center">
                <!-- <addcontent-template :content="data.attachments"></addcontent-template> -->
                <addcontent-other :content="data.attachments" v-model:loading-count="loadingCountData"></addcontent-other>
                <addcontent-stamp :content="data.attachments" v-model:loading-count="loadingCountData"></addcontent-stamp>
            </div>
            <div class="footer pt-4">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12 align-items-center text-center">
                        <button class="btn rounded-green m-1" @click="close">{{$t('message.done')}}</button>
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
    props: ['data', 'loadingCount'],
    model: {
        prop : 'loadingCount',
        event : 'input'
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
    data() {
        return {
            length: 0,
            maxlength: 2000,
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
    mounted() {
    },
    methods: {
        close() {
            this.visible = false
        },
        showModal() {
            this.visible = true
        }
    }
});