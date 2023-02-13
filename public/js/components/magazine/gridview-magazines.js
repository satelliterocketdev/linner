Vue.component('gridview-magazines', {
    template:
    `<div>
        <div class="card h-100 w-100 wordbreak-all" v-bind:class="{ 'bg-secondary': [sent_count] > 0 }">
            <div class="card-header">
                <input v-show="canEdit" type="checkbox" v-model="selected" :value="data.id" @click="onChangeCheckbox">
                <div class="row justify-content-center align-items-center">
                    <b>{{ title }}</b>
                </div>
                <div v-if="schedule_at == null" class="row justify-content-center align-items-center">
                    {{ $t('message.non_schedule') }}
                </div>
                <div v-else class="row justify-content-center align-items-center">
                    {{ schedule_at }}
                </div>
            </div>
            <!-- <img :src="src" class="fixed-img-container">-->
            <div class="card-body">
                <div class="row">
                    <div v-if="content_type == 'magazine'" class="col mb-3" style="max-height:100px; overflow:hidden;" v-html="content_message"></div>
                    <div v-else class="col mb-3" style="max-height:100px; overflow:hidden;" v-html="notification_message"></div>
                </div>
                <div class="row justify-content-center p-1">
                    <div v-if="target_count === 0" class="col text-center">
                        {{ $t('message.target_all') }}
                    </div>
                    <div v-else class="col text-center">
                        {{ $t('message.specified') }}
                    </div>
                    <div class="col text-center">
                        {{ $t('message.delivery_count', [sent_count]) }}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-end align-items-center">
                    <new-magazine v-show="canEdit" v-bind:btnclass="BtnSuccess" :type="'Edit'" :reload-magazines="reloadMagazines" :data="data" v-model:loading-count="loadingCountData"></new-magazine>
                    <!-- <magazine-new v-bind:data="data" :type="'Edit'" :reload-magazines="reloadMagazines" v-bind:btnclass="BtnSuccess"></magazine-new>-->
                    <button v-show="canEdit" class="btn mx-1 btn-info small-text font-size-table" @click="dialogVisible = true">{{$t('message.copy')}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                        <p>{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                    <button v-show="canEdit" v-bind:class="{ disabled: data.schedule_at === null || processing }" class="btn mx-1 btn-danger small-text font-size-table" v-on:click="deleteSchedule()">{{$t('message.cancel_reserve')}}</button>
                    <confirmation-test :data="data"></confirmation-test>
                </div>
            </div>
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
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
                    non_schedule: 'なし',
                    target_all: "全員",
                    specified: "指定あり",
                    delivery_count: "{0}人",
                    copy: '複製',
                    cancel_reserve: '予約キャンセル',
                    confirm_title: '確認',
                    confirm_text: '本当に実行していいですか？'

                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
    },
    props: ['data', 'reloadMagazines', 'selected', 'canEdit', 'loadingCount'],
    data() {
        return {
            defaults: {},
            id: '',
            title: '',
            /** @type {string} */
            content_type: '',
            content_message: '',
            /** アンケートの通知文面 @type {stirng} */
            notification_message: '',
            schedule_at: '',
            target_count: 0,
            sent_count: 0,
            is_active: 0,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            dialogVisible: false,
            confirmLoading: false,
            processing: false
        }
    },
    methods: {
        render() {
            this.id = this.data.id
            this.title = this.data.title
            this.content_type = this.data.content_type
            this.content_message = this.data.content_message
            if ((this.content_type == 'survey') && (this.data.surveyQuestionnaire != undefined) && (this.data.surveyQuestionnaire != null)) {
                this.notification_message = this.data.surveyQuestionnaire.notification_message
            } else {
                this.notification_message = ''
            }
            this.schedule_at = this.data.schedule_at
            this.target_count = this.data.target_count
            this.sent_count = this.data.sent_count
            this.is_active = this.data.is_active
        },
        copyMagazine() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('magazine/copy', { id: this.data.id })
                .then(function (response) {
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
                .then(function (response) {
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
                    .then(function (response) {
                        self.reloadMagazines()
                        self.visible = false
                        self.processing = false
                    })
                    .finally(() => this.$emit('input', this.loadingCount - 1))
            }
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
