<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass">MessageAction</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="550" :footer="null">
            <div class="row justify-content-center p-2">
                <button v-on:click="currentContent = 'Tag'" class="btn m-1 rounded-blue">Tag</button>
                <button v-on:click="currentContent = 'Scenario'" class="btn m-1 rounded-blue">Scenario</button>
                <button v-on:click="currentContent = 'Survey'" class="btn m-1 rounded-blue">Survey</button>
                <button v-on:click="currentContent = 'Menu'" class="btn m-1 rounded-blue">Menu</button>
            </div>
            <hr>
            <div class="p-4">
                <div class="row p-1">
                    <b>{{this.currentContent}} set</b>
                </div>
                <div class="row p-1">
                    {{ this.currentContent}}
                </div>
                <div class="row align-items-center p-2">
                    <div class="col-sm-10">
                        <div class="row p-1">
                            <input type="text" class="form-control">
                        </div>
                        <!-- <div v-if="currentContent == 'Tag'" class="row p-1">
                            <select class="form-control">
                                <option selected >Add above {{this.currentContent}}</option>
                                <option>Remove above {{this.currentContent}}</option>
                            </select>
                        </div>
                        <div v-else class="row p-1"> -->
                        <div class="row p-1">
                            <select class="form-control" v-model.lazy="isActive">
                                <option selected v-bind:value="false">Add above {{this.currentContent}}</option>
                                <option v-bind:value="true">Remove above {{this.currentContent}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="row justify-content-end">
                            <button class="btn rounded-green"> + </button>
                        </div>
                    </div>
                </div>
                <div v-if="currentContent == 'Tag'">
                    <div class="row p-1">
                        <b>Create Tags</b>
                    </div>
                    <div class="row p-1">
                        Tag name
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div v-else-if="currentContent == 'Scenario'">
                    <div class="row p-1">
                        <b>Delivery set</b>
                    </div>
                    <div class="row p-1">
                        Scenario number start
                    </div>
                    <div class="row align-items-center p-1">
                        <select class="form-control w-50 h-100" :disabled="isActive">  
                            <option >immediate</option>
                            <option>1 hour later</option>
                            <option>3 hours later</option>
                            <option>12 hours later</option>
                            <option>24 hours later</option>
                        </select>
                    </div>
                    <div class="row align-items-center p-1">
                        <input type="number" class="form-control w-25 h-100 mr-2" :disabled="isActive">
                        Delivery from
                    </div>
                </div>
                <div v-else-if="currentContent == 'Survey'">
                    <div class="row justify-content-center">
                        <!-- <button class="btn btn-block btn-info w-50 h-100" :disabled="isActive">Survey Settings</button> -->
                        <SurveySettings v-bind:btnClass="this.RoundedInfoBtn" />
                    </div>
                </div>
                <div v-else-if="currentContent == 'Menu'">
                    <div class="row p-1">
                        <a-checkbox  :disabled="isActive">Lorem ipsum</a-checkbox>
                    </div>
                    <div class="row justify-content-between align-items-center">
                        <input type="date" class="form-control w-50 h-100 m-1"  :disabled="isActive">
                        <input type="time" class="form-control w-25 h-100 m-1"  :disabled="isActive">
                        Start
                    </div>
                    <div class="row justify-content-between align-items-center">
                        <input type="date" class="form-control w-50 h-100 m-1"  :disabled="isActive">
                        <input type="time" class="form-control w-25 h-100 m-1"  :disabled="isActive">
                        End
                    </div>
                    <div class="row justify-content-center align-items-center p-1">
                        <button class="btn btn-block btn-info w-50 h-100"  :disabled="isActive">Menu Settings</button>
                    </div>
                </div>
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
import SurveySettings from "./SurveySettings.vue"
export default {
    name: "NewEditMessageAction",
    components: {
        SurveySettings
    },
    props: ["btnClass"],
    data() {
        return {
            currentContent: "Tag",
            visible: false,
            buttonClass: "btn m-1 "+ this.btnClass,
            RoundedWhiteBtn: "rounded-white",
            RoundedRedBtn: "rounded-red",
            RoundedCyanBtn: "rounded-cyan",
            RoundedGreenBtn: "rounded-green",
            RoundedBlueBtn: "rounded-blue",
            RoundedInfoBtn: "rounded-info",
            isActive: false,
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
