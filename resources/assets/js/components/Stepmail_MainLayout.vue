<template>
    <div>
        <div id="container-stepmail-header" class="bg-white rounded p-3">
            <div class="row p-2 align-items-center">
                <div class="col-sm-3 justify-content-between align-items-center" style="font-size: 26px"> 
                    Stepmail
                </div>
                <div class="col-sm-9 align-items-center">
                    <div class="row justify-content-end">
                        <ScenarioNew v-bind:data="dataArray" v-bind:btnClass="this.BtnOutlineDark" />
                        <button v-on:click="viewMode = 'list'" id="button-stepmail-listview" class="btn btn-outline-dark m-1"><i class="fas fa-xs fa-list"></i></button>
                        <button v-on:click="viewMode = 'grid'" id="button-stepmail-gridview" class="btn btn-outline-dark m-1"><i class="fas fa-xs fa-th"></i></button>
                    </div>
                </div>
            </div>
            <div class="row p-3 align-items-center">
                    <button class="btn rounded-cyan m-1" >Active Scenarios</button>
                    <button class="btn rounded-green m-1">Inactive Scenearios</button>
                    <button class="btn rounded-red m-1" :disabled="disableDelete" @click="deleteScenario">Delete Scenarios</button>
            </div>
            <div class="row justify-content-between align-items-center">
                <div class="col-sm-8">
                    <a-checkbox
                        @change="onCheckAllChange"
                        :checked="checkAll"
                        class="ml-2"
                        >
                    </a-checkbox>
                    <select class="custom-select borderless-input ml-2" style="width: 50%; font-size: 12px">
                        <option selected value="">Option 1</option>
                        <option value="">Option 2</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="justify-content-between align-items-center">
            <div v-if="viewMode == 'grid'" class="row">
                <StepmailGridView v-for="(scenario, key) in scenarios" :data="scenario" :key="key"></StepmailGridView>
            </div>
            <div v-else-if="viewMode == 'list'">
                <StepmailListView v-for="(scenario, key) in scenarios" v-bind="scenario" :key="key"></StepmailListView>
            </div>
        </div>
    </div>  
</template>

<script>
import StepmailGridView from "./cards/Stepmail_GridView";
import StepmailListView from "./list/Stepmail_ListView";
import ScenarioNew from "./modals/Scenario_New";

export default {
    //Catch the data [object] from the controller
    props:["data"],
    components:{
        //List down the components that will be used into this template
        StepmailGridView,
        ScenarioNew,
        StepmailListView
    },
    data(){
        return{
            disableDelete: true,
            selected: [],
            checkAll: true, // simple fix
            scenarios: [],
            //Parse the data obtained from the controller and output it as [variable name] data
            dataArray:JSON.parse(this.data),
            //Provide a variable [variable name] "RoundedWhiteBtn" to store the [string] value "btn-outline-dark", which is a CSS class [bootstrap] that will be added to the button on the modal.
            BtnOutlineDark:"btn-outline-dark",
            //Variable for changing the view of the cards from grid to list
            viewMode: "grid"
        }
    },
    methods: {
        onChange (checkedList) {
            // this.indeterminate = !!checkedList.length && (checkedList.length < plainOptions.length)
            // this.checkAll = checkedList.length === plainOptions.length
        },
        onCheckAllChange (e) {
            // Object.assign(this, {
            //     checkedList: e.target.checked ? plainOptions : [],
            //     indeterminate: false,
            //     checkAll: e.target.checked,
            // })
        },
        loadScenario() {
            self = this
            window.axios.get('/stepmail/lists')
            .then(function(response){
                self.scenarios = response.data
            })
        },
        deleteScenario() {
            self = this
            window.axios.delete('/stepmail/' + this.selected)
            .then(function(response){
                self.selected = []
                self.loadScenario()
            })
        }
    },
    beforeMount() {
        this.loadScenario()
    },
}
</script>

<style>
</style>