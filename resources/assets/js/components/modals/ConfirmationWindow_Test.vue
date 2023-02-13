<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass">ConfirmationMessage_Test</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
            <div class="row justify-content-center p-4">
                <b>Send test mail to this username?</b>
            </div>
            <div class="row justify-content-center p-4">
                <!-- <button class="btn rounded-white m-1">TestUser</button> -->
                <TestUser v-bind:btnClass="this.RoundedWhiteBtn" />
                <button class="btn rounded-green m-1">Execute</button>
                <button @click="handleOk" class="btn rounded-red m-1">Return to previous window</button>
            </div>
        </a-modal>
    </div>
</template>

<script>
import TestUser from "./TestUser.vue"
export default {
    name: "ConfirmationWindowTest",
    props: ["btnClass"],
    components: {
        TestUser
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
