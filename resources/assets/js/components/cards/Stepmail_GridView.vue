<template>
    <div class="col-sm-4 col-md-4 p-3">
        <div class="card h-100 w-100">
            <div class="card-header">            
                <!-- <input type="checkbox" class="mr-2"> -->
                <a-checkbox @change="onChange" :key="data.id"></a-checkbox>
                <div class="row justify-content-center align-items-center">
                    <b>{{ data.name }}</b>
                </div>
                <div class="row justify-content-center align-items-center">
                    <select class="outline-select" v-model="data.status" @change="updateStatus">
                        <option v-for="option in options" v-bind:value="option.value">{{ option.text }}</option>
                    </select>
                </div>
            </div>
            <img src="/images/starryrhone_vangogh_big.jpg" class="fixed-img-container">
            <div class="card-body">
                <div class="row justify-content-center p-2">
                    Users {{ data.users | length }}
                </div>
                <div class="row justify-content-center p-1">
                    <div class="col text-center">
                        {{data.name}}
                    </div>
                    <div class="col text-center">
                        Users
                    </div>
                    <div class="col text-center">
                        {{data.name}}
                    </div>
                </div>
                <div class="row justify-content-center p-1">
                    <div class="col text-center">
                        {{data.name2}}
                    </div>
                    <div class="col text-center">
                        {{ data.users | length }}
                    </div>
                    <div class="col text-center">
                        {{data.name2}}
                    </div>
                </div>
               
                
            </div>
            <div class="card-footer">
                <div class="row justify-content-end">
                    <ScenarioEdit v-bind:btnClass="this.RoundedGreenBtn" v-bind="data"></ScenarioEdit>
                    <button class="btn m-1 rounded-cyan" @click="copyScenario">Copy</button>
                    <button class="btn m-1 rounded-red">Confirm</button>
                    <button class="btn m-1 rounded-grey">Preview</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import  ScenarioEdit from "../modals/Scenario_Edit";
    export default {
        name: "StepmailGridView",
        props:['name','name2',"data", 'delivery_status', 'users'],
        components: {
            ScenarioEdit
        },
        data(){
            return{
                selected: [],
                RoundedGreenBtn:"rounded-green",
                options: [
                    { text: 'Active', value: 1 }, // During Delivery
                    { text: 'Inactive', value: 2 } // Stop Delivery
                ],
            }
        },
        filters: {
            length(value) {
                return 0
            }
        },
        methods: {
            updateStatus() {
                self = this
                window.axios.put('/stepmail/' + this.data.id, this.data)
                .then(function(response){
                    self.$parent.loadScenario()
                })
            },
            copyScenario() {
                self = this
                window.axios.post('/stepmail/copy/', { id: this.data.id })
                .then(function(response){
                    self.$parent.loadScenario()
                })
            },
            onChange(d) {
                self = this
                if (d.target.checked === true) {
                    this.$parent.selected.push(this.data.id)
                } else {
                    const index = this.$parent.selected.indexOf(this.data.id)
                    this.$parent.selected.splice(index, 1);
                }

                this.$parent.disableDelete = (this.$parent.selected.length > 0) ? false : true
            }
        }
    }
</script>

