Vue.component('listview-settlement', {
    template:
        `<div class="bg-white col-sm-12 rounder py-2 px-0 mt-2 d-flex align-items-center font-size-table">
            <p class="col-3 text-center wordbreak-all px-small">{{ data.settlement_at }}</p>
            <p class="col-3 text-center wordbreak-all px-small">{{ data.plan_level }}</p>
            <p class="col-3 text-center wordbreak-all px-small">{{ data.yen_only }}</p>
            <div class="col-3 text-center px-small">
                <generate-receipt v-bind:btnclass="RoundedDark" :data="data" :reload-settlement="reloadSettlement" class="text-left"></generate-receipt>
            </div>
        </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    yen: 'yen',
                    year: 'year',
                    Monthly: 'month',
                }
            },
            ja: {
                message: {
                    yen: '円',
                    year: '年',
                    Monthly: '月度',
                }
            }
        }
    },
    props: ['data', 'reloadSettlement'],
    data() {
        return {
            RoundedDark: "btn-outline-dark",
        }
    },
});
