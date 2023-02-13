Vue.component ('confirm-email', {
  template: 
  `<div class="inlineblock">
    <button type="button" @click="showModal" v-bind:class="btnclass" class="font-size-table mb-1">{{$t('message.send')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
            <h2>{{data.title}}</h2>
            <form>
                <div class="form-group">
                    <textarea class="form-control" v-model="data.content_message" rows="10" readonly></textarea>
                </div>
            </form>
            <a-button type="primary" @click="send" :loading="loading">{{$t('message.send')}}</a-button>
        </a-modal>
    </div>`,
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'loadingCount'],
    data() {
        return {
            visible: false,
            loading: false
        }
    },
    methods: {
        showModal() {
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        send(){
            this.loading = true
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("send_invitation/" + this.data.id)
            .then(function(response){
                self.loading = false
                self.handleOk()
                if (self.$parent.$parent && typeof self.$parent.$parent.changeTutorialState === 'function') {
                    self.$parent.$parent.changeTutorialState(2)
                }
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        }
    }
});