Vue.component('view-templates', {
    template:
        `<div class="bg-white rounder py-2 mt-3 row w-100 mx-auto align-items-center font-size-table">
            <div v-show="canEdit" class="col-2 col-lg-1 px-small text-center">
                <input type="checkbox" v-model="selected" :value="data.id" @click="onChangeCheckbox">
            </div>
            <div class="col-6 col-lg-6 px-small wordbreak-all">{{ data.title }}</div>
            <div class="col-4 col-lg-3 px-small wordbreak-all">{{ data.created_at }}</div>
            <div class="col-12 col-lg-2 px-small d-flex justify-content-end mt-2 mt-lg-0">
                <new-template v-show="canEdit" v-bind:btnclass="BtnSuccess" :type="'Edit'" :reload-template="reloadTemplate" :data="data" v-model:loading-count="loadingCountData"> </new-template>
                <button v-show="canEdit" style="font-size:12px" class="btn btn-sm btn-info mx-1 font-size-table link-pointer" @click="dialogVisible = true">{{ $t("message.copy") }}</button>
                <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                    <p>{{ $t('message.confirm_text') }}</p>
                </a-modal>
            </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    edit: 'Edit',
                    copy: 'Copy',
                    confirm_title: 'Confirm',
                    confirm_text: 'Are you sure?'
                }
            },
            ja: {
                message: {
                    edit: '編集',
                    copy: '複製',
                    confirm_title: '確認',
                    confirm_text: '本当に実行していいですか？'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'reloadTemplate', 'selected', 'canEdit', 'loadingCount'],
    data() {
        return {
            BtnSuccess: "btn-success",
            dialogVisible: false,
            confirmLoading: false
        }
    },
    methods: {
        render() {
            this.title = this.data.title
            this.created_at = this.data.created_at
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
        copyTemplate() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('template/copy', { id: this.data.id })
            .then(function (response) {
                self.reloadTemplate()
                self.confirmLoading = false
                self.handleCancel()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        handleOk() {
            this.confirmLoading = true
            this.copyTemplate()
        },
        handleCancel() {
            this.dialogVisible = false
        }
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