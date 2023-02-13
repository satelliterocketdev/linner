Vue.component ('autoreply-friendmessagetarget', {
    template: 
    `<div>
    <a href="#" @click.prevent="showModal" class="mr-2">friendmessagetarget</a>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="500" :footer="null">
        <div class="d-flex">
            <span style="font-size: 18px">Delivery Condition</span>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <span>Person who joined on weekdays</span>
            <span>Person who joined via xx</span>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <span>Day of the week</span>
            <button class="btn mx-1 rounded-white" @click="changeDayOption(0)" v-bind:class="{ 'day-option' : weekday }">Weekdays</button>
            <button class="btn mx-1 rounded-white" @click="changeDayOption(1)" v-bind:class="{ 'day-option' : weekend }">Weekends</button>
            <select class="form-control mx-1" style="font-size: 12px; width: 25%">
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
            </select>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <span>Date</span>
            <input type="date" class="form-control mx-1" style="width: 50%; font-size: 12px">
            <span>from</span>
            <input type="date" class="form-control mx-1" style="width: 50%; font-size: 12px">
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <span>Time</span>
            <input type="time" class="form-control mx-1" style="width: 50%; font-size: 12px">
            <span>from</span>
            <input type="time" class="form-control mx-1" style="width: 50%; font-size: 12px">
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <span>Origin</span>
            <input type="text" class="form-control mx-1" style="width: 50%; font-size: 12px">
            <span>The</span>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2">
            <select class="form-control mx-1" style="width: 75%; font-size: 12px">
                <option>Source as target for sending autoreply</option>
                <option>Source as target for not sending autoreply</option>
            </select>
            <button class="btn mx-1 rounded-green">+</button>
        </div>
        <div class="footer my-2">
            <div class="d-flex justify-content-center align-items-center">
                <button class="btn mx-1 rounded-red">Reset</button>
                <button class="btn mx-1 rounded-green">Finish/Confirm</button>
            </div>
        </div>
    </a-modal>
    </div>`,
    data() {
        return {
        visible: false,
        weekday: false,
        weekend: false,
        }
    },
    methods: {
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
        changeDayOption(e) {
            switch(e) {
                case 0:
                    this.weekday = !this.weekday;
                    this.weekend = false;
                    break;
                case 1:
                    this.weekend = !this.weekend;
                    this.weekday = false;
                    break;
                default:
                    this.weekend = false;
                    this.weekday = false;
                    break;
            }
        }
      }
});