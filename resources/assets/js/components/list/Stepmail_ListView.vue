<template>
<div class="pt-2 pb-2 pl-3 pr-3">
    <div class="row justify-content-between align-items-center border rounded bg-white shadow p-3">
        <a-checkbox></a-checkbox>
        <!-- <div class="row justify-content-center align-items-center"> -->
            <b>{{this.name}}</b>
            <b>{{this.name}}</b>
            <b>{{this.name}}</b>
            <b>{{this.name2}}</b>
            <b>{{this.name2}}</b>
            <b>{{this.name2}}</b>
        <!-- </div> -->
        <ScenarioNewEdit v-bind:btnClass="this.RoundedGreenBtn" v-bind:data="this.data"></ScenarioNewEdit>
        <button class="btn m-1 rounded-cyan">Copy</button>
        <button class="btn m-1 rounded-red">Confirm</button>
        <button class="btn m-1 rounded-grey">Preview</button>
    </div>
</div>
</template>

<script>
import  ScenarioNewEdit from "../modals/Scenario_NewEdit";
export default {
    name: "StepMailListView",
    props:['name','name2',"data"],
    components: {
        ScenarioNewEdit
    },
    data(){
        return{
            RoundedGreenBtn:"rounded-green"
        }
    }
}
</script>
