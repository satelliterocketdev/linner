Vue.component ('gridview-richmenus', {
    template: 
    `<div>
        <div class="card h-100 w-100">
            <div class="card-header">            
                <a-checkbox @change="onChange" :value="data" :checked="data.checked" ></a-checkbox>
                <div class="row justify-content-center align-items-center wordbreak-all">
                    <b>{{ data.title }}</b>
                </div>
                <div class="row justify-content-center align-items-center">
                    <select class="outline-select" v-model="is_active" @change="updateStatus">
                        <option value="1">{{$t('message.active')}}</option>
                        <option value="0">{{$t('message.inactive')}}</option>
                    </select>
                </div>
            </div>
            <div class="grid-1 text-center">
                <img :src="this.images.featured_url">
            </div>
            <div class="card-footer">
                <div class="row justify-content-end align-items-center">
                    <new-richmenu :data="data" :type="'Edit'" :reload-rich-menu="reloadRichMenu" :btnclass="BtnSuccess" :rich-menus-data="richMenusData" v-model:loading-count="loadingCountData"></new-richmenu>
                    <button class="btn mx-1 btn-info small-text" @click="dialogVisible = true">{{$t('message.copy')}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirmLoading="confirmLoading">
                        <p>{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                    <!-- <confirmation-test :data="data.messages" v-bind:btnclass="BootstrapRed"></confirmation-test>-->
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
                    first: 'Delivery method',
                    second: 'Subscribed',
                    third: 'Pause',
                    copy: 'Copy',
                    confirm_title: 'Confirm',
                    confirm_text: 'Are you sure?'
                }
            },
            ja: {
                message: {
                    active: '配信中',
                    inactive: '停止中',
                    users: '配信完了 {0} 人',
                    first: '配信方法',
                    second: '購読中',
                    third: '一時停止',
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
    props: ['data', 'reloadRichMenu', 'richMenusData', 'loadingCount'],
    data() {
        return {
            is_active: this.data.is_active,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            images: JSON.parse(this.data.rich_menu_file),
            gridType: "grid-" + this.data.rich_menu_type,
            dialogVisible: false,
            confirmLoading: false
        }
    },
    watch: {
        data: function () {
            if (this.data) {
                this.images = JSON.parse(this.data.rich_menu_file)
                this.gridType = "grid-" + this.data.rich_menu_type
                this.is_active = this.data.is_active;
            }
        }
    },
    methods: {
        onChange(event) {
            this.$emit('menu-checked', event);
        },
        copyRichMenu() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('richmenu/copy', {
                id: this.data.id 
            })
            .then(function(response){
                self.reloadRichMenu()
                self.confirmLoading = false
                self.handleCancel()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateStatus() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post('richmenu/activity/' + this.data.id, {
                is_active: this.is_active
            })
            .then(function(response){
                self.reloadRichMenu()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        handleOk() {
            this.confirmLoading = true
            this.copyRichMenu()
        },
        handleCancel() {
            this.dialogVisible = false
        },
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