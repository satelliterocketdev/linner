Vue.component ('edit-message', {
    template: 
    `<div>
    <button @click="showModal" class="btn rounded-green">Edit Message</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="1000" :footer="null">
        <div>
            Delivery Statement Set
            <b><h5><input class="borderless-input form-control" type="text" placeholder="Title"></h5></b>
        </div>
        <div class="row p-1">
            <new-message-slidebox> </new-message-slidebox> 
        </div>
        <hr>
        <div class="row justify-content-center p-1">
            
        </div>
        <!--CONTENT PANEL-->
        <div id="content-panel" class="p-1">
            
        </div>
        <div class="footer pt-1">
            <div class="row align-items-end">
                <div class="col-sm-8 text-left">
                    <button class="btn rounded-blue m-1">Send/Schedule Send</button>
                    <button class="btn rounded-green m-1">Save as draft</button>
                    <button class="btn rounded-red m-1">Send test</button> 
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-block rounded-cyan m-1">Select Draft</button>
                    <button class="btn btn-block rounded-white m-1">Save as template</button>
                </div>
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