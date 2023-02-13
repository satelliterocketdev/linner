<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass">New/Edit_Message</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="1000" :footer="null">
            <div>
                Lorem ipsum
                <b><h5><input class="borderless-input form-control" type="text" placeholder="Title"></h5></b>
            </div>
            <div class="row p-1">
                <EditMessageComponent /> 
            </div>
            <hr>
            <div class="row justify-content-center p-1">
                <button v-on:click="switchComponent(0)" class="btn rounded-blue m-1">Create Message</button>
                <button v-on:click="switchComponent(1)" class="btn rounded-blue m-1">Create Carousel</button>
                <button v-on:click="switchComponent(2)" class="btn rounded-blue m-1">Create Survey</button>
                <button v-on:click="switchComponent(3)" class="btn rounded-blue m-1">Add Map</button>
                <button v-on:click="switchComponent(4)" class="btn rounded-blue m-1">Add Contact</button>
            </div>
            <!--CONTENT PANEL-->
            <div id="content-panel" class="p-3">
                <component v-bind:is="currentComponent"></component>  
            </div>
            <div class="footer pt-1">
                <div class="row align-items-end">
                    <div class="col-sm-8 text-left">
                        <button class="btn rounded-blue m-1">Send/Schedule Send</button>
                        <button class="btn rounded-green m-1">Save as draft</button>
                        <button class="btn rounded-red m-1">Send test</button> 
                    </div>
                    <div class="col-sm-4">
                        <button class="btn btn-block rounded-cyan m-1">Select Draft</button>
                        <button class="btn btn-block rounded-white m-1">Save as template</button>
                    </div>
                </div>
            </div>
        </a-modal>
    </div>
</template>

<script>
import EditMessageComponent from "../custom/EditMessage_Component.vue";
import CreateMessage from "../panels/ContentPanel_CreateMessage.vue";
import CreateCarousel from "../panels/ContentPanel_CreateCarousel.vue";
import AddMap from "../panels/ContentPanel_AddMap.vue";
import AddContact from "../panels/ContentPanel_AddContact.vue";
import CreateSurvey from "../panels/ContentPanel_AddSurvey.vue";
export default {
    name: "ScenarioEditMessage",
    props: ["btnClass"],
    components: {
        EditMessageComponent,
        CreateMessage, 
        CreateCarousel,
        AddMap,
        CreateSurvey,
    },
    data() {
        return {
            visible: false,
            buttonClass: "btn m-1 "+ this.btnClass,
            currentComponent: CreateMessage,
        }
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        switchComponent(componentName)
        {
           switch(componentName) {
                case 0:
                    this.currentComponent = CreateMessage; 
                    break;
                case 1:
                    this.currentComponent = CreateCarousel;
                    break;
                case 2:
                    this.currentComponent = CreateSurvey;
                    break;
                case 3:
                    this.currentComponent = AddMap;
                    break;
                case 4:
                    this.currentComponent = AddContact;
                    break;
                default:
                    this.currentComponent = CreateMessage;
                    break;
           }
        }
    }
}
</script>


<style scoped>
.text-area {
    overflow-y: scroll;
    height: 131px;
    width: 100%;
    resize: none;
}
</style>
