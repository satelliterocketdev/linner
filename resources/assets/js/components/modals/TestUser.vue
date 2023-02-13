<template>
     <div>
        <button @click="showModal" v-bind:class="buttonClass">TestUser</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
            <div class="row justify-content-center p-2">
                <b>Change the test user</b>
            </div>
            <div class="row justify-content-center p-4">
                <b>Enter your test username </b>
            </div>
            <b><input class="form-control" type="text"></b>
            <div class="row justify-content-center p-4">
                <!-- <button class="btn rounded-green m-1">Execute</button> -->
                <TestUserConfirmationChange v-bind:btnClass="this.RoundedGreenBtn" />
                <button @click="handleOk" class="btn rounded-red m-1">Cancel</button>
            </div>
        </a-modal>
    </div>
    
</template>

<script>
import TestUserConfirmationChange from "./TestUser_ConfirmationChange.vue";
export default {
    name: "TestUser",
    props: ["btnClass"],
    components: {
        TestUserConfirmationChange
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
