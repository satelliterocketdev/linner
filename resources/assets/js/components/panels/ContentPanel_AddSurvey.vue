<template>
<div>
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
    <div class="row justify-content-left pb-3">
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
</div>
</template>

<script>
import SurveyQuestionnaire from "../custom/Survey_Questionnaire.vue";
export default {
    name: "CreateSurvey",
    components: {
        SurveyQuestionnaire,
    }
}
</script>
