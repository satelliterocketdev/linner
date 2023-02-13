<template>
     <div>
        <button @click="showModal" v-bind:class="buttonClass">Execute</button>
        <a-modal :closable="false" :centered="true" v-model="visible" :width="450" :footer="null">
            <div class="row justify-content-center p-4">
                <b>Change the test user</b>
            </div>
            <div class="row justify-content-center">
                <!-- <i class="far fa-5x fa-user-circle"></i> -->
                <a-avatar :size="100" icon="user" />
            </div>
            <div class="row justify-content-center p-4" style="font-size: 18px">
                <b>USERNAME</b>
            </div>
            <div class="row justify-content-center">
                Testing users to change the username.
            </div>
            <div class="row justify-content-center">
                Are you sure to continue?.
            </div>
            <div class="row justify-content-center p-2">
                <button @click="handleOk" class="btn rounded-red m-1">Cancel</button>
                <button class="btn rounded-green m-1">Execute</button>
            </div>
        </a-modal>
    </div>
    
</template>

<script>
export default {
    name: "TestUserConfirmationChange",
    props: ["btnClass"],
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
