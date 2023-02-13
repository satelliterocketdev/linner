@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div class="rounded bg-white px-4 py-4">
            <div class="row">
                <div class="col-8">
                    <h2>@{{$t("message.template")}}</h2>
                </div>
                <div class="col-4 text-right">
                    @can(App\Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE)
                        <new-template v-bind:btnclass="RoundedDark" :type="'New'" :reload-template="reloadTemplate" v-model:loading-count="loadingCount"> </new-template>
                    @endcan
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end mb-3">
                @can(App\Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE)
                    <button class="btn rounded-red my-2" :disabled="disableDelete" @click="dialogVisible = true">@{{$t("message.delete_templates")}}</button>
                    <a-modal :title="$t('message.confirm_title')" :visible="dialogVisible" @ok="handleOk" @cancel="handleCancel" :confirm-loading="confirmLoading">
                        <p>@{{ $t('message.confirm_text') }}</p>
                    </a-modal>
                @endcan
            </div>
            <div class="row align-items-center font-size-table">
                @can(App\Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE)
                    <div class="col-2 col-lg-1 text-center px-small">
                        <input type="checkbox" :checked="isAllSelected" @click="selectAll" class="m-1">
                    </div>
                @endcan
                <div class="col-6 col-lg-6 px-small" @click="sort($event, 'title')" :class="{ active: sortKey.field == 'title' }">
                    @{{$t("message.title")}} <i class="fas fa-angle-down"></i>
                    <span class="arrow" :class="sortKey.type"></span>
                </div>
                <div class="col-4 col-lg-3 px-small" @click="sort($event, 'created_at')" :class="{ active: sortKey.field == 'created_at' }">
                    @{{$t("message.created_date")}} <i class="fas fa-angle-down"></i>
                    <span class="arrow" :class="sortKey.type"></span>
                </div>
                <div class="col-0 col-lg-2 px-small">&nbsp;</div>
            </div>
        </div>
        <div class="justify-content-between align-items-center">
                <div  v-for="(data,key) in sortedTemplates" :kye="key">
                    <view-templates :reload-template="reloadTemplate" :data="data" :selected="selected" :can-edit="canEdit" v-model:loading-count="loadingCount"></view-templates>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')

<!--Main Pages-->
{{-- <script src="{{asset('js/components/template/new-template.js')}}"></script> --}}
<!--Cards/Lists-->
<script src="{{asset('js/components/template/view-templates.js')}}"></script>
<script src="{{asset('js/components/template/newtemplate.js')}}"></script>
<script src="{{asset('js/components/custom-components/addcontent-main.js')}}"></script>
<script src="{{asset('js/components/custom-components/content-panel/contentpanel-createmessage.js')}}"></script>
<script src="{{asset('js/components/template/custom-components/emoji.js')}}"></script>

<script src="{{asset('js/components/custom-components/add-content/addcontent-image.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-video.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-audio.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-template.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-other.js')}}"></script>
<script src="{{asset('js/components/custom-components/add-content/addcontent-stamp.js')}}"></script>

<script src="{{asset('js/components/custom-components/custom-components/previously-uploaded.js')}}"></script>

<script src="{{asset('js/components/custom-components/add-content/addcontent-template.js')}}"></script>
<script src="{{asset('js/components/custom-components/addcontent-template-card.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                new_templates: 'New Template',
                template: 'Template',
                dropdown: 'Dropdown',
                delete_template: 'Delete template',
                title: 'title',
                created_date: 'created date',
                confirm_title: 'Confirm',
                confirm_text: 'Are you sure?'
            }
        },
        ja: {
            message: {
                new_templates: '新規',
                template: 'テンプレート',
                dropdown: '種類で絞る',
                delete_templates: '選択したものを削除',
                title: 'タイトル',
                created_date: '作成日',
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
    var app = new Vue({
        i18n,
        el: '#app',
        data: {
            loadingCount: 0,
            data: [],
            filterData: [],
            selected: [],
            currentFilter: 'all',
            disableDelete: true,
            RoundedDark: "btn-outline-dark",
            // BtnSuccess: "btn-success",
            activeTemplate: false,
            inactiveTemplate: false,
            isAllSelected: false,
            canEdit: {{ var_export($canEdit) }},
            dialogVisible: false,
            confirmLoading: false,
            sortKey: { field: 'title', type: 'asc' },
        },
        computed: {
            sortedTemplates() {
                var records = this.filterData
                var sortKey = this.sortKey
                records = records.sort(function(a, b) {
                    a = a[sortKey.field]
                    b = b[sortKey.field]
                    var order = sortKey.type == 'asc' ? 1 : -1

                    return (a === b ? 0 : a > b ? 1 : -1) * order
                })
                return records
            }
        },
        beforeMount() {
            this.reloadTemplate()
        },
        methods: {
            selectAll () {
                this.selected = []
                if (this.isAllSelected) {
                    this.isAllSelected = false
                } else {
                    for (var data in this.filterData) {
                        this.selected.push(this.filterData[data].id)
                    }
                    this.isAllSelected = true
                }
                this.disableDelete = (this.selected.length <= 0)
            },
            reloadTemplate() {
                this.loadingCount++
                axios.get("template/lists")
                .then(response => {
                    this.data = response.data
                    this.filterData = this.data
                    console.log(this.data)
                })
                .finally(() => this.loadingCount--)
            },
            filterTemplate(is_active) {
                //This will toggle the active css class for the button
                this.activeTemplate = false
                this.inactiveTemplate = false
                if(is_active == 1) {
                    this.activeTemplate = true;
                } else if(is_active == 0){
                    this.inactiveTemplate = true;
                }
                if (this.currentFilter == is_active) {
                    this.currentFilter = 'all'
                    this.filterData = this.data
                    //this will remove the active css class for the buttons when button is pressed twice
                    this.activeTemplate = false
                    this.inactiveTemplate = false
                    return
                }
                this.currentFilter = is_active
                this.filterData = this.data.filter(function(template){
                    return (template.is_active == is_active)
                })
            },
            deleteTemplate() {
                let self = this
                this.loadingCount++
                axios.delete('template/' + this.selected)
                .then(response => {
                    self.selected = []
                    self.reloadTemplate()
                    self.disableDelete = true
                    self.confirmLoading = false
                    self.handleCancel()
                })
                .finally(() => this.loadingCount--)
            },
            handleOk() {
                this.confirmLoading = true
                this.deleteTemplate()
            },
            handleCancel() {
                this.dialogVisible = false
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
        }
    });
    </script>
@endsection

@section('css-styles')
    <style>
        .btn-success {
            background-color: #1dcd00;
            border-color: #22e200;
        }

        .btn-info {
            background-color: #4AD8FA;
            border-color: #6bdff9;
        }

        .btn-danger {
            background-color: #FF7474;
            border-color: #fc8d8d;
        }

        .btn-secondary {
            background-color: #9b9b9b;
            border-color: #c6c6c6;
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

        .scrollable {
            overflow-y: auto;
            max-height: 200px;
        }
    </style>
@endsection