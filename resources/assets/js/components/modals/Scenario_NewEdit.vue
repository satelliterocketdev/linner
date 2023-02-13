<template>
  <div>
    <button @click="showModal" v-bind:class="buttonClass">New/Edit</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="1450" :footer="null">
        <div>
            Scenario Delivery Set 
            {{ data }}
            <b><h5><input class="borderless-input form-control" type="text" placeholder="Title" v-model="name"></h5></b>
        </div>
        <div class="row align-items-center">
            <div class="col-sm-8 align-items-center">
               Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam lobortis pretium finibus. Nulla facilisi. 
            </div>
            <div class="col-sm-4">
                <div class="row justify-content-end align-items-center">
                    <NewEditMessageTarget v-bind:btnClass="this.RoundedWhiteBtn" />
                    <button class="btn rounded-white m-1">SendToAll</button>
                </div>
            </div>
        </div>
         <div class="row align-items-center">
            <div class="col-sm-8 align-items-center">
               Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam lobortis pretium finibus. Nulla facilisi. 
            </div>
            <div class="col-sm-4 align-items-end text-right">
                <NewEditMessageAction v-bind:btnClass="this.RoundedWhiteBtn" />
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-6 align-items-center">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </div>
            <div class="col-sm-6 align-items-center text-right">
                <div class="row justify-content-end">
                    <ScenarioEditMessage v-bind:btnClass="this.RoundedWhiteBtn" />
                    <ConfirmationWindowDelete v-bind:btnClass="this.RoundedRedBtn" />
                </div>
            </div>
        </div>
        <div id="table-scenario-list" class="p-4 m-2 fixed-container" >
            <div v-for="(data,key) in this.data" :key="key" data-spy="scroll">
                <ScenarioNewEditTable />
            </div>
        </div>
        <div class="footer">
            <div class="row justify-content-center">
                <button class="btn rounded-green m-1" @click="complete">Lorem ipsum</button>
            </div>
        </div>
    </a-modal>
  </div>
</template>

<script>
import ScenarioNewEditTable from "../tables/ScenarioNewEdit_Tables.vue";
import ScenarioEditMessage from "./Scenario_EditMessage.vue";
import ConfirmationWindowDelete from "./ConfirmationWindow_Delete.vue";
import NewEditMessageTarget from "./NewEdit_MessageTarget.vue";
import NewEditMessageAction from "./NewEdit_MessageAction.vue";
export default {
    props: ["data",'btnClass'],
    components:{
        ScenarioNewEditTable,
        ScenarioEditMessage,
        ConfirmationWindowDelete,
        NewEditMessageTarget,
        NewEditMessageAction,
    },
    data() {
        return {
            name: null,
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
        complete() {
            self = this
            console.log(self.id);
            console.log(self.name);
            window.axios.post('/stepmail', {
                name: this.name
            })
                .then(function(response){
                    self.visible = false
                    self.$parent.loadScenario()
                })
        }
    }
}
</script>