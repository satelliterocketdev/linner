<template>
<div>
    <div class="row p-2">
        Questionnaire contents
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    Question
                </div>
                <div class="card-body">
                    <div class="row">
                        HELLO WORLD
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row justify-content-center">
                        <button class="btn rounded-green"> + </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="card shadow">
                <div class="card-header">
                    TEST
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            Actions
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">None</button>
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">URL Open</button>
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">Outbound</button>
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">Send the message</button>
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">Reply form</button>
                            <button class="btn btn-block btn-outline-dark" style="font-size: 12px">Scenario Transition</button>
                            <div class="pt-2">
                                <span >Reply</span>
                                <span><a-radio>1 Reply</a-radio></span>
                                <span><a-radio>2 Reply</a-radio></span>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="row">
                                Automatic Reply
                            </div>
                            <div class="row">
                                <a-checkbox></a-checkbox>
                                Text
                                <textarea class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
                            </div>
                            <div class="row">
                                <a-checkbox></a-checkbox>
                                Template
                                <input type="text" class="form-control">
                            </div>
                            <div class="row">
                                Add Tags
                                <input type="text" class="form-control">
                            </div>
                            <div class="row">
                                Released Tags
                                <input type="text" class="form-control">
                            </div>
                            <div class="row">
                                Friends
                                <input type="text" class="form-control">
                            </div>
                            <div class="row">
                                Set pieces
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <div class="row justify-content-center p-2">
                        <button class="btn btn-outline-dark">Button</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "SurveyQuestionnaire",
}
</script>
