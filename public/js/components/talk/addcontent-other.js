Vue.component ('addcontent-other', {
    template: 
    `<div>
    <button type="button" @click="showModal" :disabled="disabled" class="btn m-1" style="border: 1px; border-style: solid; height: 140px; width: 140px;">
        <div class="row justify-content-center mb-1">
            <div v-if="fileData !== null" style="position:relative">
                <div @click.stop="clearFile" style="position:absolute; right: 0;"><i class="fa fa-times"></i></div>
                <div>
                    <img alt="example" style="max-width: 100%; max-height: 80px" :src="fileData.featured_url" />
                </div>
            </div>
            <div v-else><i class="fa fa-5x fa-paperclip"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_file')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
           {{$t('message.add_file')}}
        </div>
        <div class="row p-4">
            <a-upload-dragger class="col-sm-12" :showUploadList="false" :beforeUpload="() =>{return false}" @change="handleChange">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_file')}}</p>
            </a-upload-dragger>
        </div>

        <div class="px-4 mx-auto w-50">
            <div class="row border rounded p-2 text-center" style="height: 100%; width: auto">
                <div v-if="fileData !== null" class="w-100">
                    <img class="d-block m-auto" :src="fileData.featured_url" />
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
                    send_file: 'Send a file',
                    add_file: 'Add file',
                    upload_file: 'Upload File',
                    select_from_content: 'Select from content',
                    configuration: 'Configuration',
                } 
            },
            ja: { 
                message: { 
                    done: '??????',
                    send_file: '?????????????????????',
                    add_file: '?????????????????????',
                    upload_file: '?????????????????????????????????',
                    select_from_content: '???????????????????????????',
                    configuration: '??????',
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
            title: "Other",
            type: 'other',
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