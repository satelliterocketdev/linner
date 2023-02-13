<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass"><i class="fas fa-inbox mr-2"></i>Add Contents</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="550" :footer="null">
            <div class="row justify-content-center align-items-center">
                <AddContentUploadContentContainer uploadType="Image" v-bind:iconClass="Image"/>
                <AddContentUploadContentContainer uploadType="Video" v-bind:iconClass="Video"/>
                <AddContentUploadContentContainer uploadType="Audio"  v-bind:iconClass="Audio"/>
            </div>
            <div class="row justify-content-center align-items-center">
                <!-- <AddContentUploadContentContainer uploadType="Template"  v-bind:iconClass="Template" /> -->
                <AddContentOpenTemplate />
                <AddContentUploadContentContainer uploadType="Other"  v-bind:iconClass="Other" />
                <!-- <AddContentUploadContentContainer uploadType="Stamp"  v-bind:iconClass="Stamp" /> -->
                <AddContentAddStamp />
            </div>
            <div class="footer pt-4">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-4 align-items-center">
                    </div>
                    <div class="col-sm-4 align-items-center">
                        <button class="btn rounded-green m-1">Finish/Confirm</button>
                    </div>
                    <div class="col-sm-4 text-right align-items-center">
                        <button @click="handleOk" class="btn m-1"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </a-modal>
    </div>
</template>

<script>
import AddContentUploadContentContainer from "./AddContent_UploadContentContainer.vue"
import AddContentOpenTemplate from "./AddContent_OpenTemplate.vue"
import AddContentAddStamp from "./AddContent_AddStamp.vue"

export default {
    name: "AddContentMainMenu",
    props: ["btnClass"],
    components: {
        AddContentUploadContentContainer,
        AddContentOpenTemplate,
        AddContentAddStamp,
    },
    data() {
        return {
            visible: false,
            buttonClass: "btn btn-block "+ this.btnClass,
            RoundedWhiteBtn: "rounded-white",
            RoundedRedBtn: "rounded-red",
            RoundedCyanBtn: "rounded-cyan",
            RoundedGreenBtn: "rounded-green",
            RoundedBlueBtn: "rounded-blue",
            Image: "far fa-5x fa-file-image",
            Video: "fas fa-5x fa-film",
            Audio: "fas fa-5x fa-microphone",
            Other: "fas fa-5x fa-paperclip",
            Template: "far fa-5x fa-edit",
            Stamp: ""
        }
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
    }
}
</script>
