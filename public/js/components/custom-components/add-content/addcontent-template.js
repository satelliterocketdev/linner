Vue.component ('addcontent-template', {
    template: 
    `<div>
        <button type="button" @click="showModal" class="btn btn-sm btn-outline-info m-2">{{ $t('message.add_template') }}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" width="100%" style="max-width: 1000px" :footer="null">
            <div class="row justify-content-center" style="font-size: 20px">
                {{ $t('message.add_template') }}
            </div>
            <div class="row p-2 p-md-4">
                <div v-for="template in filterTemplates" class="col-sm-4">
                    <addcontent-template-card class="mt-4 card-item" @selectedTemplate="selectTemplate(template)" :data="template"></addcontent-template-card>
                </div>
            </div>
            <div class="footer">
                <div class="row justify-content-center pt-2">
                    <a-pagination
                        class="mb-5"
                        :current="currentPage"
                        :total="total"
                        :page-size="pageSize"
                        @change="change">
                    </a-pagination>
                </div>
                <div class="row justify-content-center pt-2">
                    <button @click="handleOk" class="btn rounded-green">{{ $t('message.finish') }}</button>
                </div>
            </div>
        </a-modal>
    </div>`,
    model: {
        prop : 'loadingCount',
        event : 'input'
    },
    props: ['content', 'loadingCount'],
    i18n: {
        messages: {
            en: { 
                message: { 
                    add_template: 'Add Template',
                    finish: 'Finish/Confirm',
                    getting_data: "Getting Data",
                    fetch_fail: "Fail to fetch data"
                }
            },
            ja: { 
                message: { 
                    add_template: 'テンプレートを選ぶ',
                    finish: '完了',
                    getting_data: "データの取得",
                    fetch_fail: "データの取得に失敗しました。"
                }
            }
        }
    },
    data() {
        return {
            visible: false,
            templates: [],
            filterTemplates: [],
            currentPage: 1,
            total: 1,
            lastPage: 1,
            pageSize: 6
        }
    },
    created() {
        this.reloadTemplate()
    },
    methods: {
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
        handleChange() {
            
        },
        reloadTemplate() {
            this.$emit('input', this.loadingCount + 1)
            axios.get("template/lists")
            .then(response => {
                this.templates = response.data
                this.preparePagination(this.templates)
            }).catch(e => {
                this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail'));
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        preparePagination() {
            let offset = (this.currentPage * 6) - 6
            let until = offset + 6
            if (until > this.templates.length) {
                until = this.templates.length
            }
            this.filterTemplates = this.templates.slice(offset, until)
            this.total = this.templates.length
            this.lastPage = Math.ceil(this.templates.length / this.pageSize)
        },
        openNotificationWithIcon(type, message, desc) {
            this.$notification[type]({
                message: message,
                description: desc,
            });
        },
        change(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page
                this.preparePagination()
            }
        },
        selectTemplate(template) {
            this.$emit('updateFromTemplate', template)
            this.handleOk()
        }
    }
});