Vue.component ('newmessage-slidebox', {
    template: 
    `<div>
    <div class="container-fluid">
        <div class="row p-2">
            {{$t('message.deliveryTiming')}}
        </div>
        <div class="row p-2">
            <button id="typeBox0" type="button" class="btn btn-outline-dark mx-1" @click="changeOption(0)" style="font-size: 12px">{{$t('message.immediatelyAfterDelivery')}}</button>
            <button id="typeBox1" type="button" class="btn btn-outline-dark mx-1" @click="changeOption(1)" style="font-size: 12px">{{$t('message.specifyTimeAfterRegistration')}}</button>
            <button id="typeBox2" type="button" class="btn btn-outline-dark mx-1" @click="changeOption(2)" style="font-size: 12px">{{$t('message.specifiedTimePassed')}}</button>
            <button id="typeBox3" type="button" class="btn btn-outline-dark mx-1" @click="changeOption(3)" style="font-size: 12px">{{$t('message.specifiedTime')}}</button>
        </div>
        <div class="row p-2">
            <transition name="slide-fade">
                <div v-if="show" class="col border align-items-center">
                    <div v-if="option == 'option1'">
                        <div class="row d-inline-flex">
                            <div class="p-2">
                                <a-input-number
                                    :max="365"
                                    :defaultValue="0"
                                    v-model="days"
                                    @change="daysAfter"
                                    ></a-input-number>
                                {{$t('message.afterDay')}}
                                <a-time-picker :value="schedule_time" @change="changeTimePicker" format="HH:mm" placeholder="" />
                            </div>
                        </div>
                    </div>
                    <div v-else-if="option == 'option2'">
                        <div class="row d-inline-flex">
                            <div class="p-2">
                                <a-select v-model="hour" @change="changeTime" style="display: inline-block; width: 4rem;">
                                    <a-select-option v-for="i in 24" :key="i - 1">{{i - 1}}</a-select-option>
                                </a-select>
                                {{ $t('message.afterHour') }}
                                <a-select v-model="minutes" @change="changeTime" style="display: inline-block; width: 4rem;">
                                    <a-select-option v-for="i in 60" :key="i - 1">{{i - 1}}</a-select-option>
                                </a-select>
                                {{ $t('message.afterMinutes') }}
                            </div>
                        </div>
                    </div>
                    <div v-else-if="option == 'option3'">
                        <div class="row d-inline-flex">
                            <div class="p-2">
                                <a-date-picker format="YYYY-MM-DD" :value="schedule_date" @change="changeDatePicker" :placeholder="$t('message.choose_date')" />
                                <a-time-picker :value="schedule_time" @change="changeTimePicker" format="HH:mm" placeholder="" />
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
            <!-- <button type="button" @click="show = !show"><i class="fas fa-arrow-right"></i></button> -->
        </div>
    </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    deliveryTiming: 'Delivery Timing',
                    immediatelyAfterDelivery: 'Immediately after delivery',
                    specifyTimeAfterRegistration: "Specify time after registration",
                    specifiedTimePassed: 'Specified time passed',
                    specifiedTime: 'Specified Time',
                    minutes: 'Minutes',
                    hours: 'Hours',
                    choose_date: 'Choose Date',
                    afterDay: 'After',
                    after: 'After'
                }
            },
            ja: {
                message: {
                    deliveryTiming: '配信タイミング',
                    immediatelyAfterDelivery: 'シナリオ登録直後',
                    specifyTimeAfterRegistration: "登録後時間指定",
                    specifiedTimePassed: '経過時間指定',
                    specifiedTime: '日時指定',
                    minutes: '分',
                    hours: '時',
                    choose_date: '日付を選択',
                    afterDay: '日後',
                    afterHour: '時間',
                    afterMinutes: "分後",
                    after: '後'
                }
            }
        }
    },
    props: ['data'],
    data() {
        return {
            config: {
                rules: [{ type: 'object', required: true, message: 'Please select time!' }],
            },
            visible: false,
            show: false,
            option: 'option1',
            hour: this.data.schedule_type === "2" ? this.data.time_after.split(":").shift() : 0,
            minutes: this.data.schedule_type === "2" ? this.data.time_after.split(':')[1] : 0,
            days: this.data.schedule_type === "1" ? this.data.time_after : 0,
            schedule_time: this.data.schedule_time ? moment(this.data.schedule_time, 'HH:mm') : moment(),
            schedule_date: this.data.schedule_date ? moment(this.data.schedule_date) : moment(),
        }
    },
    created() {
        if (this.data.schedule_time === null) {
            this.data.schedule_time = this.schedule_time.format('HH:mm');
        }
        if (this.data.schedule_date === null) {
            this.data.schedule_date = this.schedule_date.format('YYYY-MM-DD');
        }
        if (this.data.schedule_type) {
            this.changeOption(this.data.schedule_type)
        }
        // this will fix issue on setting up datepicker
        // let tmp = this.data
        // if (this.data.schedule_date) {
        //     $.extend(this.data, {
        //         schedule_date: moment(tmp.schedule_date).format('yyyy-MM-dd'),
        //         schedule_time: moment(tmp.schedule_date).format('hh:mm'),
        //     })
        // }
    },
    methods: {
        hide () {
            this.visible = false
        },
        changeOption(value) {
            this.option = 'option' + value;
            this.show = value > 0;
            this.data.schedule_type = String(value);
            if (value == 1) {
                this.data.time_after = this.days
            } else if(value == 2) {
                this.changeTime()
            }
        },
        daysAfter() {
            let tmp = moment(new Date(), "DD-MM-YYYY").add(this.days, 'd').format('YYYY-MM-DD')
            this.data.time_after = this.days
            this.data.schedule_date = tmp
        },
        changeTime() {
            this.schedule_time = moment(this.hour + ":" + this.minutes, 'HH:mm')
            // let schedule_time = this.schedule_time.format('HH:mm')
            let hoursAdded = moment().add(this.hour, 'H')
            let minutesAdded = moment().add(this.minutes, 'm')

            this.data.time_after = this.hour + ':' + this.minutes
            this.data.schedule_time = hoursAdded.format('HH') + ':' + minutesAdded.format('mm')
        },
        changeTimePicker(e) {
            this.data.schedule_time = e.format('HH:mm');
            this.schedule_time = moment(e, 'HH:mm');
        },
        changeDatePicker(e) {
            this.data.schedule_date = e.format('YYYY-MM-DD');
            this.schedule_date = moment(this.data.schedule_date, 'YYYY-MM-DD');
        }
    }
});
