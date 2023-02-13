Vue.component ('messagetarget-survey', {
    template: 
    `<div>
        <div class="row" style="font-size: 18px"> Served </div>
        <div v-for="(serve, key) in defaults.surveys.serves">
            <div class="row p-1"> Tags </div>
            <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
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
                    <option value="first">Include those who answered the survey</option>
                    <option value="second">Include those who have not answered the survey</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addServe">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
                </div>
            </div>
        </div>

        <div class="row pt-2" style="font-size: 18px"> Exclude </div>
        <div v-for="(exclude, key) in defaults.surveys.excludes">
            <div class="row p-1"> Tags </div>
            <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    style="width: 100%"
                    v-model="exclude.value"
                >
                    <a-select-option v-for="(survey, surveyKey) in surveys" :key="survey.title">
                        {{ exclude.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="exclude.option">
                    <option value="first">Include those who answered the survey</option>
                    <option value="second">Include those who have not answered the survey</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addExclude">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteExclude(exclude)">-</button>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center p-2">
            <survey-settings :data="surveys" :reload-tag="reloadTag"></survey-settings>
        </div>
    </div>`,
    props: ['data'],
    data() {
        return {
            defaults: {
                surveys: {
                    serves: [{
                        value: [],
                        option: 'first'
                    }],
                    excludes: [{
                        value: [],
                        option: 'first'
                    }]
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
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.data.surveys.serves.indexOf(serve)
            this.data.surveys.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.surveys.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.surveys.excludes.indexOf(exclude)
            this.data.surveys.excludes.splice(index, 1)
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