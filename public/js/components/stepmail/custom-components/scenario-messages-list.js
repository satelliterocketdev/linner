Vue.component ('scenario-messages-list', {
    template:
    `<div>
    <div v-for="(message, key) in data" class="d-flex justify-content-between align-items-center my-1" :key="key">
        <div class="col-1 px-small">
            <a-checkbox @change="onChange(key)"> </a-checkbox>
        </div>
        <div class="col-2 border rounded shadow p-1 text-center d-flex align-items-center mr-2 px-small font-size-table wordbreak-all" style="min-height: 70px" v-bind:class="message.is_active == 1 ? 'bg-white' : 'bg-secondary'">
            <div class="m-auto" v-if="message.schedule_type == 0" >
                登録直後
            </div>
            <div class="m-auto" v-else-if="message.schedule_type == 1">
                登録から{{ message.time_after }}日後{{ message.schedule_time }}
            </div>
            <div class="m-auto" v-else-if="message.schedule_type == 2">
                {{ message.time_after | convertStringToJapaneseRemainingDate }}
            </div>
            <div class="m-auto" v-else-if="message.schedule_type == 3">
                {{ message.schedule_date }}<br>
                {{ message.schedule_time }}
            </div>
        </div>
        <div class="col-8 border rounded shadow py-2" v-bind:class="message.is_active == 1 ? 'bg-white' : 'bg-secondary'">
            <div class="col-sm-4 px-small font-size-table mb-2 mb-sm-0">
                <span>{{ message.title }}</span>
            </div>
            <div class="col-sm-8 px-small">
                <div class="d-flex justify-content-between font-size-table">
                    <select class="outline-select" v-model="message.is_active">
                        <option value="1">{{ $t('message.active') }}</option>
                        <option value="0">{{ $t('message.inactive') }}</option>
                    </select>
                    <new-message v-bind:btnclass="RoundedDark" :type="type" :data="message" :all="data" v-model:loading-count="loadingCountData"></new-message>
                    <confirmation-test v-bind:btnclass="RoundedDark" :data="message"></confirmation-test>
                    <!-- <button type="button" class="btn btn-secondary small-text">Preview</button> -->
                    <!--<scenario-preview v-bind:btnclass="RoundedDark" :data="message"></scenario-preview>-->
                </div>
            </div>
        </div>
    </div>
    </div>`,
    props: ['data', 'buttonclass', 'selected', 'loadingCount'],
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    active: 'Active',
                    inactive: 'Inactive'
                }
            },
            ja: {
                message: {
                    active: '有効',
                    inactive: '無効'
                }
            }
        }
    },

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
    computed: {
        loadingCountData: {
            get() {
                return this.loadingCount
            },
            set(val) {
                this.$emit('input', val)
            }
        }
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
        }
    },
    filters: {
        convertStringToJapaneseRemainingDate(time)
        {
            let result = time.replace(':', '時')
            return result + '分後'
        }
    }
});
