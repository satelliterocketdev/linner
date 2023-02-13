Vue.component ('addimage-createcarousel', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn m-1 btn-outline-light float-btn"><i class="fas fa-plus"></i></button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-center" style="font-size: 20px">
            Add {{title}}
        </div>
        <div class="row p-4">
            <a-upload-dragger name="file" :showUploadList="false" :action="uploadUrl" @change="handleChange" accept=".jpeg, .jpg, .png">
                <p class="ant-upload-drag-icon">
                <a-icon type="inbox" />
                </p>
                <p class="ant-upload-text">Click or drag file to this area to upload</p>
                <p class="ant-upload-hint">Support for a single or bulk upload. Strictly prohibit from uploading company data or other band files</p>
            </a-upload-dragger>
        </div>
        <div class="row justify-content-between p-3">
            <div class="col-sm-6">
                <div class="row p-1">
                    <previously-uploaded v-bind:title="title" :content="file" :type="type"></previously-uploaded>
                </div>
                <div class="row p-1">
                    URL
                </div>
                <div v-if="file.length>0" class="row p-1">
                    <input type="text" class="form-control" :value="file[0].url" readonly />
                </div>
                <div class="row p-1" v-else>
                    <input type="text" class="form-control" readonly />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="border rounded p-2" style="height: 100%; width: auto">
                    <div v-if="file.length>0">
                        <img class="img-fluid" :src="file[0].featured_url" />
                    </div>
                    <div v-else>Preview</div>
                </div>
            </div>  
        </div>
        <div class="row justify-content-center pt-4">
            <button class="btn rounded-green" @click="close">Finish/Confirm</button>
        </div>
    </a-modal>
    </div>`,
    props: ['content'],
    data() {
        return {
            visible: false,
            title: "Image",
            type: 'image',
            uploadUrl: baseUrl + '/upload',
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
            self = this
            const status = info.file.status;
            if (status === 'done') {
                this.file.pop()
                this.file.push(info.file.response)
            }
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