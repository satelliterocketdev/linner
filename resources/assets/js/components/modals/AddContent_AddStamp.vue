<template>
<div>
    <button @click="showModal" class="btn flat-button-white" style="font-size: 12px">
        <img src="/images/sticker/1_6.png" height="69px" width="69px">
        <div class="row justify-content-center" style="font-size: 12px">Add Stamp</div>
    </button>
    <a-modal :centered="true" v-model="visible" :width="700" :footer="null">
        <div class="row justify-content-center">
            <AddStampStickers />
        </div>
        <div class="footer">
            <div class="row justify-content-center">
                <button class="btn rounded-green m-1">Finish/Confirm</button>
            </div>
        </div>
    </a-modal>
</div>
</template>

<script>
import AddStampStickers from "../panels/AddStamp_Stickers.vue"
export default {
    name: "AddContentAddStamp",
    components: {
        AddStampStickers,
    },
    data() {
        return {
            visible: false,
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
