<template>
<div>
    <button @click="showModal" v-bind:class="buttonClass">Survey Setting</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="750" :footer="null">
        <div class="row p-2">
            Questionnaire create screen
        </div>
        <div class="row">
            <input type="text" class="form-control borderless-input" placeholder="Title">
        </div>
        <hr>
        <SurveyQuestionnaire />
        <div class="row pt-3">
            Introduction message
        </div>
        <div class="row pb-3">
            <textarea class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
        </div>
        <div class="row">
            Select limited
        </div>
        <div class="row justify-content-center pb-3">
            <button class="btn rounded-blue m-1">Option 1</button>
            <button class="btn rounded-blue m-1">Option 2</button>
            <button class="btn rounded-blue m-1">Option 3</button>
            <button class="btn rounded-blue m-1">Option 4</button>
        </div>
        <div class="row">
            Notify content of a letter
        </div>
        <div class="row pb-3">
            <textarea class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
        </div>
        <div class="footer">
            <div class="row justify-content-center">
                <button class="btn rounded-red m-1">Reset</button>
                <button class="btn rounded-green m-1">Finish/Confirm</button>
            </div>
        </div>
    </a-modal>
</div>   
</template>

<script>
import SurveyQuestionnaire from "../custom/Survey_Questionnaire.vue";
export default {
    name: "SurveySettings",
    props: ["btnClass"],
    components: {
        SurveyQuestionnaire,
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
            RoundedInfoBtn: "rounded-info",
        }
    },
    methods: {
        showModal() {
            this.visible = true
            },
        handleOk(e) {
            this.visible = false
        },
    },
}
</script>
