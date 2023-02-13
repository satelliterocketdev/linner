Vue.component ('addimage-createcarousel', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn m-1 btn-outline-light position-absolute "><i class="fas fa-plus"></i></button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.title')}}
        </div>
        <div class="row">
            <a-upload-dragger class="col-sm-12" name="file" :showUploadList="false" :beforeUpload="() =>{return false}" @change="handleChange">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                <p class="ant-upload-text">{{$t('message.hint_title')}}</p>
            </a-upload-dragger>
<!--            <a-upload-->
<!--                name="avatar"-->
<!--                listType="picture-card"-->
<!--                class="avatar-uploader"-->
<!--                :showUploadList="false"-->
<!--                :action="uploadUrl"-->
<!--                @change="handleChange"-->
<!--            >-->
<!--                :beforeUpload="beforeUpload"-->
        </div>
        <div class="row justify-content-between p-3">
            <div class="col-sm-6">
                <div class="row p-1">
                    <previously-uploaded v-bind:title="title" :content="file" :type="type"></previously-uploaded>
                </div>
                <div class="row p-1">
                    {{$t('message.url')}}
                </div>
                <div v-if="file.length>0" class="row p-1">
                    <input type="text" class="form-control" :value="file[0].url" readonly />
                </div>
                <div class="row p-1" v-else>
                    <input type="text" class="form-control" :placeholder="$t('message.url_write')" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border rounded p-2" style="height: 100%; width: auto">
                    <div v-if="file.length>0">
                        <img class="img-fluid" :src="file[0].featured_url" />
                    </div>
                    <div v-else>{{$t('message.preview')}}</div>
                </div>
            </div>  
        </div>
        <div class="row justify-content-center pt-4">
            <button class="btn rounded-green" @click="close">{{$t('message.finish')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    title: "Add Image",
                    hint_title: "Click or drag file to this area to upload",
                    preview: "Preview",
                    finish: 'Finish',
                    url: "URL",
                    url_write: "Input Url"
                }
            },
            ja: {
                message: {
                    title: "画像追加",
                    hint_title: "画像をアップロード",
                    preview: "プレビュー",
                    finish: '完了',
                    url: "URL設定",
                    url_write: "URL入力"
                }
            }
        }
    },
    props: ['content'],
    data() {
        return {
            visible: false,
            title: "Image",
            type: 'image',
            uploadUrl: baseUrl + '/upload/image',
            file: [],
            tmp: [],
        }
    },
    methods: {
        close() {
            if (this.file.length > 0 && this.tmp != this.file[0]) {
                Object.assign(this.content, this.file[0])
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
            let formData = new FormData()
            formData.append('file', info.file)
            axios.post(this.uploadUrl, formData)
            .then(res => {
                this.file.pop()
                this.file.push(res.data)
            })
        },
        handlePreview() {
            console.log('show preview')
        },
        clearFile() {
            if (this.file.length < 1) {
                return
            }

            // key = this.content.indexOf(this.file[0])
            // this.content.splice(key, 1)
            // this.file.pop()
        },
    }
});