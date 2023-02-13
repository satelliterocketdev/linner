Vue.component ('addcontent-video', {
    template: 
    `<div>
    <button type="button" @click="showModal" :disabled="disabled" class="btn m-1" style="border: 1px; border-style: solid; height: 140px; width: 140px;">
        <div class="justify-content-center mb-1">
            <div v-if="fileData !== null" style="position:relative">
                <div @click.stop="clearFile" style="position:absolute; right: 0;"><i class="fa fa-times"></i></div>
                <div>
                    <img alt="example" style="max-width: 100%; max-height: 80px" :src="fileData.featured_url" />
                </div>
            </div>
            <div v-else><i class="fa fa-5x fa-video"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_video')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.add_video')}}
        </div>
        <div class="row p-4">
            <a-upload-dragger class="col-sm-12" :showUploadList="false" :beforeUpload="() =>{return false}" @change="handleChange" accept=".video, .mp4, .mov">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_video')}}</p>
            </a-upload-dragger>
        </div>
        <div class="px-4 mx-auto w-50">
            <div class="row border rounded p-2 text-center" style="height: 100%; width: auto">
                <div v-if="fileData !== null" class="w-100">
                    <video id="preview-player" class="img-fluid" controls="true">
                        <source :src="fileData.url" type="video/mp4">
                    </video>
                </div>
                <div v-else>Preview</div>
            </div>  
        </div>
        <div class="row justify-content-center pt-4">
            <button class="btn rounded-green" @click="done">{{$t('message.done')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    done: 'Done',
                    send_video: 'Send a Video',
                    add_video: 'Add Video',
                    upload_video: 'Upload Video',
                    playback_option: 'Upload Image',
                    select_from_content: 'Select from content',
                    configuration: 'Configuration',
                } 
            },
            ja: { 
                message: { 
                    done: '完了',
                    send_video: '動画を送る',
                    add_video: '動画を追加',
                    upload_video: '動画をアップロード',
                    playback_option:  '再生オプション',
                    select_from_content: 'コンテンツから選択',
                    configuration: '設定',
                } 
            }
        }
    },
    props: {
        disabled: {
            type: Boolean,
        },
    },
    data() {
        return {
            visible: false,
            title: "Video",
            type: 'video',
            uploadUrl: '/upload/image',
            fileData: null,
            previously: [],
        }
    },
    methods: {
        done() {
            this.visible = false
        },
        selectedFromPreviously(item){
            this.fileData = item
            this.fileData['attachment_type'] = this.type
            this.$emit('selected', this.fileData)
        },
        showModal() {
            this.visible = true
        },
        handleChange(info) {
            let formData = new FormData()
            formData.append('file', info.file)
            axios.post(this.uploadUrl, formData)
            .then(res => {
                this.fileData = res.data
                this.fileData['attachment_type'] = this.type
                this.$emit('selected', this.fileData)
            })
        },
        clearFile() {
            if (this.fileData == null) {
                return
            }
            this.$emit('remove', this.fileData)
            this.fileData = null
        },
    }
});