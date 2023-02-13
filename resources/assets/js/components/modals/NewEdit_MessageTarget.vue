<template>
    <div>
        <button @click="showModal" v-bind:class="buttonClass">MessageTarget</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="550" :footer="null">
            <div class="row justify-content-center p-2">
                <button v-on:click="currentContent = 'Tag'" class="btn m-1 rounded-blue">Tag</button>
                <button v-on:click="currentContent = 'Scenario'" class="btn m-1 rounded-blue">Scenario</button>
                <button v-on:click="currentContent = 'Survey'" class="btn m-1 rounded-blue">Survey</button>
                <button v-on:click="currentContent = 'Source'" class="btn m-1 rounded-blue">Source</button>
                <button v-on:click="currentContent = 'Conversion'" class="btn m-1 rounded-blue">Conversion</button>
                <button v-on:click="currentContent = 'Name'" class="btn m-1 rounded-blue">Name</button>
                <button v-on:click="currentContent = 'RegisterDate'" class="btn m-1 rounded-blue">RegisterDate</button>
            </div>
            <hr>
            {{ name }}
            <div class="p-4">
                <div class="row" style="font-size: 18px">
                    Served
                </div>
                <div class="row">
                    {{this.currentContent}} 1 
                </div>
                <div v-if="currentContent != 'RegisterDate'">
                    <input type="text" class="form-control" v-model="name[tag.value]">
                </div>
                <div class="row align-items-center p-3">
                    <div class="col-sm-10">
                        <div v-if="currentContent == 'Name'">
                            <div class="row justify-content-between" v-model="name[tag.option]">
                                <a-checkbox>Only show exact matches</a-checkbox>
                                <a-checkbox>Include similar results</a-checkbox>
                            </div>
                        </div>
                        <div v-else-if="currentContent == 'RegisterDate'">
                            <div class="row justify-content-between align-items-center">
                                <input type="date" class="form-control">
                                <label>from</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div v-else class="row justify-content-center">
                            <select class="form-control">
                                <option selected> {{this.currentContent}} Options </option>
                                <option>Test 1</option>
                                <option>Test 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="row justify-content-end">
                            <button class="btn rounded-green"> + </button>
                        </div>
                    </div>
                </div>
                
                <div class="row" style="font-size: 18px">
                    Exclude
                </div>
                <div class="row">
                    {{this.currentContent}} 1 
                </div>
                <div v-if="currentContent != 'RegisterDate'">
                    <input type="text" class="form-control">
                </div>
                <div class="row align-items-center p-3">
                    <div class="col-sm-10">
                        <div v-if="currentContent == 'Name'">
                            <div class="row justify-content-between">
                                <a-checkbox>Only show exact matches</a-checkbox>
                                <a-checkbox>Include similar results</a-checkbox>
                            </div>
                        </div>
                         <div v-else-if="currentContent == 'RegisterDate'">
                            <div class="row justify-content-between align-items-center">
                                <input type="date" class="form-control">
                                <label>from</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div v-else class="row justify-content-center">
                            <select class="form-control">
                                <option selected> {{this.currentContent}} Options </option>
                                <option>Test 1</option>
                                <option>Test 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="row justify-content-end">
                            <button class="btn rounded-green"> + </button>
                        </div>
                    </div>
                </div>
                <div v-if="currentContent == 'Tag'">
                    <div class="row" style="font-size: 18px">
                        Create Tags
                    </div>
                    <div class="row"> 
                        Tag name
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div v-if="currentContent == 'Survey'">
                    <div class="row justify-content-center">
                        <button class="btn m-1 btn-info">SurveySettings</button>
                    </div>
                </div>
                <div v-if="currentContent == 'Source'">
                    <div class="row" style="font-size: 18px">
                        Source
                    </div>
                    <div class="row">
                        Add new source name
                        <input type="text" class="form-control">
                    </div>
                    <div class="row">
                         Add new source URL
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div v-if="currentContent == 'Conversion'">
                    <div class="row justify-content-center">
                        <button class="btn m-1 btn-info">Conversion Settings</button>
                    </div>
                </div>
               
                <div class="footer pt-4">
                    <div class="row justify-content-center">
                        <button class="btn m-1 rounded-red"> Reset </button>
                        <button class="btn m-1 rounded-green" @click="confirm"> Finish/Confirm </button>
                    </div>
                </div>
            </div>
            
        </a-modal>
    </div>
</template>

<script>
export default {
    name: "NewEditMessageTarget",
    props: ["btnClass"],
    data() {
        return {
            tag: {
                name: 'name',
                option: 'option'
            },
            name: {},
            currentContent: "Tag",
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
        confirm() {
            this.visible = false
            console.log(this.name)
            this.$parent.messageTargetData = ['hello'];
        }
    },
}
</script>
