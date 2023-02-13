Vue.component ('survey-settings', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn btn-info">Survey Setting</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="750" :footer="null" :destroyOnClose="true">
        <form id="surveyForm" method="post">
        <div class="row p-2">
            Questionnaire create screen
        </div>
        <div class="row">
            <input type="text" class="form-control borderless-input" placeholder="Title" v-model="title" name="title">
        </div>
        <hr>
        <survey-questionnaire></survey-questionnaire>
        <div class="row pt-3">
            Introduction message
        </div>
        <div class="row pb-3">
            <textarea class="form-control" name="intro_message" maxlength="1000" rows="3" style="resize: none" v-model="intro_message"></textarea>
        </div>
        <div class="row">
            Select limited
        </div>
        <div class="row justify-content-center pb-3">
            <a-radio-group buttonStyle="solid" v-model="option">
                <a-radio-button value="1" class="btn m-1 blue">Option 1</a-radio-button>
                <a-radio-button value="2" class="btn m-1 blue">Option 2</a-radio-button>
                <a-radio-button value="3" class="btn m-1 blue">Option 3</a-radio-button>
                <a-radio-button value="4" class="btn m-1 blue">Option 4</a-radio-button>
            </a-radio-group>
        </div>
        <div class="row">
            Notify content of a letter
        </div>
        <div class="row pb-3">
            <textarea class="form-control" name="notification_message" maxlength="1000" rows="3" style="resize: none" v-model="notification_message"></textarea>
        </div>
        <div class="footer">
            <div class="row justify-content-center">
                <button type="button" class="btn rounded-red m-1" @click="reset">Reset</button>
                <button type="button" class="btn rounded-green m-1" @click="addSurvey">Finish/Confirm</button>
            </div>
        </div>
        </form>
    </a-modal>
    </div>`,
    props: ['data', 'reloadTag'],
    data() {
        return {
            title: '',
            option: 0,
            intro_message: '',
            notification_message: '',
            visible: false,
        }
    },
    mounted() {
        this.reset()
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            console.log(e);
            this.visible = false
        },
        reset() {
            this.title = ''
            this.option = 0
            this.intro_message = ''
            this.notification_message = ''
        },
        addSurvey(data) {
            form = $('#surveyForm')

            form.validate({
                rules: {
                    title: "required",
                    intro_message: "required",
                    notification_message: "required",
                }
            })
            
            if (!form.valid()) {
                return
            }

            self = this
            axios.post('survey', {
                title: this.title,
                option: this.option,
                intro_message: this.intro_message,
                notification_message: this.notification_message
            })
            .then(function(response){
                self.visible = false
                self.reloadTag()
            })
        },
    }
});