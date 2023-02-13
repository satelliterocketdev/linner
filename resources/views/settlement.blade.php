@extends('layouts.app')

@section('content')
    <div id="app" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div v-if="latest_data != null" class="bg-white rounded py-3">
            <div class="d-flex align-items-center justify-content-between px-3 mb-3">
                <div class="col-sm-12 justify-content-between align-items-center">
                    <h2>@{{$t("message.settlement_information")}}</h2>
                    <p class="failed_text" v-if="latest_data.status == 2">@{{$t("message.fail_message")}}</p>
                </div>
            </div>
            <div class="col-sm-12 align-items-center">
                <table class="col-12 col-sm-6 table">
                    <tr>
                        <th>@{{ $t("message.settlement_date_latest") }}</th>
                        <td>@{{ latest_data.created_at }}</td>
                    </tr>
                    <tr>
                        <th>@{{ $t("message.settlement_date_next") }}</th>
                        <td>@{{ latest_data.next_date }}</td>
                    </tr>
                    <tr>
                        <th>@{{$t("message.select_plan")}}</th>
                        <td>@{{ latest_data.plan_level }}</td>
                    </tr>
                    <tr>
                        <th>@{{$t("message.amount")}}</th>
                        <td>¥@{{ latest_data.amount | number_format }}</td>
                     </tr>
                </table>
                <ul>
                    {{-- TODO: 決済代行より連携した情報を表示させる --}}
                    {{-- <li>プランA（仮）</li>
                    <li>カード情報</li> --}}
                    {{-- <p class="card-information text-center">カード情報（仮）</p> --}}
                    {{-- TODO:決済代行に移動 --}}
                    {{-- <button class="btn btn-outline-dark m-1">@{{$t("message.change")}}</button> --}}
                </ul>
            </div>
        </div>
        <div class="mt-4" >
            <a-card>
                <div class="row">
                    <div class="col-sm-12">
                        <h5>@{{ $t("message.settlement_history") }}</h5>
                    </div>
                </div>
                <div class="row mx-2">
                    <div class="col-3 text-center">
                        <p @click="sortBy('settlement_at')" :class="sortedClass('settlement_at')">@{{ $t("message.payment_day") }}</p>
                    </div>
                    <div class="col-3 text-center">
                        <p @click="sortBy('plan_id')" :class="sortedClass('plan_id')">@{{ $t("message.plan") }}</p>
                    </div>
                    <div class="col-3 text-center">
                        <p @click="sortBy('amount')" :class="sortedClass('amount')">@{{ $t("message.amount") }}</p>
                    </div>
                    <div class="col-3">
                    </div>
                </div>
                <div class="row m-1 border rounded shadow bg-white col-12 " v-for="(data, key) in filterData" :key="key">
                    <listview-settlement :reload-settlement="reloadSettlement" :data="data"></listview-settlement>
                </div>
                <a-pagination
                    :current="currentPage"
                    :total="total"
                    :page-size="pageSize"
                    @change="change">
                </a-pagination>
            </a-card>
        </div>
    </div>
@endsection

@section('footer-scripts')
<!--Main Pages-->
<script src="{{asset('js/components/settlement/listview-settlement.js')}}"></script>
{{-- <script src="{{asset('js/components/settlement/receipt-settlement.js')}}"></script> --}}
<script src="{{asset('js/components/settlement/generate-receipt.js')}}"></script>
<script src="{{asset('js/components/pagenation/prev-next.js')}}"></script>

<script>
    const messages = {
        en: {
            message: {
                settlement_information: 'Settlement Information',
                settlement_history: 'Settlement History',
                change: 'change',
                settlement_date_latest: 'Latest settlement date',
                settlement_date_next: 'Next settlement date',
                settlement_status: 'Settlement status',
                settlement_complete: '決済完了',
                settlement_fail: '決済失敗',
                settlement_yet: '未決済',
                select_plan: '選択中プラン',
                amount: '決済金額',
                fail_message: '決済に失敗しました。「再決済」ボタンより決済を行ってください。',
                re_settlement: '再決済',
                payment_day: 'Payment Day',
                plan: 'Plan'
            }
        },
        ja: {
            message: {
                settlement_information: '決済情報',
                settlement_history: '決済履歴',
                change: '変更',
                settlement_date_latest: '最終決済日時',
                settlement_date_next: '次回決済日時',
                settlement_status: '決済状況',
                settlement_complete: '決済完了',
                settlement_fail: '決済失敗',
                settlement_yet: '未決済',
                select_plan: '選択中プラン',
                amount: '決済金額',
                fail_message: '決済に失敗しました。「再決済」ボタンより決済を行ってください。',
                re_settlement: '再決済',
                payment_day: '決済日時',
                plan: 'プラン'
            }
        }
    }

    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el:"#app",
        data() {
            // const items = ;  // TODO ページャー機能 あとで調整
            // const perPage = 2;  // TODO ページャー機能 あとで調整
            return {
                loadingCount: 0,
                visible: false,
                data: [],
                filterData: [],
                latest_data: {},
                sort: {
                    key: '', // ソートキー
                    isAsc: false // 昇順ならtrue,降順ならfalse
                },
                currentPage: 1,
                total: 1,
                lastPage: 1,
                pageSize: 5
            };
        },
        computed: {
            sortedData: function() { // ソート実施
                if(this.sort.key) {
                    this.data.sort((a, b) => {
                        a = a[this.sort.key];
                        b = b[this.sort.key];
                        return (a === b ? 0 : a > b ? 1 : -1) * (this.sort.isAsc ? 1 : -1);
                    });
                }
            }
        },
        filters: {
            number_format(value) {
                if (value) {
                    return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' )
                }
            }
        },
        methods: {
            showModal: function () {
                this.visible = true;
            },
            handleOk(e) {
                this.visible = false;
            },
            reloadSettlement() {
                self = this
                this.loadingCount++
                axios.get("settlement/lists")
                .then(function (response){
                    self.data = response.data.settlements
                    self.latest_data = response.data.latest
                    self.preparePagination(self.data)
                })
                .finally(() => this.loadingCount--)
            },
            // sort用キーをセットし、昇順・降順を入れ替える
            sortBy: function(key) {
                this.sort.isAsc = this.sort.key === key ? !this.sort.isAsc : false;
                this.sort.key = key;
                this.currentPage = 1
                this.preparePagination();
            },
            sortedClass: function(key) {
                return this.sort.key === key ? `sorted ${this.sort.isAsc ? 'asc' : 'desc' }` : '';
            },
            change(page) {
                if (page >= 1 && page <= this.lastPage) {
                    this.currentPage = page
                    this.preparePagination(page);
                }
            },
            preparePagination() {
                let offset = (this.currentPage * this.pageSize) - this.pageSize
                let until = offset + this.pageSize
                if (until > this.data.length) {
                    until = this.data.length
                }
                this.filterData = (this.sortedData) ? this.sortedData.slice(offset, until) : this.data.slice(offset, until)
                this.total = this.data.length
                this.lastPage = Math.ceil(this.data.length / this.pageSize)
            },
        },
        beforeMount() {
            this.reloadSettlement()
        }
    })
</script>
@endsection
@section('css-styles')
    <style>
        .failed_box {
            display: flex;
            flex-wrap: wrap;
            align-items:center;
        }
        
        .failed_box span {
            margin: 0 20px 0 0;
        }

        .failed_text {
            color:#FF4E00;
        }

        .btn-failed {
            background: #FF4E00;
            color: #fff;
        }

        p.sorted.desc::after {
            display: inline-block;
            content: ' ▼';
        }

        p.sorted.asc::after {
            display: inline-block;
            content: ' ▲';
        }

        .item_table p {
            cursor: pointer;
        }

        .table_title {
            font-size: 1.5rem;
            margin:20px 0 0;
        }

        .pagination {
            text-align: center;
        }

        .pagination * {
            display: inline;
        }

        a {
            /* border: 0;
            background: none;
            font-size: initial;
            margin: 0 1rem; */
        }

    </style>
@endsection