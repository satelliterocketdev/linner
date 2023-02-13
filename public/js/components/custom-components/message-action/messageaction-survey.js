Vue.component ('messageaction-survey', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> Scenario set </div>
    <div v-for="(serve, key) in defaults.surveys.serves">
        <div class="row p-1"> Tag set</div>
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
            >
                <a-select-option v-for="(survey, surveyKey) in surveys" :key="survey.title">
                    {{ survey.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="serve.option">
                <option value="first">Add above scenario</option>
                <option value="second">Remove above scenario</option>
            </select>
            <div>
                <button type="button" class="btn rounded-green" @click="addServe">+</button>
                <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>

        <div class="row align-items-center p-2">
            <span> Delivery set</span>
            <select class="custom-select ml-2" style="width: 50%" :disabled="serve.option == 'second'" v-model="serve.delivery.timing">
                <option value="0">Immediate</option>
                <option value="1">1 hour later</option>
                <option value="3">3 hours later</option>
                <option value="12">12 hours later</option>
                <option value="24">24 hours later</option>
            </select>
        </div>
    </div>

    <div class="row justify-content-center p-2">
        <survey-settings></survey-settings>
    </div>
    </div>`,
    props: ['data'],
    data() {
        return {
            isActive: false,
            defaults: {
                surveys: {
                    serves: [{
                        value: [],
                        option: 'first',
                        delivery: {
                            timing: ''
                        }
                    }],
                }
            },
            surveys: []
        }
    },
    created() {
        this.reloadTag()
        if (this.data.surveys) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.surveys.serves.push({
                value: [],
                option: 'first',
                delivery: {
                    timing: ''
                }
            })
        },
        deleteServe(serve) {
            const index = this.data.surveys.serves.indexOf(serve)
            this.data.surveys.serves.splice(index, 1)
        },
        reloadTag() {
            self = this
            axios.get('survey/lists')
            .then(function(response){
                self.surveys = []
                response.data.forEach(function(value, index){
                    self.surveys.push({
                        id: value.id,
                        title: value.title,
                    })
                })
            })
        }
    }
});