<template>
<div>
    <button @click="showModal" class="btn flat-button-white">
        <i v-bind:class='this.iconClass'></i>
        <!-- <div v-if="this.uploadType == 'Stamp'">
            <img src="/images/sticker/1_6.png" height="69px" width="69px">
        </div> -->
        <div class="row justify-content-center" style="font-size: 12px">Add {{this.uploadType}}</div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="700" :footer="null">
        <div class="p-2">
            <div class="row justify-content-center p-4">
                <b>Add {{this.uploadType}}</b>
            </div>
            <a-upload-dragger name="file" :multiple="true" action="//jsonplaceholder.typicode.com/posts/" @change="handleChange">
                <label class="ant-upload-drag-icon">
                    <a-icon type="inbox" />
                </label>
                <label class="ant-upload-text">Click or drag {{this.uploadType}} file to this area to upload</label>
            </a-upload-dragger>
            <div class="row justify-content-center">
                <div class="col-sm-6 p-4">
                    <div class="row pb-2">
                        <!-- <button class="btn btn-info m-1 btn-block">Add Contents/Previously Uploaded</button> -->
                        <PreviouslyAdded v-bind:content="this.uploadType" v-bind:btnClass="this.RoundedWhiteBtn" />
                    </div>
                    <div v-if="this.uploadType == 'Video' || this.uploadType == 'Audio'">
                        <div class="row">
                            Crop {{this.uploadType}} file
                        </div>
                        <div class="row justify-content-between align-items-center pb-2">
                            <input type="number" min="0" max="60" class="number-input">
                            <label>Start time</label>
                            <input type="number" min="0" max="60" class="number-input">
                            <label>End time</label>
                        </div>
                    </div>
                    <div v-if="this.uploadType == 'Template' || this.uploadType == 'Audio' ">
                        <div class="row">
                        </div>
                    </div>
                    <div v-else>
                        <div class="row">
                            URL
                        </div>
                        <div class="row p-1">
                            <input type="text" class="form-control">        
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 p-4">  
                    <div class="row dashed-border p-4">
                        P R E V I E W
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="row justify-content-center p-2">
                <button class="btn rounded-green m-1">Finish/Confirm</button>
            </div>
        </div>
    </a-modal>
</div>
</template>

<script>
import PreviouslyAdded from "./Previously_Added.vue";
export default {
    name: "AddContentUploadContentContainer",
    props: ['uploadType', 'iconClass'],
    components: {
        PreviouslyAdded,
    }, 
    data() {
        return {
            visible: false,
            buttonClass: "btn m-1 "+ this.btnClass,
            RoundedWhiteBtn: "rounded-white",
            RoundedRedBtn: "rounded-red",
            RoundedCyanBtn: "rounded-cyan",
            RoundedGreenBtn: "rounded-green",
            RoundedBlueBtn: "rounded-blue",
            // iconClass: "" + this.iconName,
        }
    },
     methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        handleChange(info) {
            const status = info.file.status;
            if (status !== 'uploading') {
                console.log(info.file, info.fileList);
            }
            if (status === 'done') {
                this.$message.success(`${info.file.name} file uploaded successfully.`);
            } else if (status === 'error') {
                this.$message.error(`${info.file.name} file upload failed.`);
            }
        },
    }
}
</script>

<style scoped>
    .number-input {
        border-style: solid;
        border-width: 1px;
        border-radius: 5px;
        border-color: #ced4da;
        padding: 5px;
    }
</style>

