Vue.component ('addcontent-image', {
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
            <div v-else><i class="far fa-5x fa-file-image"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_image')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.add_image')}}
        </div>
        <div class="row p-4">
            <a-upload-dragger class="col-sm-12" :showUploadList="false" :beforeUpload="() =>{return false}" @change="handleChange" accept=".jpeg, .jpg, .png">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_image')}}</p>
            </a-upload-dragger>
        </div>

        <div class="px-4 mx-auto w-50">
            <div class="row border rounded p-2 text-center" style="height: 100%; width: auto">
                <div v-if="fileData !== null" class="w-100">
                    <img class="img-fluid" :src="fileData.featured_url" />
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
                    send_image: 'Send Image',
                    add_image: 'Add Image',
                    upload_image: 'Upload Image',
                    select_from_content: 'Select from content',
                    configuration: 'Configuration'
                } 
            },
            ja: { 
                message: { 
                    done: '完了',
                    send_image: '画像を送る',
                    add_image: '画像を追加',
                    upload_image: ' 画像をアップロード',
                    select_from_content: 'コンテンツから選択',
                    configuration: '設定'
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
            title: "Image",
            type: 'image',
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