Vue.component ('scenario-preview', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn btn-secondary mx-1 mb-1 small-text font-size-table px-small">{{$t('message.preview')}}</button>
    <a-modal :closable="false" :centered="true" v-model="visible" :width="350" :footer="null">
        <div class="d-flex bg-success rounded mb-4 mx-1 p-4" style="font-size: 12px">
            <div v-if="message">
                <div v-html="message.content_message"></div>
            </div>
        </div>
        <h6>URL Click rate</h6>
        <hr> 
        <p> Enter URL here </p>
        <div class="footer pt-4">
            <div class="row justify-content-center align-items-center">
                <div class="col-sm align-items-center"></div>
                <div class="col-sm align-items-center">
                    <button class="btn rounded-green m-1" @click="close">Finish/Confirm</button>
                </div>
                <div class="col-sm text-right align-items-center">
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
                    preview: 'Preview',
                }
            },
            ja: {
                message: {
                    preview: 'プレビュー',
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            message: this.data,
            visible: false,
        }
    },
    methods: {
        close() {
            this.visible = false
        },
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            console.log(e);
            this.visible = false
        },
    }
});