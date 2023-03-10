Vue.component ('listview-magazines-many', {
    template: 
    `<div class="rounded border my-1" v-bind:class="{ 'bg-white': [sent_count] == 0, 'bg-secondary': [sent_count] > 0 }">
        <div class="row justify-content-between align-items-center p-3 font-size-table wordbreak-all">
            <div class="col-1 px-small text-center">
                <input v-show="canEdit" type="checkbox" v-model="selected" :value="data.id" @click="onChangeCheckbox">
            </div>
            <div class="col-2 text-center px-small">
                {{ account_name }}
            </div>
            <div v-if="schedule_at == null" class="col-2 col-xl-2 text-center px-small">
                {{ $t('message.non_schedule') }}
            </div>
            <div v-else class="col-2 col-xl-2 text-center px-small">
                {{ schedule_at }}
            </div>
            <div class="col-2 text-center px-small" style="max-height:100px; overflow:hidden;" v-html="content_message"></div>
            <div v-if="target_count === 0" class="col-2 col-xl-1 text-center px-small">
                {{ $t('message.target_all') }}
            </div>
            <div v-else class="col-2 col-xl-1 text-center px-small">
                {{ $t('message.specified') }}
            </div>
            
            <div class="col-2 col-xl-1 text-center px-small">
                {{ $t('message.delivery_count', [sent_count]) }}
            </div>
            <div class="col-1 col-xl-3">
                <div class="row justify-content-end pr-3 mt-2 mt-xl-0 align-items-center">
                    <new-magazine-many v-show="canEdit" v-bind:btnclass="BtnSuccess" :type="'Edit'" :many="'true'" :reload-magazines="reloadMagazines" :data="data" v-model:loading-count="loadingCountData"></new-magazine-many>
                    <!--<button v-show="canEdit" class="btn mx-1 mb-1 btn-info small-text font-size-table" @click="dialogVisible = true">{{$t('message.copy')}}</button>-->
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                        <p>{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                    <button v-show="canEdit" v-bind:class="{ disabled: data.schedule_at === null || processing }" class="btn mx-1 mb-1 btn-danger small-text font-size-table" v-on:click="deleteSchedule()">{{$t('message.cancel_reserve')}}</button>
                    <confirmation-test :data="data"></confirmation-test>
                </div>
            </div>
        </div>
    </div>`,
  i18n: {
    messages: {
      en: {
        message: {
            non_schedule: 'Non Schedule',
            target_all: "Everyone",
            specified: "Specified",
            delivery_count: "{0}",
            copy: 'Copy',
            cancel_reserve: 'Cancel reserve',
            confirm_title: 'Confirm',
            confirm_text: 'Are you sure?'
        }
      },
      ja: {
        message: {
            non_schedule: '??????',
            target_all: "??????",
            specified: "????????????",
            delivery_count: "{0}???",
            copy: '??????',
            cancel_reserve: '?????????????????????',
            confirm_title: '??????',
            confirm_text: '???????????????????????????????????????'
        }
      }
    }
  },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props:['data', 'reloadMagazines', 'selected', 'canEdit', 'loadingCount'],
    data() {
        return {
            defaults: {},
            id: '',
            title: '',
            content_message: '',
            schedule_at: '',
            target_count: 0,
            sent_count: 0,
            is_active: 0,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            processing: false,
            dialogVisible: false,
            confirmLoading: false
        }
    },
    methods: {
        render() {
            this.id = this.data.id
            this.content_message = this.data.content_message
            this.account_name = this.data.account.name
            this.schedule_at = this.data.schedule_at
            this.target_count = this.data.target_count
            this.sent_count = this.data.sent_count
            this.is_active = this.data.is_active
        },
        copyMagazine() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('magazine/copy', { id: this.data.id })
            .then(function(response){
                self.reloadMagazines()
                self.confirmLoading = false
                self.handleCancel()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateStatus() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put('/magazine/' + this.id, {
                is_active: this.is_active
            })
            .then(function(response){
                self.reloadMagazines()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
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
        deleteSchedule() {
            if (this.schedule_at !== null && !this.processing) {
                this.processing = true
                self = this
                this.$emit('input', this.loadingCount + 1)
                axios.post('magazine/schedule', {
                    id: this.id,
                    schedule_at: null
                })
                .then(function(response){
                    self.reloadMagazines()
                    self.visible = false
                    self.processing = false
                })
                .finally(() => this.$emit('input', this.loadingCount - 1))
            }
        },
        handleOk() {
            this.confirmLoading = true
            this.copyMagazine()
        },
        handleCancel() {
            this.dialogVisible = false
        }
    },
    filters: {
        count() {
            return 0
        },
        truncate(text, stop, clamp) {
            return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
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