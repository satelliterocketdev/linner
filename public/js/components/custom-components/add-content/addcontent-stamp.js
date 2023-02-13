Vue.component('addcontent-stamp', {
    template:
    `<div>
        <button type="button" @click="showModal" class="btn m-1" style="border: 1px; border-style: solid; height: 150px; width: 150px;">
            <div class="row justify-content-center mb-1">
                <!-- <i class="fas fa-5x fa-paperclip"></i> -->
                <div v-if="selected">
                    <div class="float-right" @click="clearFile"><i class="fa fa-times"></i></div>
                    <img alt="example" style="max-width: 100%; max-height: 80px" :src="selected.featured_url" />
                </div>
                <div v-else><i class="fas fa-5x fa-stamp"></i></div>
            </div>
            <div class="row justify-content-center">
                {{$t('message.send_a_stamp')}}
            </div>
        </button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="500" :footer="null">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                        <img src="img/stickers/1_1.png" width="30px" height="30px">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">
                        <img src="img/stickers/2_18.png" width="30px" height="30px">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                        <img src="img/stickers/3_180.png" width="30px" height="30px">
                    </a>
                </li>
            </ul>
            <div class="tab-content tabpanel">
                <div class="tab-pane fade show active p-4" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row justify-content-center">
                        <div v-for="sticker in stickers.first">
                            <img :src="sticker.url" :data-type="sticker.type" width="69" @click="selectSticker(sticker)" />
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade p-4" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row justify-content-center">
                        <div v-for="sticker in stickers.second">
                            <img :src="sticker.url" :data-type="sticker.type" width="69" @click="selectSticker(sticker)" />
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row justify-content-center">
                        <div v-for="sticker in stickers.third">
                            <img :src="sticker.url" :data-type="sticker.type" width="69" @click="selectSticker(sticker)" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer pt-4">
                <div class="row justify-content-center">
                    <button class="btn rounded-green" @click="close">{{$t('message.done')}}</button>
                </div>
            </div>
        </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    attachment: 'Attachment',
                    done: 'Done',
                    send_a_stamp: 'Send a Stamp'
                }
            },
            ja: {
                message: {
                    attachment: '添付',
                    done: '完了',
                    send_a_stamp: 'スタンプを送る'
                }
            }
        }
    },
    props: ['content', 'loadingCount'],
    data() {
        return {
            visible: false,
            selected: "",
            stickers: {
                first: [],
                second: [],
                third: [],
            },
        }
    },
    model: {
        prop : 'loadingCount',
        event : 'input'
    },
    created() {
        let self = this
        this.$emit('input', this.loadingCount + 1)
        axios.get("upload/lists/sticker")
            .then(function (response) {
                $.each(response.data, (key, data) => {
                    switch (data.tab) {
                        case 'brown-cony-and-sally':
                            data.package_id = '11537'
                            self.stickers.first.push(data)
                            break
                        case 'choco-and-friends':
                            data.package_id = '11538'
                            self.stickers.second.push(data)
                            break
                        case 'universtar-bt21':
                            data.package_id = '11539'
                            self.stickers.third.push(data)
                            break
                    }
                })
            })
            .finally(() => self.$emit('input', self.loadingCount - 1))
    },
    methods: {
        selectSticker(sticker) {
            this.selected = sticker;
            this.close();
        },
        close() {
            if (this.selected) {
                // this.write(' <img src="'+this.selected.url+'" data-type="'+this.selected.type+'" data-package-id="'+this.selected.package_id+'" />')
                this.content.push(this.selected)
            }
            this.visible = false
        },
        showModal() {
            this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
        handleChange() {

        },
        clearFile() {
            this.selected = ""
        },
    }
});
