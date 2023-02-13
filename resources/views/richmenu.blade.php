@extends('layouts.app')

@section('content')
    <div id="richmenu" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-9 justify-content-between align-items-center"> 
                    <h2>@{{$t("message.rich_menu")}}</h2>
                </div>
                <div class="col-3 align-items-center">
                    <div class="row justify-content-end align-items-center">
                        <new-richmenu v-bind:btnclass="RoundedDark" :type="'New'" :reload-rich-menu="reloadRichMenu" :rich-menus-data="richMenus" v-model:loading-count="loadingCount"> </new-richmenu>
                    </div>
                </div>
            </div>
            <div class="row px-2 justify-content-between align-items-center mb-2 mb-sm-0">
                <div>
                    <button type="button" class="btn rounded-white mb-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 2 }" @click="currentFilterIndex = 2">@{{$t("message.show_all")}}</button>
                    <button type="button" class="btn rounded-white mb-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 1 }" @click="currentFilterIndex = 1">@{{$t("message.active_menu")}}</button>
                    <button type="button" class="btn rounded-white mb-1" v-bind:class="{ 'active-filter' : currentFilterIndex == 0 }" @click="currentFilterIndex = 0">@{{$t("message.inactive_menu")}}</button>
                </div>
                <button class="btn rounded-red mb-1" :disabled="!selected.length" @click="dialogVisible = true">@{{$t("message.delete_menu")}}</button>
                <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                    <p>@{{ $t('message.confirm_text') }}</p>
                </a-modal>
                {{-- <div class="col-sm-3 text-left">
                    <select class="borderless-input">
                        <option selected>種類で絞る（未実装）</option>
                    </select>
                </div> --}}
            </div>
            <div class="row justify-content-between align-items-center m-1 font-size-table" style="font-size:12px">
                <a-checkbox @change="onAllCheckChanged" :checked="checkAll" class="col-2"></a-checkbox>
                <div class="col-6 text-center sortable" @click="sort($event, 'title')" :class="{ active: sortKey.field == 'title' }">
                    @{{$t("message.title")}}
                    <span class="arrow" :class="sortKey.type"></span>
                </div>
                <div class="col-4 text-center sortable" @click="sort($event, 'created_at')" :class="{ active: sortKey.field == 'created_at' }">
                    @{{$t("message.created_at")}}
                    <span class="arrow" :class="sortKey.type"></span>
                </div>
                <div class="col"></div>
            </div>  
        </div>
        <div class="justify-content-between align-items-center">
            <div class="row">
                <div class="col-sm-4 col-md-4 p-3" v-for="(data,key) in sortedMenus" :key="key">
                    <gridview-richmenus @menu-checked="onChange" :data="data" :reload-rich-menu="reloadRichMenu" :rich-menus-data="richMenus" v-model:loading-count="loadingCount"></gridview-richmenus>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')

<script src="{{asset('js/components/richmenu/gridview-richmenus.js')}}"></script>
<script src="{{asset('js/components/richmenu/newrichmenu.js')}}"></script>
{{-- <script src="{{asset('js/components/richmenu/menu-layout.js')}}"></script> --}}
<script src="{{asset('js/components/richmenu/select-richmenu-layout.js')}}"></script>
<script src="{{asset('js/components/custom-components/messagetarget.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-tags.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-scenario.js')}}"></script>
<script src="{{asset('js/components/custom-components/message-target/messagetarget-registerdate.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-selection.js')}}"></script>
<script src="{{asset('js/components/magazine/custom-components/prepend-message-target-date-selection.js')}}"></script>
<script src="{{asset('js/components/custom-components/loading.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                rich_menu: 'Rich Menu',
                title:'Title',
                delete_menu:'Delete Scenarios',
                status: 'Status',
                subscribed: 'Subscribed',
                delivery_completed: 'Delivery Completed',
                after_delivery: 'After Delivery',
                active_menu: 'Active Menu',
                inactive_menu: 'Inactive Menu',
                show_all: 'Show all',
                title: 'Title',
                created_at: 'Created At',
                cannot_be_empty: 'This field cannot be empty',
                invalid_url: "Please enter a valid URL.",
                not_exist_url_scheme: "Please enter in the format of http / https",
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?'
            }
        },
        ja: {
            message: {
                rich_menu: 'リッチメニュー',
                title: 'タイトル',
                delete_menu: '選択したものを削除',
                status: 'ステータス',
                subscribed: '配信中',
                delivery_completed: '配信完了',
                after_delivery: '配信後',
                active_menu: '配信中のみ表示',
                inactive_menu: '停止中のみ表示',
                show_all: '全て表示',
                title: 'タイトル',
                created_at: '作成日',
                cannot_be_empty: 'このフィールドは必須です',
                invalid_url: "無効なURLです",
                not_exist_url_scheme: "http/httpsの形式でご入力ください",
                confirm_title: '確認',
                confirm_text: '本当に実行していいですか？'
            }
        }
    }

    // Create VueI18n instance with options
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}', // locale form config/app.php 
        messages, // set locale messages
    })

    var richmenu = new Vue({
        i18n,
        el: '#richmenu',
        data: {
            data: [],
            filterData: [],
            selected: [],
            richMenus: [],
            checkAll: false,
            RoundedDark: "btn-outline-dark",
            BtnSuccess: "btn-success",
            sortKey: { field: 'title', type: 'asc' },
            dialogVisible: false,
            confirmLoading: false,
            currentFilterIndex: 2,
            loadingCount: 0
        },
        watch: {
            currentFilterIndex: function () {
                this.filterOption()
            }
        },
        beforeMount() {
            this.reloadRichMenu()
        },
        computed: {
            sortedMenus() {
                let records = this.filterData
                let sortKey = this.sortKey
                records = records.sort(function(a, b) {
                    a = a[sortKey.field]
                    b = b[sortKey.field]
                    var order = sortKey.type == 'asc' ? 1 : -1

                    return (a === b ? 0 : a > b ? 1 : -1) * order
                })
                return records
            }
        },
        methods: {
            filterOption() {
                if (this.currentFilterIndex == 2) {
                    this.filterData = this.data
                } else {
                    this.filterData = this.data.filter(this.filterCheck(this.currentFilterIndex))
                }
            },
            filterCheck(filterIndex) {
                return function(richmenu) {
                    return richmenu.is_active == filterIndex
                }
            },
            reloadRichMenu() {
                this.loadingCount++;
                axios.get('richmenu/list')
                .then(response => {
                    this.data = response.data.richMenuItems
                    this.richMenus = response.data.richMenus
                    this.filterOption()
                    console.log(response.data)
                })
                .finally(() => this.loadingCount--)
            },
            deleteRichMenu() {
                let ids = this.selected.map(menu => {
                    return menu.id
                })
                self = this
                this.loadingCount++;
                axios.delete('richmenu/delete/' + ids)
                .then(function(response){
                    self.selected = []
                    self.reloadRichMenu()
                    self.confirmLoading = false
                    self.handleCancel()
                })
                .finally(() => this.loadingCount--)
            },
            onChange(event) {
                var menu = event.target.value;
                if (event.target.checked == true) {
                    this.selected.push(menu);
                    this.$set(menu, 'checked', true)

                } else {
                    var id = this.selected.indexOf(menu);
                    this.selected.splice(id, 1);
                    this.$set(menu, 'checked', false)
                }
                this.checkAll = false
            },
            onAllCheckChanged(e){
                let checkAll = e.target.checked
                this.selected = []
                this.checkAll = checkAll
                if (checkAll) {
                    this.filterData.forEach(menu => {
                        this.selected.push(menu)
                        this.$set(menu, 'checked', true)
                    });
                } else {
                    this.filterData.forEach(menu => {
                        this.$set(menu, 'checked', false)
                    });
                }
                this.checkAll = checkAll
            },
            sort(event, colField) {
                var sortDirection = this.sortKey.type
                if(colField == this.sortKey.field) {
                    sortDirection = sortDirection == 'asc' ? 'desc' : 'asc'
                } else {
                    sortDirection = 'asc'
                }
                this.sortKey = {field: colField, type: sortDirection}
            },
            handleOk() {
                this.confirmLoading = true
                this.deleteRichMenu()
            },
            handleCancel() {
                this.dialogVisible = false
            }
        }
    });
</script>

<style scoped>
/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  /* transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0); */
  transition: all ease;
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(-10px);
  opacity: 0;
}

.activeViewStatus {
    border: solid 2px black;
    background-color: #e6e8ed;
}

.grid-1 {
    justify-items: center;
}

.grid-1 img {
    max-width: 100%;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 5px;
    align-items: stretch;
    justify-items: center;
}

.grid-2 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-3 {
    display: grid;
    grid-template-rows: 1fr 1fr;
    grid-gap: 5px;
    align-items: stretch;
    justify-items: center;
}

.grid-3 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-4 {
    display: grid;
    grid-template-columns: repeat(1fr, 2);
    grid-gap: 5px;
    align-items: start;
    justify-items: center;
}

.grid-4 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-4 img:nth-child(1) {
    grid-column: span 2;
}

.grid-5 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 5px;
    align-items: center;
    justify-items: center;
}

.grid-5 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-5 img:nth-child(1) {
    grid-column: span 1;
    grid-row: span 2;
}

.grid-6 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 5px;
    align-items: center;
    justify-items: center;
}

.grid-6 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-6 img:nth-child(2) {
    grid-column: span 1;
    grid-row: span 2;
}

.grid-7 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    grid-gap: 5px;
    align-items: start;
    justify-items: center;
}

.grid-7 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-8 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 5px;
    align-items: start;
    justify-items: center;
}

.grid-8 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.grid-8 img:nth-child(1) {
    grid-column: span 3;
}

.grid-9 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    grid-gap: 5px;
    align-items: start;
    justify-items: center;
}

.grid-9 img {
    border: 1px solid #ccc;
    max-width: 100%;
}

.sortable.active .arrow {
    opacity: 1;
}

.arrow {
    display: inline-block;
    vertical-align: middle;
    width: 0;
    height: 0;
    margin-left: 5px;
    opacity: 0;
}

.arrow.asc {
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-bottom: 4px solid rgba(0,0,0,.65);
}

.arrow.desc {
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid rgba(0,0,0,.65);
}

.selected {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
}

</style>

@endsection

