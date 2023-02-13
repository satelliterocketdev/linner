@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <new-conversion ref="editor" @completion="reloadConversion" v-model:loading-count="loadingCount"></new-conversion>
        <div class="bg-white shadow border rounded p-3">
            <div class="row no-gutters align-items-center ">
                <div class="col-9 justify-content-between align-items-center"> 
                    <h2>@{{ $t('message.conversion') }}</h2>
                </div>
                <div class="col-3 justify-content-end text-right">
                    <button @click="createConversion" class="btn btn-outline-dark mx-1">@{{$t('message.create')}}</button>
                </div>
            </div>
            <div class="row p-2 justify-content-between align-items-center btn-group-toggle" data-toggle="buttons">
                <div class="px-1">
                    <button type="button" class="btn rounded-white m-1" :class="{ 'active-filter' : filterNumber == -1 }" @click="filterConversion(-1)">@{{ $t('message.all_conversions') }}</button>
                    <button type="button" class="btn rounded-white m-1" :class="{ 'active-filter' : filterNumber == 1 }" @click="filterConversion(1)">@{{ $t('message.active_conversions') }}</button>
                    <button type="button" class="btn rounded-white m-1" :class="{ 'active-filter' : filterNumber == 0 }" @click="filterConversion(0)">@{{ $t('message.inactive_conversions') }}</button>
                </div>
                <div class="px-1">
                    <div class="row justify-content-end align-items-center mx-0">
                        <button class="btn rounded-cyan m-1 px-4" :disabled="!hasSelected" @click="startConversion">@{{ $t('message.enable_conversions') }}</button>
                        <button class="btn rounded-red m-1 px-4" :disabled="!hasSelected" @click="stopConversion">@{{ $t('message.disable_conversions') }}</button>
                    </div>
                </div>
            </div>
            <div class="row no-gutters justify-content-between align-items-center small-text font-size-table">
                <div class="col-1">
                    <a-checkbox @change="changedAllCheck" :checked="isAllSelected"></a-checkbox>
                </div>

                <div class='col-2 header-colmun-sortable' @click="sort($event, 'title')"
                    :class="{ active: sortKey.field == 'title' }">
                    @{{ $t('message.title') }}
                    <span class="arrow" :class="sortDirection"></span>
                </div>

                <div class="col-2 col-sm-1 text-center">
                    <span>@{{ $t('message.status') }}</span>
                </div>
                <div class="col-2 text-center">
                    <span>@{{ $t('message.actions') }}</span>
                </div>
                <div class="col-1 text-center">
                    <span>@{{ $t('message.people') }}</span>
                </div>
                <div class="col-2 text-center">
                    <span>@{{ $t('message.url') }}</span>
                </div>
                <div class="col-2 text-center">
                    <span>@{{ $t('message.measurement_tag') }}</span>
                </div>
                <div class="col-sm-1 text-center pc">
                    <span>&nbsp;</span>
                </div>
            </div>  
        </div>
        <div class="justify-content-between align-items-center my-3">
            <div>
                <div v-for="(data,key) in processedRecords" :key="key">
                    <conversion-entrylist :conv="data" @row-check-changed="changedRowCheckbox" >
                        <template slot="editButton" slot-scope="slotProps">
                            <button @click="editConversion(slotProps.conversion)" class="btn btn-success mx-1 small-text">@{{$t('message.edit')}}</button>
                        </template>
                    </conversion-entrylist>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/vue2-filters/dist/vue2-filters.min.js"></script>

<!-- magazine -->
<script src="{{ asset('js/components/magazine/custom-components/prepend-message-target-selection.js') }}"></script>
<script src="{{ asset('js/components/magazine/message-action/messageaction-tag.js') }}"></script>
<script src="{{ asset('js/components/magazine/message-action/messageaction-scenario.js') }}"></script>

<script src="{{asset('js/components/conversion/action-modal.js')}}"></script>
<script src="{{asset('js/components/conversion/newconversion.js')}}"></script>

<script src="{{asset('js/components/conversion/custom-components/conversion-entrylist.js')}}"></script>

<script>
Vue.config.devtools = true

    const messages = {
        en: {
            message: {
                conversion: 'Conversion',
                all_conversions: 'All Conversions',
                active_conversions: 'Active Conversions',
                inactive_conversions: 'Inactive Conversions',
                enable_conversions:'Enable Conversions',
                disable_conversions:'Disable Conversions',
                title: 'Title',
                status: 'Status',
                actions: 'Actions',
                people: 'Amount of People',
                measurement_tag: 'Measurement',
                url: 'URL',
                create: 'Create',
                edit: 'Edit',
            }
        },
        ja: {
            message: {
                conversion: 'コンバージョン',
                all_conversions: '全て表示',
                active_conversions: '有効のみ表示',
                inactive_conversions: '無効のみ表示',
                enable_conversions:'有効化',
                disable_conversions:'無効化',
                title: 'タイトル',
                status: 'ステータス',
                actions: 'アクション',
                people: '人数',
                measurement_tag: '計測用タグ',
                url: 'URL',
                create: '新規',
                edit: '編集',
            }
        }
    }

    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}', 
        messages,
    })

    var app = new Vue({
        i18n,
        el: '#app',
        data: {
            loadingCount: 0,
            data: [],
            filterData: [],
            selected: [],
            isAllSelected: false,

            hasSelected: false,
            sortKey: {field: 'created_at', type: 'desc',},
            sortDirection: 'asc',
            filterNumber: -1,
        },
        watch: {
            data:{
                handler(){
                    this.filterData = _.cloneDeep(this.data);
                }
            },
            selected:{
                handler(){
                    this.hasSelected = this.selected.length > 0;
                }
            },
        },
        computed: {
            processedRecords(){
                var records = this.filterData;

                var filtering = this.filterNumber;
                if(filtering >= 0 ){
                    if(filtering == 0 || filtering == 1){
                        records = records.filter((conv) => {
                            return (conv.is_active == filtering)
                        });
                    }
                }

                if (Object.keys(this.sortKey).length) {
                    var sortKey = this.sortKey
                    records = records.sort(function(a,b){
                    a = a[sortKey.field]
                    b = b[sortKey.field]
                    var order = sortKey.type == 'asc' ? 1 : -1

                    return (a === b ? 0 : a > b ? 1 : -1) * order
                    })
                }
                if(filtering >= 0){
                    this.filterData = records;
                }
                return records;
            }
        },
        methods:{
            openNotificationWithIcon(type, message, desc) {
                this.$notification[type]({
                    message: message,
                    description: desc,
                });
            },
            changedRowCheckbox(event) {
                var conv = event.target.value;
                if (event.target.checked == true) {
                    this.selected.push(conv);
                    this.$set(conv, 'checked', true)
                } else {
                    var id = this.selected.indexOf(conv);
                    this.selected.splice(id, 1);
                    this.$set(conv, 'checked', false)
                }
                this.$emit('change-checked', this.selected)
                this.isAllSelected = false
            },
            changedAllCheck (e) {
                this.isAllSelected = e.target.checked;
                this.selected = [];
                if (this.isAllSelected) {
                    // 全件選択
                    this.filterData.forEach( conv => {
                        this.selected.push(conv);
                        this.$set(conv, 'checked', true)
                    });
                } else {
                    this.filterData.forEach( conv => {
                        this.$set(conv, 'checked', false)
                    });
                }
                this.$emit('change-checked', this.selected)
            },
            // sort関連
            inverseSortType(type){
                if (type === 'asc') return 'desc';
                return 'asc';
            },
            sort(event, colField){
                if(colField == this.sortKey.field){
                this.sortDirection = this.inverseSortType(this.sortDirection);
                } else {
                this.sortDirection = 'asc'
                }

                this.sortKey = {field: colField, type: this.sortDirection,}
            },
            filterConversion(is_active) {
                this.filterNumber = is_active;

                // 選択済み解除
                this.selected = [];
                this.filterData = _.cloneDeep(this.data);
            },
            reloadConversion() {
                this.loadingCount++
                axios.get("conversion/lists")
                .then(response => {
                    this.data = response.data
                })
                .finally(() => this.loadingCount--)
            },
            stopConversion() {
                self = this
                this.loadingCount++
                axios.post('conversion/stop' , { ids: this.selected.map((s)=> s.id )})
                .then(function(response){
                    self.selected = []
                    self.reloadConversion()
                })
                .finally(() => this.loadingCount--)
            },
            startConversion() {
                self = this
                this.loadingCount++
                axios.post('conversion/start', { ids: this.selected.map((s)=> s.id )})
                .then(function(response){
                    self.selected = []
                    self.reloadConversion()
                })
                .finally(() => this.loadingCount--)
            },
            createConversion(){
                this.$refs.editor.showModal();
            },
            editConversion(data){
                this.$refs.editor.showModal(data.id);
            }
        },
        beforeMount: function() {
            this.reloadConversion()
        }
    });
</script>

<style>
.active-action {
    background-color: blue;
}

.header-colmun-sortable.active {
    background-color: #f0f0f0;
}

.header-colmun-sortable.active .arrow {
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

.header-colmun-sortable {
    color: var(--real-blue) !important;
}

.delete-button {
  background-color: #9b9b9b;
  border-color: #c6c6c6;
  border-radius: 50px;
  color: white;
  font-size: 12px;
}

@media (max-width: 576px) {
    input[type="text"] {
        font-size: 0.4em !important;
        line-height: 1.2 !important;
        padding: .15rem .4rem;
    }
}

</style>
@endsection