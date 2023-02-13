Vue.component('receipt-settlement', {
    template:
        `<div>
            <button @click="showModal" v-bind:class="buttonclass">{{$t('message.receipt')}}</button>
            <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null">
                <div class="receipt-frame">
                    <div class="align-items-center">
                        <div class="row">
                            <div class="col-sm-7"><h3 style="margin-left: 20px;">{{$t("message.receipt")}}</h3></div>
                            <div class="col-sm-4 text-right"><h5 style="text-decoration: underline;">{{ data.year }}{{$t('message.year')}}{{ data.month }}{{$t('message.month')}}{{ data.day }}{{$t('message.day')}}</h5></div>
                        </div>
                        <div class="col-sm-4 text-center" style="margin-left:100px;"><h5 style="border-bottom: solid;">{{ data.yen_only }}</h5></div>
                        <h5 style="margin: 20px 0 0 120px;">{{$t("message.as_service_charge")}}</h5>
                        <div class="text-right"><h5 style="margin-right: 50px;">{{$t("message.address_company_name_etc")}}</h5></div>
                    </div>
                </div>
            </a-modal>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    receipt: 'receipt',
                    as_service_charge: 'As service charge',
                    address_company_name_etc: 'Address, company name, etc.',
                    year: 'year',
                    month: 'month',
                    day: 'day',
                }
            },
            ja: {
                message: {
                    receipt: '領収書',
                    as_service_charge: 'サービス利用料として',
                    address_company_name_etc: '住所・会社名等',
                    year: '年',
                    month: '月',
                    day: '日',
                }
            }
        }
    },
    props: ['data', 'btnclass', 'reloadSettlement', 'type'],
    data() {
        return {
            visible: false,
            buttonclass: "btn mx-1 " + this.btnclass,
        }
    },
    methods: {
        showModal() {
            self = this
            this.$confirm({
                title: '注意',
                content: '領収書の発行は一度のみ可能です。発行してもよろしいですか？',
                onOk() {
                    self.visible = true
                },
                onCancel() {}
            });
        },
        handleOk(e) {
            this.visible = false
        },
    },
});

