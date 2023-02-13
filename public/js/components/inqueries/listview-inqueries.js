Vue.component('listview-inqueries', {
    template:
    `<div class="bg-white rounder py-2 mt-3 d-flex align-items-center font-size-table">
        <div class="col-6 col-sm-8" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ data.body }}</div>
        <div class="col-3 col-sm-2 text-right">{{ data.answer == null ? '回答なし' : '回答あり' }}</div>
        <div class="col-3 col-sm-2 d-flex justify-content-end">
            <detail-inqueries v-bind:btnclass="RoundedDark" :data="data" :reload-inqueries="reloadInqueries"></detail-inqueries>
        </div>
    </div>`,
    props: ['data', 'reloadInqueries'],
    data() {
        return {
            RoundedDark: "btn-outline-dark",
        }
    },
});