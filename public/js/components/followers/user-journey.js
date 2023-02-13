Vue.component ('user-journey', {
    template: 
    `<div>
    <button @click="showModal" class="btn btn-info" style="font-size: 12px">User Journey</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="600" :footer="null">
        <div class="row justify-content-between align-items-center">
            <div class="col">
                <div class="row justify-content-center">
                    <i class="far fa-user-circle fa-5x"></i>
                </div>
                <div class="row justify-content-center">
                    <span>User Name</span>
                </div>
            </div>
            <div class="col">
                <span><h2>User Journey</h2></span>
            </div>
        </div>
        <hr>
        <div id="content-panel" class="border roounded p-5 my-2" style="overflow-y: scroll; overflow-x: hidden; width: 100%; height: 50px;">
        </div>
        <div class="row justify-content-center" >
            <button class="btn btn-success mx-1" style="font-size: 12px">Add Tag</button>
            <button class="btn btn-info mx-1" style="font-size: 12px">Add Scenario</button>
            <button class="btn btn-secondary mx-1" style="font-size: 12px">Add Survey</button> 
        </div>
        <div class="footer pt-4">
            <div class="row justify-content-between">
                <button class="btn rounded-white mx-1">lorem ipsum</button>
                <button class="btn rounded-green mx-1">Confirm</button>
                <button class="btn rounded-red mx-1">Stop all actions</button>
            </div>
        </div>
        
    </a-modal>
    </div>`,
    data() {
        return {
        visible: false,
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