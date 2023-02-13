Vue.component ('gridview-scenarios', {
    template:
    `<div>
        <div class="card h-100 w-100 wordbreak-all">
            <div class="card-header">
                <input v-show="canEdit" type="checkbox" v-model="selected" :value="id" @click="onChangeCheckbox">
                <div class="row justify-content-center align-items-center">
                    <b>{{ name }}</b>
                </div>
                <div class="row justify-content-center align-items-center">
                    <select class="outline-select" v-model="is_active" @change="updateStatus">
                        <option value="1">{{$t('message.active')}}</option>
                        <option value="0">{{$t('message.inactive')}}</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center p-2">
                    {{$t('message.users', [data.sent_count | 0])}}
                </div>
                <div class="row justify-content-center p-1">
                    <div class="col text-center">
                        {{$t('message.subscription')}}
                    </div>
                    <div class="col text-center">
                        {{$t('message.after_delivery_aciton')}}
                    </div>
                </div>
                <div class="row justify-content-center p-1">
                    <div class="col text-center">
                        {{ data.subscription_count }} {{$t('message.count')}}
                    </div>
                    <div class="col text-center">
                        <div style="font-size:0.7rem;">
                            <span v-if="data.scenario_action">{{ $t('message.scenario') }}</span>
                            <span v-if="data.scenario_action && data.tag_action">/</span>
                            <span v-if="data.tag_action">{{ $t('message.tag') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-end align-items-center">
                    <new-scenario v-show="canEdit" v-bind:data="data" :type="'Edit'" :reload-scenario="reloadScenario" v-bind:btnclass="BtnSuccess" v-model:loading-count="loadingCountData"></new-scenario>
                    <button v-show="canEdit" class="btn mx-1 mb-1 btn-info small-text font-size-table px-small" @click="dialogVisible = true">{{$t('message.copy')}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                        <p>{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                    <!--<confirmation-test v-show="canEdit" :data="data.messages" v-bind:btnclass="BootstrapRed"></confirmation-test>-->
                    <!--<scenario-preview :data="preview"></scenario-preview>-->
                </div>
            </div>
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    active: 'Active',
                    inactive: 'Inactive',
                    users: 'Users {0}',
                    subscription: 'Subscribed',
                    after_delivery_aciton: 'After delivery',
                    copy: 'Copy',
                    confirm_title: 'Confirm',
                    confirm_text: 'Are you sure?',
                    count: '',
                    tag:'Tag',
                    scenario: 'Scenario'
                }
            },
            ja: {
                message: {
                    active: '配信中',
                    inactive: '停止中',
                    users: '配信完了 {0} 人',
                    subscription: '購読中',
                    after_delivery_aciton: '配信後',
                    copy: '複製',
                    confirm_title: '確認',
                    confirm_text: '本当に実行していいですか？',
                    count: '人',
                    tag: 'タグ',
                    scenario: 'シナリオ'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'reloadScenario', 'selected', 'canEdit', 'loadingCount'],
    data() {
        return {
            defaults: {},
            id: '',
            name: '',
            is_active: 0,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            preview: this.data.messages[0],
            dialogVisible: false,
            confirmLoading: false
        }
    },
    methods: {
        render() {
            this.id = this.data.id
            this.name = this.data.name
            this.is_active = this.data.is_active
        },
        onChangeCheckbox(d) {
            if (d.target.checked === true) {
                this.$parent.selected.push(this.data.id)
            } else {
                const index = this.$parent.selected.indexOf(this.data.id)
                this.$parent.selected.splice(index, 1);
            }

            this.$parent.disableDelete = (this.$parent.selected.length > 0) ? false : true
        },
        copyScenario() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('stepmail/copy', {
                id: this.data.id,
                message: JSON.stringify(this.data.messages)
            }).then(function(response){
                self.reloadScenario()
                self.confirmLoading = false
                self.handleCancel()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateStatus() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('stepmail/activity', {
                id: this.id,
                is_active: this.is_active
            })
            .then(function(response){
                self.reloadScenario()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        handleOk() {
            this.confirmLoading = true
            this.copyScenario()
        },
        handleCancel() {
            this.dialogVisible = false
        }
    },
    filters: {
        count() {
            return 0
        }
    },
    created() {
        this.render()
    },
    updated() {
        this.render()
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
    }
});
