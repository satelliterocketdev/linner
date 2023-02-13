Vue.component ('messagetarget-conversion', {
    template: 
    `<div>
        <div class="row" style="font-size: 18px"> Served </div>
        <div v-for="(serve, key) in defaults.conversions.serves">
            <div class="row p-1"> Tags </div>
            <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    style="width: 100%"
                    v-model="serve.value"
                >
                    <a-select-option v-for="(survey, surveyKey) in conversions" :key="survey.title">
                        {{ survey.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="serve.option">
                    <option value="first">Include those who have one or more of above conversions</option>
                    <option value="second">Include those who have all of above conversions</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addServe">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
                </div>
            </div>
        </div>

        <div class="row pt-2" style="font-size: 18px"> Exclude </div>
        <div v-for="(exclude, key) in defaults.conversions.excludes">
            <div class="row p-1"> Tags </div>
            <prepend-message-target-selection :values="exclude.value"></prepend-message-target-selection>
            <div class="row p-1">
                <a-select
                    style="width: 100%"
                    v-model="exclude.value"
                >
                    <a-select-option v-for="(survey, surveyKey) in conversions" :key="survey.title">
                        {{ exclude.title }}
                    </a-select-option>
                </a-select>
            </div>
            <div class="row justify-content-between align-items-center p-1">
                <select class="custom-select" style="width: 75%" v-model="exclude.option">
                    <option value="first">Include those who have one or more of above conversions</option>
                    <option value="second">Include those who have all of above conversions</option>
                </select>
                <div>
                    <button type="button" class="btn rounded-green" @click="addExclude">+</button>
                    <button type="button" v-if="key!=0" class="btn rounded-red" @click="deleteExclude(exclude)">-</button>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <button type="button" class="btn m-1 btn-primary"> Conversion Settings</button>
        </div>
    </div>`,
    props: ['data'],
    data() {
        return {
            defaults: {
                conversions: {
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
            conversions: []
        }
    },
    created() {
        this.reloadTag()
        if (this.data.conversions) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.conversions.serves.push({
                value: [],
                option: 'first'
            })
        },
        deleteServe(serve) {
            const index = this.data.conversions.serves.indexOf(serve)
            this.data.conversions.serves.splice(index, 1)
        },
        addExclude() {
            this.defaults.conversions.excludes.push({
                value: [],
                option: 'first'
            })
        },
        deleteExclude(exclude) {
            const index = this.data.conversions.excludes.indexOf(exclude)
            this.data.conversions.excludes.splice(index, 1)
        },
        reloadTag() {
            self = this
            axios.get('conversion/lists')
            .then(function(response){
                self.conversions = []
                response.data.forEach(function(value, index){
                    self.conversions.push({
                        id: value.id,
                        title: value.title,
                    })
                })
            })
        }
    }
});