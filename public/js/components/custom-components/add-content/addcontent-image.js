Vue.component ('addcontent-image', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn m-1" style="border: 1px; border-style: solid; height: 150px; width: 150px;">
        <div class="justify-content-center mb-1">
            <div v-if="file.length>0">
                <div class="float-right" @click="clearFile"><i class="fa fa-times"></i></div>
                <img alt="example" style="max-width: 100%; max-height: 80px" :src="file[0].featured_url" />
            </div>
            <div v-else><i class="far fa-5x fa-file-image"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_image')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.add_image')}}
        </div>
        <div class="row p-4">
            <a-upload-dragger class="col-sm-12" name="file" :showUploadList="false" :beforeUpload="() =>{return false}" @change="handleChange" accept=".jpeg, .jpg, .png">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_image')}}</p>
            </a-upload-dragger>
        </div>
        <div class="px-4 mx-auto w-50">
            <div class="border rounded p-2" style="height: 100%; width: auto">
                <div v-if="file.length>0" class="text-center">
                    <img class="img-fluid" :src="file[0].featured_url" />
                </div>
                <div v-else>Preview</div>
            </div>
        </div>
        <div class="row justify-content-center pt-4">
            <button class="btn rounded-green" @click="close">{{$t('message.done')}}</button>
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
    props: ['data', 'content', 'loadingCount'],
    model: {
        prop : 'loadingCount',
        event : 'input'
    },
    data() {
        return {
            visible: false,
            title: "Image",
            type: 'image',
            uploadUrl: baseUrl + '/upload/image',
            deleteUrl: baseUrl + '/upload/delete',
            file: [],
            tmp: [],
        }
    },
    beforeMount() {
        console.log(baseUrl)
    },
    methods: {
        close() {
            if (this.file.length > 0 && this.tmp != this.file[0]) {
                this.content.push(this.file[0])
                this.tmp = this.file[0]
            }
            this.visible = false
        },
        showModal() {
            this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
        handleChange(info) {
            if (this.file.length > 0) {
                let delFile = this.file.pop();
                this.$emit('input', this.loadingCount + 1)
                info.file.delete(
                    this.deleteUrl,
                    delFile.url,
                    res =>{
                        this.$emit('input', this.loadingCount + 1)
                        info.file.delete(
                            this.deleteUrl,
                            delFile.featured_url,
                            null,
                            null,
                            this.axiosFinallyCallback
                        );
                    },
                    null,
                    this.axiosFinallyCallback
                );
            }

            this.$emit('input', this.loadingCount + 1)
            info.file.upload(
                this.uploadUrl,
                (res) => this.file.push(res.data),
                null,
                this.axiosFinallyCallback
            );
        },
        handlePreview() {
            console.log('show preview')
        },
        axiosFinallyCallback() {
            this.$emit('input', this.loadingCount - 1)
        },
        clearFile() {
            if (this.file.length < 1) {
                return
            }

            let key = this.content.indexOf(this.file[0])
            this.content.splice(key, 1)

            this.$emit('input', this.loadingCount + 1)
            File.prototype.delete(
                this.deleteUrl,
                this.file[0].url,
                res => this.file.pop(),
                null,
                this.axiosFinallyCallback
            );
        },
    }
});