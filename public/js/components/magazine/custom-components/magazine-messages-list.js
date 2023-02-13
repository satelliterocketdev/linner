Vue.component ('magazine-messages-list', {
    template:
    `<div>
    <div v-for="(message, key) in data" class="d-flex justify-content-between align-items-center m-1" :key="key">
        <div class="col-sm-1">
            <a-checkbox @change="onChange(key)"> </a-checkbox>
        </div>
        <div class="col-sm-1 border rounded shadow bg-white px-3 py-2">
            <div class="d-flex justify-content-center">
                {{ message | schedule }}
            </div>
            <div class="d-flex justify-content-center">
                {{ (key!=0) ? key : '' }}
            </div>
        </div>
        <div class="col-sm-10 px-3 py-2">
            <div class="row d-flex justify-content-between align-items-center border rounded shadow bg-white px-3 py-2">
                <div class="col-sm-9">
                    <span>{{ message.title }}</span>
                </div>
                <div class="col-sm-3 text-center">
                    <select class="outline-select" v-model="message.is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between">
                        <new-message v-bind:btnclass="RoundedDark" :type="type" :data="message" :all="data"></new-message>
                        <confirmation-test v-bind:btnclass="" :data="message"></confirmation-test>
                        <!-- <button type="button" class="btn btn-secondary small-text">Preview</button> -->
                        <scenario-preview v-bind:btnclass="RoundedDark" :data="message"></scenario-preview>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>`,
    props: ['data', 'buttonclass', 'selected'],
    data() {
        return {
            checked: false,
            type: 'Edit',
            RoundedWhite: "rounded-white",
            RoundedDark: "btn-outline-dark",
            BtnSuccess: "btn-success small-text",
        }
    },
    created() {

    },
    methods: {
        onChange(pos) {
            // remove from selected messages
            if ($.inArray(pos, this.selected) >= 0) {
                index = this.selected.indexOf(pos)
                this.selected.splice(index, 1)
                return
            }

            // // add from selected messages
            this.selected.push(pos)
        },
    },
    filters: {
        schedule(value) {
            return moment(value.schedule_date).format('HH:mm')
        }
    }
});
