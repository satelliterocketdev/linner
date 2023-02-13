Vue.component ('listview-scenarios', {
    template: 
    `<div class="bg-white rounded border my-1">
        <div class="row justify-content-between align-items-center p-3 wordbreak-all font-size-table">
            <div v-show="canEdit" class="pl-1 pl-sm-3 text-center">
                <input type="checkbox" v-model="selected" :value="data.id" @click="onChangeCheckbox">
            </div>
            <div class="col-3 px-small"> 
                <b>{{ name }}</b>
            </div>
            <div class="col-2 text-center px-small">
                <select class="outline-select" v-model="is_active" @change="updateStatus">
                    <option value="1">{{$t('message.active')}}</option>
                    <option value="0">{{$t('message.inactive')}}</option>
                </select>
            </div>
            <div class="col-2 col-sm-1 text-center px-small">
                {{$t('message.users', [data.sent_count | 0])}}
            </div>
            <div class="col-2 col-sm-1 text-center px-small">
                {{$t('message.users', [data.subscription_count | 0])}}
            </div>
            <div class="col-2 col-sm-1 text-center px-small">
                <div style="font-size:0.7rem;">
                    <span v-if="data.scenario_action">{{ $t('message.scenario') }}</span>
                    <span v-if="data.scenario_action && data.tag_action">/</span>
                    <span v-if="data.tag_action">{{ $t('message.tag') }}</span>
                </div>
            </div>
            <div class="col-sm-3 px-small">
                <div class="row justify-content-end align-items-center pr-3 mt-2 mt-sm-0">
                    <new-scenario v-show="canEdit" v-bind:data="data" :type="'Edit'" :reload-scenario="reloadScenario" v-bind:btnclass="BtnSuccess" v-model:loading-count="loadingCountData"></new-scenario>
                    <!--<edit-scenario v-bind:data="data" :reload-scenario="reloadScenario" v-bind:btnclass="BtnSuccess"></edit-scenario>-->
                    <button v-show="canEdit" class="btn mx-1 mb-1 btn-info small-text font-size-table px-small" @click="dialogVisible = true">{{$t('message.copy')}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                        <p>{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                    <!--<confirmation-test v-show="canEdit" :data="defaults" v-bind:btnclass="BootstrapRed"></confirmation-test>-->
                    <!--<scenario-preview></scenario-preview>-->
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
            copy: 'Copy',
            confirm_title: 'Confirm',
            confirm_text: 'Are you sure?',
            complete: 'Complete',
            users: 'Users {0}',
            tag:'Tag',
            scenario: 'Scenario'
        }
      },
      ja: {
        message: {
            active: '配信中',
            inactive: '停止中',
            copy: '複製',
            confirm_title: '確認',
            confirm_text: '本当に実行していいですか？',
            complete: '配信完了',
            users: '{0} 人',
            tag:'タグ',
            scenario: 'シナリオ'
        }
      }
    }
  },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props:['data', 'reloadScenario', 'selected', 'canEdit', 'loadingCount'],
    data() {
        return {
            defaults: {},
            id: '',
            name: '',
            is_active: 0,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
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