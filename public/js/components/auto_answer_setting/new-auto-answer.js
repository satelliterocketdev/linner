Vue.component ('new-auto-answer', {
    template:
    `<div>
    <button v-if="type == 'New'" @click="showModal" v-bind:class="buttonclass">{{$t('message.new')}}</button>
    <button v-else @click="showModal" v-bind:class="buttonclass" class="mb-1">{{$t('message.edit')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
        <form id="autoAnswerForm" method="post" v-on:submit.prevent>
            <div>
                <span v-if="type == 'New'">{{$t("message.new_autoanswer_title")}}</span>
                <span v-else>{{$t("message.edit_autoanswer_title")}}</span>
                <b><input name="title" v-model:value="title" class="borderless-input form-control" type="text" :placeholder="$t('message.title')" style="font-size: 24px"></b>
            </div>
            <hr>
            <div>{{$t('message.response_time')}}</div>


            <div class="condition_box">
                <div class="btn btn-outline-secondary">
                    <label><input type="radio" v-model="condition" @change="onChange" name="condition" class="condition" value="always"> {{$t('message.always')}}</label>
                </div>
                <div class="btn btn-outline-secondary">
                    <label><input type="radio" v-model="condition" @change="onChange" name="condition" class="condition" value="specified"> {{$t('message.condition')}}</label>
                </div>
            </div>
    
            <div v-if="is_condition" class="condition_detail">
                <div class="week_box">
                    <!-- 曜日設定 start -->
                        <div v-for="(label,id) in weekOptions" class="week_item" >
                            <input type="checkbox" v-model="week[id].value" :value="id" :id="'week_'+id" class="week_checkbox" >
                            <label class="week_btn" :for="'week_'+id">{{ label }}</label>
                        </div>
                    <!-- 曜日設定 end -->
                </div>
                <div class="timeset_box">
                    <div>
                        <div class="btn btn-outline-secondary">
                            <label><input type="radio" v-model="is_timeset" @change="timeSetChange" class="condition" value="false"> {{$t('message.anytime')}}</label>
                        </div>
                        <div class="btn btn-outline-secondary">
                            <label><input type="radio" v-model="is_timeset" @change="timeSetChange" class="condition" value="true"> {{$t('message.timing')}}</label>
                        </div>
                    </div>
                    <div v-if="is_timeset" class="col-sm">
                        <!-- 時間設定 start  -->
                            <a-time-picker v-model="from_time" format="HH:mm" :placeholder="$t('message.time')" />
                            {{$t('message.from')}}
                            <a-time-picker v-model="to_time" format="HH:mm" :placeholder="$t('message.time')" />
                            {{$t('message.to')}}
                        <!-- 時間設定 end  -->
                    </div>
                </div>
            </div>


 
   
            <hr>
            <div class="row pt-3"><div class="col-sm">{{$t('message.response_keyword')}}</div></div>
            <div class="row pb-3">
                <div class="col-sm">
                    <textarea name="keyword" v-model="keyword" :placeholder="$t('message.keyword')" class="form-control center-block" maxlength="1000" rows="3" style="resize: none"></textarea>
                    <!--<select class="custom-select col-sm-2"><option value="first">{{$t('message.matches_perfectly')}}</option></select>-->
                    <p>{{$t('message.comma_comment')}}</p>
                </div>
            </div>
            <!--<div class="row justify-content-center p-1">
                <button type="button" class="btn rounded-blue m-1">{{$t('message.add_keyword')}}</button>
            </div>
            <div class="row justify-content-around p-1">
                <button type="button" class="btn btn-outline-secondary">{{$t('message.all')}}</button>
                <button type="button" class="btn btn-outline-secondary">{{$t('message.some')}}</button>
            </div>-->
            <hr>
            <div class="row pt-3">
                <div class="col-sm">{{$t('message.message')}}</div>
            </div>
            <div class="row pb-3">
                <div class="col-sm">
                    <textarea name="message" v-model:value="message" class="form-control" maxlength="1000" rows="3" style="resize: none"></textarea>
                </div>
            </div>
            <div class="row p-2">
                <div class="col d-flex justify-content-center">
                    <a-button class="rounded-green" @click="register">{{$t('message.register')}}</a-button>
                </div>
            </div>
        </form>
    </a-modal>
    </div>`,
    i18n: {
        messages: {
            en: {
                message: {
                    new: 'New',
                    edit: 'Edit',
                    new_autoanswer_title: 'New Auto Answer',
                    edit_autoanswer_title: 'Edit Auto Answer',
                    title: 'Title',
                    message: 'Message',
                    register: 'Register',
                    response_keyword: 'Response Keyword',
                    matches_perfectly: 'Matches Perfectly',
                    keyword: 'Keyword',
                    add_keyword: 'Add Keyword',
                    all: 'All',
                    some: 'Some',
                    response_time: 'Response Time',
                    always: 'Always',
                    condition: 'Conditions',
                    day: 'Day',
                    monday: 'Monday',
                    tuesday: 'Tuesday',
                    wednesday: 'Wednesday',
                    thursday: 'Thursday',
                    friday: 'Friday',
                    satuday: 'Saturday',
                    sunday: 'Sunday',
                    timing: 'Time',
                    anytime: 'Anytime',
                    from: 'From',
                    to: '',
                    time: 'Time',
                    comma_comment:'Multiple selections are possible by separating them with commas',

                }
            },
            ja: {
                message: {
                    new: '新規',
                    edit: '編集',
                    title: 'タイトル',
                    new_autoanswer_title: '新規自動応答設定',
                    edit_autoanswer_title: '編集自動応答設定',
                    title: 'タイトル',
                    message: '応答メッセージ内容',
                    register: '登録',
                    response_keyword: '応答キーワード',
                    matches_perfectly: '完全に一致',
                    keyword: 'キーワード',
                    add_keyword: 'キーワード追加',
                    all: '全てを満たす',
                    some: 'いずれかを満たす',
                    response_time: '応答時間',
                    always: '常に',
                    condition: '条件設定',
                    day: '曜日',
                    monday: '月',
                    tuesday: '火',
                    wednesday: '水',
                    thursday: '木',
                    friday: '金',
                    saturday: '土',
                    sunday: '日',
                    timing: '時間帯指定',
                    anytime: 'いつでも',
                    from: 'から',
                    to: 'まで',
                    time: '時間',
                    comma_comment:'カンマ( , )区切りで複数選択可能',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'type', 'reloadAutoAnswerSetting', 'loadingCount'],
    data() {
        return {
            id: 0,
            title: '',
            message: '',
            week: {},
            weekOptions: {
                0: '日',
                1: '月',
                2: '火',
                3: '水',
                4: '木',
                5: '金',
                6: '土'
            },
            auto_answer_keyword:[],
            condition: 'always',
            keyword: '',
            visible: false,
            buttonclass: "btn mx-1 " + this.btnclass,
            is_condition : '',
            is_timeset:'',
            from_time:'',
            to_time:'',
        }
    },
    methods: {
        showModal() {
            this.visible = true

            /*
            * 曜日情報 初期化
            */
            this.week = {
                0: { label:'日', value: false},
                1: { label:'月', value: false},
                2: { label:'火', value: false},
                3: { label:'水', value: false},
                4: { label:'木', value: false},
                5: { label:'金', value: false},
                6: { label:'土', value: false},
            }
        
            if (this.data) {

                this.id           = this.data.id
                this.title        = this.data.title
                this.message      = this.data.content_message
                this.keyword      = this.data.keyword
                this.is_condition = this.data.is_always == 1 ? false : true
                this.condition    = this.data.is_always == 1 ? 'always' : 'specified'
                this.week         = this.data.week
                this.is_timeset   = this.data.from_time != '' ? true : false
                this.from_time    = this.data.from_time != '' ? moment('2019-01-01 ' + this.data.from_time) : null
                this.to_time      = this.data.to_time   != '' ? moment('2019-01-01 ' + this.data.to_time) : null

            }
        },
        handleOk(e) {
            this.visible = true
        },
        /*
        * 常に or 条件設定選択
        */
        onChange(event) {
            if(event.target.value == 'always'){
                this.is_condition = false
            }else if(event.target.value == 'specified'){
                this.is_condition = true
            }
        },
        /*
        * いつでも or 時間指定選択
        */
        timeSetChange(event){
            if(event.target.value == 'true'){
                this.is_timeset = true
            }else{
                this.is_timeset = false
            }
        },
        register() {

            form = $("#autoAnswerForm")
            form.validate({
                rules: {
                    title: "required",
                    message: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            if (this.id) {
                this.update()
                return
            }
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("auto_answer_setting", {
                title           : this.title,
                content_message : this.message,
                condition       : this.condition,
                week            : this.week,
                keyword         : this.keyword,
                from_time       : this.from_time,
                to_time         : this.to_time,
                is_timeset      : this.is_timeset,
                // TODO: ちゃんとした値を渡す
                is_draft        : 0,
                content_type    : 'message',
            })
            .then(function(response){
                self.reloadAutoAnswerSetting()
                self.reset()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        update() {

            form = $("#autoAnswerForm")
            form.validate({
                rules: {
                    title: "required",
                    message: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("auto_answer_setting/" + self.id, {
                title           : this.title,
                content_message : this.message,
                condition       : this.condition,
                week            : this.week,
                keyword         : this.keyword,
                from_time       : this.from_time,
                to_time         : this.to_time,
                is_timeset      : this.is_timeset,
                
            })
            .then(function(response){
                self.reloadAutoAnswerSetting()
                self.reset()
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        reset() {
            this.id = 0
            this.title = ''
            this.message = ''
        }
    }
});
