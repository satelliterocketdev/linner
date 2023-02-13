Vue.component ('messageaction-menu', {
    template: 
    `<div>
    <div class="row" style="font-size: 18px"> Scenario set </div>
    <div v-for="(serve, key) in defaults.menus.serves">
        <div class="row p-1"> Tag set</div>
        <prepend-message-target-selection :values="serve.value"></prepend-message-target-selection>
        <div class="row p-1">
            <a-select
                mode="multiple"
                style="width: 100%"
                v-model="serve.value"
            >
                <a-select-option v-for="(menu, menuKey) in menus" :key="menu.title">
                    {{ menu.title }}
                </a-select-option>
            </a-select>
        </div>
        <div class="row justify-content-between align-items-center p-1">
            <select class="custom-select" style="width: 75%" v-model="serve.option">
                <option value="first">Add above menu</option>
                <option value="second">Remove above menu</option>
            </select>
            <div>
                <button class="btn rounded-green" @click="addServe">+</button>
                <button v-if="key!=0" class="btn rounded-red" @click="deleteServe(serve)">-</button>
            </div>
        </div>

        <div class="row align-items-center p-1">
            <a-checkbox :disabled="serve.option == 'second'"><span class="ml-2">Show only the next time</span></a-checkbox>
        </div>

        <div class="row justify-content-center align-items-center p-1">
            <div class="col-sm-9">
                <a-date-picker v-model="serve.period.from.date" placeholder="Start Date" :disabled="serve.option == 'second'" />
                <a-time-picker v-model="serve.period.from.time" placeholder="Start Time" :disabled="serve.option == 'second'" />
                <span class="m-1">from</span>
            </div>
        </div>
        <div class="row justify-content-center align-items-center p-1">
            <div class="col-sm-9">
                <a-date-picker v-model="serve.period.to.date" placeholder="Start Date" :disabled="serve.option == 'second'" />
                <a-time-picker v-model="serve.period.to.time" placeholder="Start Time" :disabled="serve.option == 'second'" />
                <span class="m-1">to</span>
            </div>
        </div>
        <div class="row justify-content-center p-1">
            <button class="btn btn-info">Menu Settings</button>
        </div>
    </div>
    </div>`,
    props: ['data'],
    data() {
        return {
            isActive: false,
            defaults: {
                menus: {
                    serves: [{
                        value: [],
                        option: 'first',
                        isPeriod: true,
                        period: {
                            from: {
                                date: '',
                                time: '',
                            },
                            to: {
                                date: '',
                                time: '',
                            }
                        }
                    }],
                }
            },
            menus: []
        }
    },
    created() {
        // this.reloadTag()
        if (this.data.menus) {
            Object.assign(this.defaults, this.data)
        } else {
            Object.assign(this.data, this.defaults)
        }
    },
    methods: {
        addServe() {
            this.defaults.menus.serves.push({
                value: [],
                option: 'first',
                period: {
                    from: {
                        date: '',
                        time: '',
                    },
                    to: {
                        date: '',
                        time: '',
                    }
                }
            })
        },
        deleteServe(serve) {
            const index = this.data.menus.serves.indexOf(serve)
            this.data.menus.serves.splice(index, 1)
        },
        reloadTag() {
            self = this
            axios.get('menu/lists')
            .then(function(response){
                self.menus = []
                response.data.forEach(function(value, index){
                    self.menus.push({
                        id: value.id,
                        title: value.title,
                    })
                })
            })
        }
    }
});