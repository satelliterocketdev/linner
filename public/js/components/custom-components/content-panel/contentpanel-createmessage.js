class MessageDecorator {
    constructor(dom) {
        this.node = dom
    }

    getNodesHavingText(master) {
        var nodes = Array.prototype.slice.call(master.getElementsByTagName("*"), 0);
        // 子要素にテキストノードを抱えているノードを抽出
        var leafNodes = nodes.filter(function(elem) {
            if (!elem.hasChildNodes()){
                return false
            }
            for (var i = 0; i < elem.childNodes.length; i++) {
                if (elem.childNodes[i].nodeType == Node.TEXT_NODE) {
                    return true
                }
            }
            return false
        });
        return leafNodes;
    }

    decorateUrl(){
        this.leafNodes = this.getNodesHavingText(this.node)
        var resNodes = []
        this.leafNodes.forEach(function(v, i){

            // unwapするときにテキストノードは複数に分裂するため、一度結合させる。
            v.normalize()

            // nbspを見つけたらspaceで括る （urlの直後にあるケースでは誤動作を起こすので先頭に半角スペース）
            v.innerHTML = v.innerHTML.replace( /(&nbsp;)+/g , ' <space>$&</space>')

            var text = v.innerHTML

            var regex = /https?:\/\/(\S)+/g
            var regexImage = /<img src="https?:\/\/(\S)+/g
            var tmp;
            
            const ignoreUrlRegex = RegExp('^' + GLOBAL_APP_ROOT_PATH + '/','')
            var i = 0
            var textArray = [];
            while ((tmp = regex.exec(text)) !== null) {

                if(regexImage.exec(text)){ //url がイメージタグだった場合
                    continue
                }

                var u = tmp[0]
                if(i < tmp.index) {
                    textArray.push(text.slice(i, tmp.index))
                }

                var urltext = text.slice(tmp.index, regex.lastIndex)
                if(u.match(ignoreUrlRegex)) {
                    textArray.push('<span class="internalurl">' + urltext + '</span>')
                } else {
                    textArray.push('<span class="actionurl">' + urltext + '</span>')
                }

                i = regex.lastIndex
                resNodes.push({node: v, url: u})
            }
            textArray.push(text.slice(i))

            var newText = textArray.join('')
            newText = newText.replace(/\s(<space>)|(<\/space>)/g, '')
            v.innerHTML = newText
        })
        return resNodes
    }
}
  
Vue.component ('contentpanel-createmessage', {
    template: 
    `<div>
        <div class="row p-2">
            <addcontent-template :content="content" @updateFromTemplate="updateFromTemplate" v-model:loading-count="loadingCountData"></addcontent-template>
            <addcontent-main :data="content" v-model:loading-count="loadingCountData"></addcontent-main>
        </div>
        <div class="row align-items-start p-2">
            <div class="col-sm-7">
                <div class="row">
                    <!-- <textarea class="form-control" rows="8" v-model="data.content_message" :maxlength="maxlength">{{ data.content_message }}</textarea> -->
                    <div id="message" name="message" contenteditable="true" :data="data.content_message" class="form-control" style="min-height: 370px; overflow-y: auto;" @keyup="write" @focus="msgFocus" @blur="msgBlur" @paste="msgPaste"></div>
                    <!-- <textarea class="contentpanel-createmessage">{{ content.message }}</textarea> -->
                </div>
                <div class="row pt-2" style="font-size: 12px">
                <div class="col-10 p-0">
                    <ul style="list-style-type: none">
                    <li>{{$t('message.warning_text1')}}</li>
                    <li>{{$t('message.warning_text2')}}</li>
                    <li v-if="urlclickAction == 'true'">{{$t('message.warning_text3')}}</li>
                    </ul>
                </div>
                    <div class="col-2 p-0 text-right">{{ length }} / {{ maxlength }}</div>
                </div>
                <!-- URLクリック時のアクション設定 -->
                <template v-if="urlclickAction == 'true'">
                <p>{{$t('message.action_setting')}}</p>
                <div class="pl-4">
                    <div v-for="obj in content.url_actions" class="row align-items-center my-1">
                        <div class="col-8">{{obj.url}}</div>
                        <div class="col-4">
                            <button type="button" class="btn rounded-blue mx-1" @click="showReactionModal(obj)">{{$t('message.setting')}}</button>
                        </div>
                    </div>
                <action-modal ref="reactionModal"></action-modal>
                </div>
                </template>
                <div class="row">
                    <div v-for="attachment in content.attachments" class="mr-1">
                        <img :src="attachment.featured_url" :height="attachment.type=='emoji' ? 26 : 69" />
                        <i class="fa fa-times" @click="removeAttachment(attachment)"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="row justify-content-center rounded bg-dark m-1">
                    <h3 class="mt-2" style="color: white"><i class="far fa-smile-wink mr-2"></i>EMOJI</h3>
                    <emoji :write="writemessage" :attachment="content.attachments"></emoji>
                </div>
            </div>
        </div>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    attachment: 'Attachment',
                    warning_text1: 'If you enter [you], it will be converted to the destination name. ',
                    warning_text2: 'If friend information is registered, it will be converted to sending friend\'s information by entering [friend name].',
                    warning_text3: '',
                    action_setting: 'Action setting when URL is clicked',
                    setting: 'Setting'
                } 
            },
            ja: {
                message: { 
                    attachment: '添付',
                    warning_text1: '[you]と入力すると送信先の名前へ変換されます。',
                    warning_text2: 'フレンド情報を登録している場合、[フレンド名]と入力するとで送信先の友だち情報へ変換されます。',
                    warning_text3: 'URLに日本語が含まれる場合は正常に動作しない場合があります。',
                    action_setting: 'URLクリック時のアクション設定',
                    setting: '設定'
                } 
            }
        }
    },
    model: {
        prop : 'loadingCount',
        event : 'input'
    },
    props: ['data', 'type', 'urlclickAction', 'loadingCount'],
    data() {
        return {
            length: 0,
            maxlength: 2000,
            content: {
                content_type: 'message',
                attachments: [],
                content_message: '',
                url_actions: [],
            },
            defaultContent: {
                content_type: 'message',
                attachments: [],
                content_message: '',
                url_actions: [],
            },
            tinymce,
            tmp: null,
            selected: {},
            // m: this.data.content_message,
        }
    },
    watch: {
        "data.content_message"(value) {
            this.length = value.replace(/<img src=".+?>/g, 'i')
            .replace(/<div>|<\/div>|<span>|<\/span>/g, '')
            .replace(/<br>|<br *\/>/g, 'i').length
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
    mounted() {
        if (this.type !== 'New' && this.data) {
            Object.assign(this.content, this.data)
        } else {
            Object.assign(this.content, this.defaultContent)
        }

        this.writemessage(this.content.content_message)

        this.$nextTick(function(){
            // レンダリング後にデコレーションだけ処理する
            this.msgDecoration()
        })
    },
    methods: {
        showReactionModal(obj){
            var self = this
            this.$refs.reactionModal.showModal(obj.actions, function(info){
                obj.actions = info
                self.$emit('updateTextInput', self.content)
            })
        },
        msgDecoration(){
             // div直下に素のテキストが存在する場合はwrap
             $('#message').contents().filter( function(){
                return this.nodeType == Node.TEXT_NODE
            }).wrap('<div />')

            var dom = $('#message')[0]
            var d = new MessageDecorator(dom)
            d.decorateUrl()
        },
        msgFocus(){
            $('#message .internalurl').contents().unwrap();
            $('#message .actionurl').contents().unwrap();
        },
        msgBlur(){
            this.msgDecoration()
            const pre = this.content.url_actions;
            var url_actions = []
            $('#message .actionurl').each(function(index, node){
                var newData = {
                    actions : {
                        tags: {serves: [] },
                        scenarios: { serves: []}
                    },
                    url : node.textContent,
                    id : ''
                }

                if(pre.length > 0){
                    // delete で消えている場合判定中のeはundefinedになるため、条件にeの存在確認を入れる
                    var exists = pre.findIndex((e)=> e && (e.url ==  newData.url));
                    if(exists >= 0){
                        newData = pre[exists];
                        delete pre[exists]
                    }
                }
                url_actions.push(newData)
            })

            this.content.url_actions = url_actions
            this.updateMessage($("#message").html())
        },
        msgPaste(e){
            // cancel paste
            e.preventDefault();
        
            // get text representation of clipboard
            var text = (e.originalEvent || e).clipboardData.getData('text/plain');
        
            document.execCommand("insertText", false, text);
        },
        write(e) {
            let value = $(e.target).html()
            this.updateMessage(value)
        },
        writemessage(value) {
            if (!value) {
                value = "";
            }
            const msg = $("#message");
            msg.focus();
            document.execCommand("insertHtml", false, value);

            let updatedValue = msg.html()
            this.updateMessage(updatedValue)
        },
        updateMessage(value) {
            this.content.content_message = value
            this.length = value.replace(/<img src=".+?>/g, 'i')
            .replace(/<div>|<\/div>|<span>|<\/span>|&nbsp;/g, '')
            .replace(/<br>|<br *\/>|&nbsp;/g, 'i').length
            this.$emit('updateTextInput', this.content)
        },
        selectEmoji(emoji) {
            this.content.attachments.push(emoji)
        },
        removeAttachment(attachment) {
            key = this.content.attachments.indexOf(attachment)
            this.content.attachments.splice(key, 1)
        },
        updateFromTemplate(template) {
            Object.assign(this.content, template)
            $("#message").html(this.content.content_message)
            this.updateMessage(this.content.content_message)
        }
    },
});