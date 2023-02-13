@extends('layouts.app')

@section('content')
<div id="notification" v-cloak>
    <loading :visible="loadingCount > 0"></loading>
    <a-card class="mb-4">
        <div class="row">
            <div class="col">
                <h2>@{{ title }}</h2>
            </div>
        </div>
        <div class="row mb-3 created_at">
            <div class="col-sm-8 ml-0 ml-sm-4">
                @{{ created_at }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8 ml-0 ml-sm-4">
                @{{ content }}
            </div>
        </div>
    </a-card>

    <a-card @click="onSelect(notification)" v-for="notification in notifications" :key="notification.id" :class="'mb-2' + ((notification.id === id) ? ' selected' : '')">
        <div class="row">
            <div class="col wordbreak-all link-pointer" v-bind:class="{ 'font-weight-bold': notification.is_read == 0 }">
                @{{notification.title}}
            </div>
        </div>
    </a-card>
    <a-pagination
        :current="current_page"
        :total="total"
        :page-size="10"
        @change="change"
        :show-total="(total, range) => `${range[0]}-${range[1]} of ${total} items`">
    </a-pagination>
</div>
@endsection

@section('footer-scripts')
<script>
    Vue.config.devtools = true

    const messages = {
        en: {
            message: {
                notifications: 'Notifications',
            }
        },
        ja: {
            message: {
                notifications: 'お知らせ',

            }
        }
    }
    // Create VueI18n instance with options
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}', // locale form config/app.php
        messages, // set locale messages
    })

    var notification = new Vue({
        i18n,
        el:"#notification",
        data:{
            loadingCount: 0,
            notifications: [],
            id: -1,
            title: '',
            content: '',
            created_at: '',

            // ページネーション
            current_page: 1,
            last_page: 1,
            total: 1,
            from: 0,
            to: 0
        },
        beforeMount() {
            @if(isset($notification))
            this.onSelect(JSON.parse('{!! $notification !!}'));
            @endif
            this.reloadNotification(1);
        },
        computed: {
            pages() {
                // let start = this._.max([this.current_page - 2, 1])
                // let end = this._.min([start + 5, this.last_page + 1])
                // start = this._.max([end - 5, 1])
                // return this._.range(start, end)
                let start = Math.max.apply(null, [this.current_page -2, 1]);
                let end = Math.min.apply(null, [start + 5, this.last_page + 1]);
                start = Math.max.apply(null, [end - 5, 1]);
                let range = Array((end - start) + 1);
                for (let i = 0; i < range.length; i++) {
                    range[i] = start + i;
                }
                return range;
            },
        },
        methods:{
            reloadNotification(page) {
                this.loadingCount++
                axios.get('/notifications/page?page=' + page)
                .then(res => {
                    this.notifications = res.data.data;
                    this.current_page = res.data.current_page;
                    this.last_page = res.data.last_page;
                    this.total = res.data.total;
                    this.from = res.data.from;
                    this.to = res.data.to;
                })
                .finally(() => this.loadingCount--);
            },
            change(page) {
                if (page >= 1 && page <= this.last_page) {
                    this.reloadNotification(page);
                }
            },
            onSelect(notification) {
                this.id = notification.id;
                this.title = notification.title;
                this.content = notification.body;
                this.created_at = notification.created_at;

                self = this
                this.loadingCount++
                axios.put('notifications/' + this.id, {

                }).then(function(res) {
                    self.reloadNotification(self.current_page)
                })
                .finally(() => this.loadingCount--)
            }
        }
    });
</script>
@endsection
