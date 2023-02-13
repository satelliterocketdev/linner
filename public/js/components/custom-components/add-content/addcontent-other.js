Vue.component ('addcontent-other', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn m-1" style="border: 1px; border-style: solid; height: 150px; width: 150px;">
        <div class="row justify-content-center mb-1">
            <div v-if="file.length>0">
                <div class="float-right" @click="clearFile"><i class="fa fa-times"></i></div>
                <img alt="example" style="max-width: 100%; max-height: 80px" :src="file[0].featured_url" />
            </div>
            <div v-else><i class="fa fa-5x fa-paperclip"></i></div>
        </div>
        <div class="row justify-content-center">
            {{$t('message.send_file')}}
        </div>
    </button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            {{$t('message.add_file')}}
        </div>
        <div class="row p-4">
            <a-upload-dragger class="col-sm-12" name="file" :fileList="fileList" :multiple="true" :beforeUpload="() =>{return false}" @change="handleChange">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">{{$t('message.upload_file')}}</p>
            </a-upload-dragger>
        </div>
<!--        <div class="px-4 mx-auto w-50">-->
<!--            <div class="border rounded p-2" style="height: 100%; width: auto">-->
<!--                <div v-if="file.length>0">-->
<!--                    <img class="d-block m-auto" :src="file[0].featured_url" />-->
<!--                </div>-->
<!--                <div v-else>Preview</div>-->
<!--            </div>-->
<!--        </div>-->
        <div class="row justify-content-center pt-4">
            <button class="btn rounded-green" @click="close">{{$t('message.done')}}</button>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    attachment: 'Attachment',
                    done: 'Done',
                    send_file: 'Send a file',
                    add_file: 'Add File',
                    upload_file: 'Upload File'
                } 
            },
            ja: { 
                message: { 
                    attachment: '添付',
                    done: '完了',
                    send_file: 'ファイルを送る',
                    add_file: 'ファイルを追加',
                    upload_file: 'ファイルをアップロード'
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
            title: "Other",
            type: 'other',
            uploadUrl: baseUrl + '/upload/file',
            deleteUrl: baseUrl + '/upload/delete',
            file: [],
            fileList: [],
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
                this.$emit('input', this.loadingCount + 1);
                info.file.delete(
                    this.deleteUrl,
                    this.file[0].url,
                    (res) => {
                        this.file.pop();
                        this.fileList.pop();
                    },
                    null, // catch
                    this.axiosFinallyCallback // finally
                );
            }

            if (info.file.status === 'removed') {
                return;
            }

            this.$emit('input', this.loadingCount + 1);
            info.file.upload(
                this.uploadUrl,
                (res) => { // callback
                    this.file.push(res.data)
                    this.fileList.push(info.file)
                },
                null, // catch
                this.axiosFinallyCallback // finally
            )
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
            this.fileList[0].delete(
                this.deleteUrl,
                this.file[0].url,
                res => {
                    this.file.pop();
                    this.fileList.pop();
                },
                null,
                this.axiosFinallyCallback
            );
        },
    }
});