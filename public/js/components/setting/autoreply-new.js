Vue.component ('autoreply-new', {
      template: 
      `<div>
      <button @click="showModal" class="btn btn-outline-dark">New/Edit</button>
      <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="1250" :footer="null">
          <div class="row px-2">
              <span>Lorem ipsum</span>
          </div>
          <div class="row px-2">
              <b><input class="borderless-input form-control" type="text" placeholder="Title" style="font-size: 24px"></b>
          </div>
          <div class="d-flex justify-content-self align-items-center py-2">
            <span>Delivery time</span>
            <select class="form-control mx-2" style="width: 25%">
              <option selected>Immediate</option>
            </select>
          </div>
          <div class="d-flex justify-content-between align-items-center py-2">
            <div class="">
              <span>Delivery Target</span>
            </div>
            <div class="">
              <span>Tag :</span>
              <span>Survey Respondents</span>
            </div>
            <div class="">
              <span>Scenarios:</span>
              <span>Scenarios:</span>
            </div>
            <div class="">
              <button class="btn mx-1 rounded-grey">MessageTarget</button>
              <button class="btn mx-1 rounded-white">SendtoAll</button>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center py-2">
            <div class="">
              <span>Completed delivery Target</span>
            </div>
            <div class="">
              <span>Tag :</span>
              <span>Survey Respondents</span>
            </div>
            <div class="">
              <span>Scenarios:</span>
              <span>Lorem ipsum</span>
            </div>
            <div class="">
              <button class="btn mx-1 rounded-grey">MessageAction</button>
            </div>
          </div>
          <hr>
      </a-modal>
      </div>`,
      data() {
        return {
        visible: false,
        RoundedGreen: "rounded-green",
        RoundedWhite: "rounded-white",
        }
      },
      methods: {
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
      }
  });
