Vue.component('listview-allfriendslist', {
    template:
    `<div class="bg-white rounder py-2 mt-3 d-flex align-items-center">
        <div class="col-1">
            <a-checkbox></a-checkbox>
        </div>
        <div class="col-1">
            <img src="/img/user-admin.png" width="50px;">
        </div>
        <div class="col-2" style="border-right: solid 2px;">
            ユーザー名
        </div>
        <div class="col-2">2019-09-20</div>
        <div class="col-3">アフィリエイターA</div>
        <div class="col-3">account1</div>
    </div>`,
    props:['data', 'processedRecords'],
    data() {
        return {
            followers:[],
        }
    },
});