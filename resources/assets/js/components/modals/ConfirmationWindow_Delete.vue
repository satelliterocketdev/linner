<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass">ConfirmationMessage_Delete</button>
        <a-modal :closable="false" :centered="true" v-model="visible" @ok="handleOk" :width="400" :footer="null">
            <div class="row justify-content-center p-2">
                <b>Are you sure you want to delete?</b>
            </div>
            <div class="row justify-content-center p-4">
                <button @click="handleOk" class="btn rounded-white m-1">Return to previous window</button>
                <button class="btn rounded-red m-1">Execute</button>
            </div>
        </a-modal>
    </div>
    
</template>

<script>
export default {
    name: "ConfirmationWindowDelete",
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

