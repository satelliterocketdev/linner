Vue.component ('addcontent-video', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn m-1" style="border: 1px; border-style: solid; height: 150px; width: 150px;">
        <div class="justify-content-center mb-1">
            <div v-if="file.length>0">
                <div class="float-right" @click="clearFile"><i class="fa fa-times"></i></div>
                <img alt="example" style="max-width: 100%; max-height: 80px" :src="file[0].featured_url" />
            </div>
            <div v-else><i class="fa fa-5x fa-video"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_video')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.add_video')}}
        </div>
        <div class="row p-4">
        <a-upload-dragger class="col-sm-12" name="file" :showUploadList="false" :beforeUpload="() =>{return false}" @preview="handlePreview" @change="handleChange" accept=".video, .mp4, .mov">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_video')}}</p>
            </a-upload-dragger>
        </div>
        <div class="px-4 mx-auto w-50">
            <div class="row border rounded p-2 text-center" style="height: 100%; width: auto">
                <div v-if="file.length>0" class="w-100">
                    <video id="preview-player" class="img-fluid" controls="true">
                        <source :src="file[0].url" type="video/mp4">
                    </video>
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
                    send_video: 'Send a Video',
                    add_video: 'Add Video',
                    playback_option: 'Upload Image',
                    select_from_content: 'Select from content',
                    configuration: 'Configuration',
                    upload_video: 'Upload Video'
                } 
            },
            ja: { 
                message: { 
                    done: '完了',
                    send_video: '動画を送る',
                    add_video: '動画を追加',
                    playback_option:  '再生オプション',
                    select_from_content: 'コンテンツから選択',
                    configuration: '設定',
                    upload_video: '動画をアップロード'
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
            title: "Video",
            type: 'video',
            uploadUrl: baseUrl + '/upload/file',
            deleteUrl: baseUrl + '/upload/delete',
            file: [],
            tmp: [],
        }
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
                this.$emit('input', this.loadingCount + 1);
                info.file.delete(
                    this.deleteUrl,
                    delFile.url,
                    null,
                    null,
                    this.axiosFinallyCallback
                );
            }

            this.$emit('input', this.loadingCount + 1);
            info.file.upload(
                this.uploadUrl,
                res => this.file.push(res.data),
                null, // callback
                this.axiosFinallyCallback
            );
        },
        handlePreview() {
            console.log('show preview')
        },
        axiosFinallyCallback () {
            this.$emit('input', this.loadingCount - 1)
        },
        clearFile() {
            if (this.file.length < 1) {
                return
            }

            let key = this.content.indexOf(this.file[0])
            this.content.splice(key, 1)

            this.$emit('input', this.loadingCount + 1);
            File.prototype.delete(
                this.deleteUrl,
                this.file[0].url,
                res => {
                    this.file.pop();
                },
                null,
                this.axiosFinallyCallback
            );
        },
    }
});