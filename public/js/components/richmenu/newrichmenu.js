Vue.component ('new-richmenu', {
    template:
    `<div>
        <button v-if="type == 'New'" @click="showModal" v-bind:class="buttonclass" class="mb-1">{{$t('message.new')}}</button>
        <button v-if="type == 'Edit'" @click="showModal" v-bind:class="buttonclass">{{$t('message.edit')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
            <form id="richmenuForm" method="post" v-on:submit.prevent>
                <div>
                    {{$t("message.new_rich_menu")}}
                    <b><input name="title" class="borderless-input form-control" type="text" :placeholder="$t('message.title')" style="font-size: 24px" v-model="title"></b>
                </div>
                <div class="row align-items-center mt-2">
                    <div class="col-sm-4 align-items-center">
                        {{$t('message.change_display_method')}}<br>
                        <select-richmenu-layout :layout-type="layout_type" :select-layout-type="selectLayoutType"></select-richmenu-layout>
                    </div>
                    <div class="col align-items-center">
                        <div class="row">
                            <div class="col">
                                <img v-bind:src="layoutTypeImageSrc" alt="" class="w-100" style="max-width:275px;">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row align-items-center">
                    <div class="col align-items-center">
                        {{$t('message.delivery_completion')}}
                    </div>
                    <div class="col align-items-center">
                        <span v-for="(targetServe, key) in targetServes">
                            <span v-if="key!=0">&nbsp; : &nbsp;</span>{{ targetServe }} {{ key+1 }}
                        </span>
                    </div>
                    <div class="col align-items-center">
                        <div class="row justify-content-end">
                            <message-target :data="target" :reset="clearTarget" :update-data="updateTarget"> </message-target>
                            <!-- <button type="button" class="btn rounded-white" @click="sendToAll = !sendToAll">{{$t('message.send_to_all')}}</button> -->
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <!-- 複数のアップロードの場合（将来使うかも） -->
                    <!-- <div v-for="(menuImage, index) in menuImages" class="col-md-4 align-items-center mb-1">
                        <div v-if="menuImage.featured_url">
                            <img class="img-fluid" :src="menuImage.featured_url" />
                        </div>
                        <a-upload-dragger v-else name="file" :showUploadList="false" :beforeUpload="() =>{return false}" @change="onFileChange" accept=".jpeg, .jpg, .png">
                            <p class="ant-upload-drag-icon">
                                <a-icon type="upload" />
                            </p>
                            <p class="ant-upload-text">{{$t('message.upload_image')}}</p>
                        </a-upload-dragger>
                        <span v-show="error.index == index && error.type == 'image'" class="error">{{error.msg}}</span>
                    </div> -->
                    <a-upload-dragger :action="menuImages.featured_url" name="file" :showUploadList="false" :beforeUpload="() =>{return false}" @change="onFileChange" accept=".jpeg, .jpg, .png">
                        <div v-if="menuImages.featured_url">
                            <img class="img-fluid" :src="menuImages.featured_url" />
                        </div>
                        <div v-else>
                            <p class="ant-upload-drag-icon">
                                <a-icon type="upload" />
                            </p>
                            <p class="ant-upload-text">{{$t('message.upload_image')}}</p>
                        </div>
                    </a-upload-dragger>
                    <span v-show="error.type == 'image'" class="error col-12">{{error.msg}}</span>
                </div>
                <div id="image_description">
                    <p style="margin-bottom: 0px; margin-top: 1rem">
                        ※画像容量が1MBを超えるとアップロードできません。
                    </p>
                    <p style="margin-bottom: 0px;">
                        ※リッチメニューは以下サイズでの設定が推奨です。
                    </p>
                    <ul>
                        <li style="margin-left: 20px">
                            <span>2500×1686：高解像度端末向け。容量が大きく読み込みに時間がかかる可能性があります。</span>
                        </li>
                        <li style="margin-left: 20px">
                            <span>1200×810（推奨）：一般的なスマホ向け。こちらが推奨サイズです。</span>
                        </li>
                        <li style="margin-left: 20px">
                            <span>800×540：より容量をおさえることができますが、画像が荒れる可能性があります。</span>
                        </li>
                        <li style="margin-left: 20px">
                            <span>400×270：ネットワーク環境が整わない海外向けの、最も容量の小さなサイズです。</span>
                        </li>
                    </ul>

                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        {{$t('message.menu_selection')}}
                    </div>
                </div>
                <div v-for="(richMenu, index) in richMenus">
                    <div class="row justify-content-center">
                        <a-radio-group>
                            <a-radio-button v-on:click="switchMessageAction(index, 0)">{{$t('message.url')}}</a-radio-button>
                            <a-radio-button v-on:click="switchMessageAction(index, 1)">{{$t('message.send_message')}}</a-radio-button>
                        </a-radio-group>
                    </div>
                    <div v-if="menuActions[index] == 0" class="row">
                        <div class="col align-items-center">
                            <div class="form-group">
                                <label class="form-control-label w-100">{{$t('message.url')}}
                                </label>
                                <input name="url" class="form-control" type="text" :placeholder="$t('message.url')" v-model="actionUrl[index]">
                                <span v-show="error.index == index && error.type == 'action'" class="error">{{error.msg}}</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="menuActions[index] == 1" class="row">
                        <div class="col align-items-center">
                            <div class="form-group">
                                <label class="form-control-label w-100">
                                    {{$t('message.send_message')}}
                                </label>
                                <textarea class="form-control" v-model="actionMessage[index]"></textarea>
                                <span v-show="error.index == index && error.type == 'action'" class="error">{{error.msg}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <div class="row justify-content-center">
                        <button type="button" class="btn rounded-green m-1" @click="register">{{$t('message.register')}}</button>
                    </div>
                </div>
            </form>
        </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    new_rich_menu: "Create a new rich menu",
                    new: "New",
                    title: "Title",
                    change_display_method: 'Change the display method',
                    change: 'change',
                    image1: "Image",
                    menu_selection: "Menu selection",
                    url: 'URL',
                    send_message: "Send message",
                    register: "Register",
                    upload_image: 'Upload Image',
                    delivery_completion: 'Delivery Completion',
                }
            },
            ja: {
                message: {
                    new_rich_menu: 'リッチメニュー新規作成',
                    new: "新規",
                    edit: "編集",
                    title: "タイトル",
                    change_display_method: '表示方法を変更する',
                    change: "変更",
                    image1: "画像",
                    menu_selection: "メニューの選択",
                    url: 'URL',
                    send_message: 'メッセージの送信',
                    register: "登録",
                    upload_image: '画像をアップロード',
                    delivery_completion: '配信対象設定追加',
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'btnclass', 'reloadRichMenu', 'type', 'richMenusData', 'loadingCount'],
    data() {
        return {
            // data: {},
            layout_type: 1,
            layoutTypeImageSrc: "img/rich_menu/layout1.png",
            menuActions: [],
            menuImages: this.data ? {featured_url: JSON.parse(this.data.rich_menu_file).featured_url} : {featured_url: ""},
            actionUrl: [],
            actionMessage: [],
            title: '',
            richMenus: {},
            buttonclass: "btn mx-1 " + this.btnclass,
            visible: false,
            uploadUrl: baseUrl + '/upload/image',
            file: [],
            error: {index: null, type:'', msg: ''},
            target: {},
            targetServes: []
        }
    },
    methods: {
        filterType(richMenus, type) {
            return richMenus.filter(function(richMenu) {
                return richMenu.rich_menu_type == type
            })
        },
        updateTarget() {
            tmp = this.title
            this.title = ' '
            this.title = tmp
            magazine = this
            magazine.targetServes = []
            $.each(this.target, function(section, temp){
                $.each(temp.serves, function(key, serve){
                    serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function(key, serve_child){
                            magazine.targetServes.push(serve_child)
                        })
                    } else {
                        if (section != 'dates') {
                            magazine.targetServes.push(serve_value)
                        }
                    }
                })
            })
        },
        init() {
            if (this.data) {
                this.richMenus = this.filterType(this.richMenusData, this.data.rich_menu_type)
                this.reset()
                this.target = this.data.target
                this.updateTarget()
                if (this.data.target.dates.serves.length > 0) {
                    for (let i = 0; i < this.data.target.dates.serves.length; i++) {
                        if (this.data.target.dates.serves[i].value.hasOwnProperty('from')) {
                            this.data.target.dates.serves[i].value.from = moment(new Date(this.data.target.dates.serves[i].value.from))
                            this.data.target.dates.serves[i].value.to = moment(new Date(this.data.target.dates.serves[i].value.to))
                        }
                    }
                    for (let i = 0; i < this.data.target.dates.excludes.length; i++) {
                        if (this.data.target.dates.excludes[i].value.hasOwnProperty('from')) {
                            this.data.target.dates.excludes[i].value.from = moment(new Date(this.data.target.dates.excludes[i].value.from))
                            this.data.target.dates.excludes[i].value.to = moment(new Date(this.data.target.dates.excludes[i].value.to))
                        } 
                    }
                }
                return
            }
            this.layout_type = 1
            this.menuImages = {featured_url: ""}
            this.richMenus = this.filterType(this.richMenusData, this.layout_type)
            this.reset()
        },
        selectLayoutType(layout_type) {
            this.layout_type = layout_type
            this.layoutTypeImageSrc = "img/rich_menu/layout" + layout_type + ".png"
            this.richMenus = this.filterType(this.richMenusData, this.layout_type)
            this.menuActions = this.richMenus.map(r => 0)
            this.actionUrl = this.richMenus.map(r => '')
            this.actionMessage = this.richMenus.map(r => '')
            // this.menuImages = []
        },
        switchMessageAction(index, action) {
            this.$set(this.menuActions, index, action)
        },
        onFileChange(info) {
            let formData = new FormData()
            formData.append('file', info.file)
            this.$emit('input', this.loadingCount + 1)
            axios.post(this.uploadUrl, formData)
            .then(res => {
                // this.menuImages.splice(0,1)
                this.menuImages= res.data
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        register() {
            // エラーメッセージのリセット
            this.error = {index: null, type:'', msg: ''}

            let form = $("#richmenuForm")

            form.validate({
                rules: {
                    title: "required"
                }
            })

            if (!form.valid()) {
                return
            }

            for (let i = 0; i < this.menuActions.length; i++) {
                if (this.menuActions[i] === 0) {
                    if (this.actionUrl[i] === '') {
                        this.showErrorRequired(i, 'action')
                        return
                    } else {
                        if (!this.isExistUrlScheme(this.actionUrl[i])) {
                            this.showErrorExistUrlScheme(i, 'action');
                            return;
                        }

                        if (!this.isValidURL(this.actionUrl[i])) {
                            this.showErrorInvalidUrl(i, 'action')
                            return
                        }
                    }
                } else if (this.menuActions[i] === 1) {
                    if (this.actionMessage[i] === '') {
                        this.showErrorRequired(i, 'action')
                        return
                    }
                }
            }
            let actions = Array()
            for (let i = 0; i < this.menuActions.length; i++) {
                if (this.menuActions[i] === 0) {
                    actions[i] = {type: "uri", uri: this.actionUrl[i]};
                } else if (this.menuActions[i] === 1) {
                    actions[i] = {type: "message", uri: this.actionMessage[i]};
                }
            }

            if (!this.menuImages.hasOwnProperty('featured_url') || !this.menuImages.featured_url) {
                this.showErrorRequired(0, 'image')
                return
            }
            // for (let i = 0; i < this.menuImages.length; i++) {
            //     if (!this.menuImages[i].hasOwnProperty('featured_url') || !this.menuImages[i].featured_url) {
            //         console.log(this.menuImages[i])
            //         this.showErrorRequired(i, 'image')
            //         return
            //     }
            // }

            //複数の場合つかう
            // let images = this.menuImages.map(menuimg => menuimg.featured_url)
            let images = this.menuImages
            
            let formData = new FormData()
            formData.append('title', this.title)
            formData.append('rich_menu_type', this.layout_type)
            formData.append('rich_menu_file', JSON.stringify(images))
            formData.append('action_value_data', JSON.stringify(actions))
            formData.append('target', JSON.stringify(this.target))

            self = this
            if (this.data) {
                this.$emit('input', this.loadingCount + 1)
                axios.post("richmenu/edit/" + this.data.id, formData)
                    .then(function(response){
                        self.reloadRichMenu()
                        self.layout_type = 1
                        self.reset()
                        self.title = ''
                        self.visible = false
                    })
                .finally(() => this.$emit('input', this.loadingCount - 1))
                    return
            }

            this.$emit('input', this.loadingCount + 1)
            axios.post("richmenu", formData).then(function(response){
                self.reloadRichMenu()
                self.layout_type = 1
                self.reset()
                self.title = ''
                self.visible = false
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateTarget() {
            tmp = this.name
            this.name = ' '
            this.name = tmp
            richmenu = this
            richmenu.targetServes = []
            $.each(this.target, function(section, temp) {
                $.each(temp.serves, function(key, serve) {
                    serve_value = serve.value
                    if ($.isArray(serve_value)) {
                        $.each(serve_value, function(key, serve_child) {
                            richmenu.targetServes.push(serve_child)
                        })
                    }
                })
            })
        },
        clearTarget() {
            this.target = {}
        },
        reset() {
            if (this.data !== undefined && Object.keys(this.data).length !== 0) {
                this.title = this.data.title
                let file = {} 
                file.featured_url = this.data.rich_menu_file
                this.file.push(file)
                this.layout_type = this.data.rich_menu_type
                this.layoutTypeImageSrc = "img/rich_menu/layout" + this.data.rich_menu_type + ".png"
                let actions = JSON.parse(this.data.action_value_data)
                this.title = this.data.title
                this.actionUrl = actions.map(action => action.type === 'uri' ? action.uri : action.uri)
                this.actionMessage = actions.map(action => action.type === 'message' ? action.uri : action.uri)
                this.menuActions = actions.map(action => action.type === 'uri' ? 0 : 1)
                this.target = {}
                this.targetServes = []
                
                // let menuImages = JSON.parse(this.data.rich_menu_file)
                // this.menuImages = this.richMenus.map(r => [])
                // for (let i = 0; i < menuImages.length; i++) {
                //     this.menuImages[i].featured_url = menuImages[i]
                // }
                return
            }
            this.selectLayoutType(this.layout_type)
            this.menuActions = this.richMenus.map(r => 0)
            this.actionUrl = this.richMenus.map(r => '')
            this.actionMessage = this.richMenus.map(r => '')
            // this.menuImages = this.richMenus.map(r => '')
            this.target = {}
            this.targetServes = []
        },
        showModal() {
            this.init()
            this.visible = true
        },
        handleOk(e) {
            this.visible = false
        },
        addMessage(event) {
            alert(event)
        },
        showErrorRequired(i, t) {
            Object.assign(this.error, {index: i, type: t, msg: i18n.locale === 'ja' ? i18n.messages.ja.message.cannot_be_empty : i18n.messages.en.message.cannot_be_empty})
        },
        showErrorInvalidUrl(i, t) {
            Object.assign(this.error, {index: i, type: t, msg: i18n.locale === 'ja' ? i18n.messages.ja.message.invalid_url : i18n.messages.en.message.invalid_url})
        },
        showErrorExistUrlScheme(i, t) {
            Object.assign(this.error, {index: i, type: t, msg: i18n.locale === 'ja' ? i18n.messages.ja.message.not_exist_url_scheme : i18n.messages.en.message.not_exist_url_scheme})
        },
        isValidURL(string) {
            let res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
            return (res !== null)
        },
        isExistUrlScheme(string) {
            let res = string.match(/^(http(s)?:\/\/)/);
            return res !== null;
        }
      }
  });
