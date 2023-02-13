Vue.component ('template-preview', {
    template:
`<div>
    <button type="button" @click="showModal" style="font-size:12px" class="btn btn-secondary mx-1 font-size-table link-pointer">{{$t('message.preview')}}</button>
    <a-modal :closable="false" :centered="true" v-model="visible" :width="350" :footer="null">
        <div class="line__container" style="font-size: 12px">
            <div class="line__contents">
                <div v-if="message" class="line__right">
                    <div v-html="message.content_message" class="text"></div>
                </div>
            </div>
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-center align-items-center">
                <div class="col-sm align-items-center text-center">
                    <button class="btn rounded-green m-1" @click="close">{{ $t('message.close') }}</button>
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
                    close: 'Close'
                }
            },
            ja: {
                message: {
                    preview: 'プレビュー',
                    close: '閉じる'
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