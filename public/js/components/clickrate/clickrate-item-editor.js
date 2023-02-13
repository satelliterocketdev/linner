const clickrateForm = {
    template: 
    `<form id="clickrateForm" type="post" v-on:submit.prevent>
        <div class="row m-2">
            <span v-if="editMode">{{$t('message.edit_clickrate')}}</span>
            <span v-else>{{$t('message.register_clickrate')}}</span>
        </div>
        <div class="form-group m-2">
            <input name="title" type="text" class="borderless-input form-control" :placeholder="$t('message.title')" v-model="title" style="font-size: 24px">
        </div>
        <hr>
        <div class="row m-2">
            <span>{{$t('message.url')}}</span>
        </div>
        <div class="row m-2">
            <input v-model="url" type="text" class="form-control" readonly>
        </div>
        <div class="row m-2">
            <span>{{$t('message.redirect_url')}}</span>
        </div>
        <div class="row m-2">
            <input name="redirect_url" v-model="redirect_url" type="text" class="form-control">
        </div>
        <div class="footer">
            <div class="row justify-content-center mx-1">
                <a-button v-if="editMode" class="rounded-green" @click="update">{{ $t('message.update') }}</a-button>
                <a-button v-else class="rounded-green" @click="register">{{ $t('message.register') }}</a-button>
            </div>
        </div>
    </form>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
          en: { 
            message: { 
                title: 'Title',
                register_clickrate: 'Register Clickrate Item',
                edit_clickrate: 'Edit Clickrate Item',
                url: 'URL',
                redirect_url: 'Redirect URL',
                register: 'Register',
                update: 'Update',
            } 
          },
          ja: {
            message: { 
                title: 'タイトル',
                register_clickrate: '外部クリック測定登録',
                edit_clickrate: '外部クリック測定編集',
                url: 'URL',
                redirect_url: '登録するURL',
                register: '登録',
                update: '更新',
            }
          }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props:{ 
        initialData: {
            type: Object,
        },
        editMode: false,
        loadingCount: {
            type: Number
        }
    },
    data() {
        return {
            id: null,
            title: '',
            clickrate_token: null,
            url: '',
            redirect_url: '',
        }
    },
    watch: {
        initialData: {
            handler(){
                this.reset()
            },
            immediate: true,
        },
    },
    mounted(){
        let form = $("#clickrateForm")
        // validationの設置 
        form.validate({
            ignore: [],// hiddenの項目チェックも行うように上書き
            rules: {
                title: "required",
                redirect_url: {required : true, url : true},
            }
        })
    },
    methods:{
        // 登録、更新
        register() {
            let form = $("#clickrateForm")
            if (!form.valid()) {
                return
            }    
    
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.post("clickrate", this.$data)
            .then(function(res){
                self.$emit('completion', res)
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        update() {
            let form = $("#clickrateForm")
            if (!form.valid()) {
                return
            }

            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.put("clickrate/" + this.id, this.$data)
            .then(function(res){
                self.$emit('completion', res)
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        // リセット
        reset() {
            Object.assign(this.$data , _.cloneDeep(this.initialData))
        },
    }
}

Vue.component ('clickrate-item-editor', {
    template: 
    `<div>
        <a-modal :centered="true" v-model="visible" :width="600" :afterClose="afterClose" :footer="null">
        <div v-if="!showing"></div>
        <div v-else-if="loading">Loading...</div>
        <div v-else>
           <clickrate-form :initial-data="itemData" :edit-mode="editMode" @completion="completion" v-model:loading-count="loadingCountData"></clickrate-form>
        </div>
        </a-modal>
    </div>`,
    components: {
        'clickrate-form' : clickrateForm,
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['loadingCount'],
    data() {
        return {
            showing: true,　// modalが開いたとき〜modalが閉じた後までを管理。
            visible: false,
            itemData: null,
            loading : false,
            editMode: false,
        }
    },
    computed: {
        loadingCountData: {
            get() {
                return this.loadingCount
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    },
    methods: {
        openNotificationWithIcon (type, message, desc) {
            this.$notification[type]({
                message: message,
                description: desc,
            });
        },
        initialData(){
            return { 
                title: '',
                clickrate_token: null,
                url: '',
                redirect_url: '',
            }
        },
        getData(itemId){
            // 改めて情報を取得する
            var self = this
            self.loading = true
            this.$emit('input', this.loadingCount + 1)
            axios.get("/clickrate/" + itemId +'/edit')
            .then(res=> {
                self.itemData = res.data;
            })
            .catch(e=> self.openNotificationWithIcon('error','An Error Occurred'))
            .finally(() => {
                self.loading = false
                this.$emit('input', this.loadingCount - 1)
            })
        },
        // 新規時のトークン取得
        generateToken() {
            var self = this
            self.loading = true
            this.$emit('input', this.loadingCount + 1)
            axios.get("clickrate/generate_token")
            .then(response => {
                var data = this.initialData()
                data.clickrate_token = response.data.clickrate_token
                data.url = response.data.url
                self.itemData = data
            })
            .catch(e=> self.openNotificationWithIcon('error','An Error Occurred'))
            .finally(() => {
                self.loading = false
                this.$emit('input', this.loadingCount - 1)
            })
        },
        showModal(itemId) {
            this.showing = true
            this.visible = true
            this.editMode = itemId != null
      
            this.itemData = this.initialData()
            if(itemId){
                this.getData(itemId)
            } else {
                this.generateToken()
            }
        },
        hideModal() {
            this.visible = false
        },
        afterClose(){
            this.showing = false
        },
        completion(res){
            this.$emit('completion', res)
            this.hideModal()
        }
    }
});
