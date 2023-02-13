<template>
    <div class="row justify-content-around align-items-center pb-2">
        <!-- <input type="checkbox" class=""> -->
        <a-checkbox> </a-checkbox>
        <div class="card shadow pl-4 pr-4 pt-2 pb-2">
            <div class="row justify-content-center"><b> {{this.time}} </b></div>
            <div class="row justify-content-center">{{this.text1}}</div>
        </div>
        <div class="card shadow">
            <div class="row justify-content-around align-items-center pl-4 pr-4 pt-2 pb-2">
                <div> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam lobortis pretium finibus.</div>
                <select class="outline-select">
                    <option selected value="">Option 1</option>
                    <option value="">Option 2</option>
                    <option value="">Option 3</option>
                    <option value="">Option 4</option>
                    <option value="">Option 5</option>
                    <option value="">Option 6</option>
                    <option value="">Option 7</option>
                    <option value="">Option 8</option>
                </select>
                <ScenarioEditMessage v-bind:btnClass="this.RoundedGreenBtn" />
                <ConfirmationWindowTest  v-bind:btnClass="this.RoundedRedBtn" />
                <button class="btn rounded-grey m-1">Scenario_Preview</button>
            </div>
        </div>
    </div>
</template>

<script>
import ScenarioEditMessage from "../modals/Scenario_EditMessage.vue";
import ConfirmationWindowTest from "../modals/ConfirmationWindow_Test.vue";
export default {
    name: 'scenarioNewEdit_table',
    components: {
        ScenarioEditMessage,
        ConfirmationWindowTest,
    },
    data() {
        return {
            time: "12: 00",
            text1: "Lorem ipsum",
            text2: "Lorem ipsum dolor",
            RoundedWhiteBtn: "rounded-white",
            RoundedRedBtn: "rounded-red",
            RoundedCyanBtn: "rounded-cyan",
            RoundedGreenBtn: "rounded-green",
            RoundedBlueBtn: "rounded-blue",
        }
    }
}
</script>
