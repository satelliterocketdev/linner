Vue.component ('survey-behavior', {
    template: 
    `<div>
        <button type="button" @click="showModal" class="btn rounded-white m-1">ビヘイビア</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="1000" :footer="null">
            <div class="row justify-content-center" style="font-size: 20px">
                test
            </div>
            <div class="row p-4">
            </div>
            <div class="footer">
                <div class="row justify-content-center pt-2">
                    <button @click="handleOk" class="btn rounded-green">完了</button>
                </div>
            </div>
        </a-modal>
    </div>`,
    data() {
        return {
            visible: false,
        }
    },
    created() {
    
    },
    methods: {
        showModal() {
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        }
    }
});