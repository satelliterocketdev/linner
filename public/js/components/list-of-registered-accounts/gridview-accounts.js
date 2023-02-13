Vue.component('gridview-accounts', {
    template:
        `<div class="card w-100 m-0 mx-sm-2">
            <div v-if="checkObject(data)">
                <div class="text-center mt-3">
                    <img :src="data.profile_image" style="width: 150px; height: 150px;" class="rounded-circle mb-2">
                </div>
                <div class="card-body">
                    <h3 class="card-title text-center">{{data.name}}</h3>
                    <div class="row" style="font-size: 12px;">
                        <div class="col-6 col-sm-4 text-center px-1 px-small">
                            <img src="/img/menu/accounticon1.png" width="20px;"><a style="color: #00CCCC;">{{ data.totalRegisteredUsers }}</a>
                            <p>{{$t('message.number_of_registered_people')}}</p>
                        </div>
                        <div class="col-6 col-sm-4 text-center px-1 px-small">
                            <img src="/img/menu/accounticon2.png" width="20px;"><a style="color: #00CCCC;">{{ data.availableFollowers }}</a>
                            <p>{{$t('message.valid_registered_number')}}</p>
                        </div>
                        <div class="col-6 col-sm-4 text-center px-1 px-small">
                            <img src="/img/menu/accounticon3.png" width="20px;"><a style="color: #00CCCC;">{{ data.totalBlockedUsers }}/{{ percentage(data.totalBlockedUsers, data.totalRegisteredUsers) }}%</a>
                            <p>{{$t('message.number_of_blocks_ratio')}}</p>
                        </div>
                    </div>
                    <p class="text-center">{{$t('message.monthly_data')}}</p>
                    <div class="row justify-content-center" style="font-size: 12px;">
                        <div class="col-6 text-center px-small">
                            <a style="color: #00CCCC;">{{ data.monthlyRegisteredUsers }}</a>
                            <p>{{$t('message.new_registration')}}</p>
                        </div>
                        <div class="col-6 text-center px-small">
                            <a style="color: #00CCCC;">{{ data.clickCount }}/{{ data.clickRate }}%</a>
                            <p>{{$t('message.clicks_rate')}}</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <detail v-bind:btnclass="RoundedDark" :data="data" :reload-accounts="reloadAccounts" v-model:loading-count="loadingCountData"></detail>
                    </div>
                </div>
            </div>
            <div v-else class="card-body align-items-center d-flex justify-content-center">
                <add-account :type="'grid'" :reload-accounts="reloadAccounts" v-model:loading-count="loadingCountData"></add-account>
            </div>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    number_of_registered_people: 'number of registered people',
                    valid_registered_number: 'valid registered number',
                    number_of_blocks_ratio: 'number of blocks/ratio',
                    monthly_data: 'monthly data',
                    new_registration: 'new_registration',
                    clicks_rate: 'clicks/rate'
                }
            },
            ja: {
                message: {
                    number_of_registered_people: '登録人数',
                    valid_registered_number: '有効登録人数',
                    number_of_blocks_ratio: 'ブロック数/割合',
                    monthly_data: '月間データ',
                    new_registration: '新規登録人数',
                    clicks_rate: 'クリック数/率'
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'reloadAccounts', 'btnclass', 'loadingCount'],
    data() {
        return {
            RoundedDark: "btn-outline-dark",
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
    },
    methods: {
        percentage(partialValue, totalValue) {
            let n = (100 * partialValue) / totalValue
            if (n !== n) {
                return 0
            }
            return n.toFixed(2);
        },
        checkObject(obj) {
            return Object.keys(obj).length > 0
        }
    }
});