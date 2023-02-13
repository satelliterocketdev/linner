Vue.component ('select-draft', {
    template: 
    `<div>
    <button type="button" @click="showModal" class="btn rounded-cyan btn-block">{{$t('message.create_from_draft')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="750" :footer="null" :destroyOnClose="true">
        <div class="d-flex justify-content-center" style="font-size: 18px">
            {{$t('message.draft')}}
        </div>
        <div id="draft-containter">
            <div class="slide-wrapper row p-4">
                <div class="owl-carousel mb-2">
                    <div v-for="content in firstRow" :key="content.id" class="item" @click="selectItem(content)">
                        <div class="card card-item">
                            <div class="card-body" style="max-height:150px; overflow:hidden;">
                                <h5 class="card-title">{{ content.title }}</h5>
                                <div class="card-text" v-html="content.content_message"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="owl-carousel mb-2">
                    <div v-for="content in secondRow" :key="content.id" class="item" @click="selectItem(content)">
                        <div class="card">
                            <div class="card-body" style="max-height:150px; overflow:hidden;">
                                <h5 class="card-title" style="overflow: hidden;">{{ content.title }}</h5>
                                <div class="card-text" v-html="content.content_message"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div style="display: block; margin: 0 auto;">
                    <span @click="prevSlide"><i class="fa fa-chevron-left"></i></span> &nbsp;
                    <span>{{ currentPage }} / {{ totalPage }}</span> &nbsp;
                    <span @click="nextSlide"><i class="fa fa-chevron-right"></i></span>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="d-flex justify-content-center py-3">
                <button class="btn mx-1 rounded-green" @click="close">{{$t('message.close')}}</button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    delivery_statement_setting: 'Delivery Statement Setting',
                    title: "Title",
                    delivery_timing: '',
                    immediately_after_delivery: 'Immediately after delivery',
                    specified_time: ' Specified Time',
                    normal_delivery_statement: 'Create Message',
                    carousel: 'Create Carousel',
                    questionnaire: 'Add Survey',
                    map: 'Add Map',
                    introduction: 'Add Contact',
                    save_as_draft: 'Save as draft',
                    create_from_draft: 'Select draft',
                    save_as_template: 'Save as template',
                    delivery_registration: 'Send / Schedule send',
                    save_and_exit: 'Save and Exit',
                    draft: 'Draft Message',
                    close: 'Close',
                    add_message: 'Add Message' //New / Edit
                }
            },
            ja: {
                message: {
                    delivery_statement_setting: '配信文設定',
                    delivery_timing: '',
                    immediately_after_delivery: '配達直後',
                    specified_time: ' 時間指定',
                    normal_delivery_statement: '通常配信文', //Create Message
                    carousel: 'カルーセル',
                    questionnaire: 'アンケート', //Add Survey
                    map: 'マップ',
                    introduction: '紹介', //Add Contact
                    save_as_draft: '下書きを保存',
                    create_from_draft: '下書きから作成', //Select draft
                    save_as_template: 'テンプレート保存',
                    delivery_registration: '配達登録', //Send / Schedule send
                    save_and_exit: '保存して終了',
                    draft: '下書き',
                    close: '閉じる',
                    add_message: "メッセージを追加" //New / Edit
                }
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['data', 'message', 'loadingCount'],
    data() {
        return {
            visible: false,
            contents: [],
            currentPage: 1,
            totalPage: 1,
            selectedItem: null,
            itemPerPage: 3,
            slider: null,
            isFirstRow: true,
            firstRow: [],
            secondRow: [],
        }
    },
    methods: {
        close() {
            // Object.assign(this.data, this.selectedItem)
            // $.extend(this.data, this.selectedItem)

            // switch (this.data.content_type) {
            //     case 'message':
            //         // get tinymce current id
            //         tinymce_id = $('#content-panel').find('textarea').attr('id')
            //         // // reload tinymce
            //         this.message.EditorManager.execCommand('mceRemoveEditor', true, tinymce_id)
            //         // // reinitialize tinymce
            //         this.message.EditorManager.execCommand('mceAddEditor', true, tinymce_id)
            //         break;
            // }

            this.visible = false
        },
        selectItem(item) {
            this.selectedItem = item
            this.$emit('update-from-draft', this.selectedItem)
            this.deleteDraftMessage()
            this.handleOk()
        },
        prevSlide() {
            if (this.currentPage == 1) return
            this.currentPage--
            this.slider.trigger('prev.owl.carousel')
        },
        nextSlide() {
            if (this.currentPage >= this.totalPage) return
            this.currentPage++
            this.slider.trigger('next.owl.carousel')
        },
        showModal() {
            this.fillDraft()
            this.visible = true
        },
        handleOk(e) {
            console.log(e);
            this.visible = false
        },
        initializeOwlCarousel() {
            this.slider = $('.owl-carousel').owlCarousel({
                margin: 8,
                items: this.itemPerPage,
                slideBy: this.itemPerPage,
                mouseDrag: false,
                touchDrag: false,
                pullDrag: false,
                freeDrag: false,
                dots: false,
                responsive: {
                    0: {
                        items: this.itemPerPage,
                        slideBy: this.itemPerPage
                    }
                }
            })
        },
        fillDraft() {
            self = this
            this.currentPage = 1
            this.$emit('input', this.loadingCount + 1)
            axios.get("stepmail/draft_messages")
            .then(function(response){
                slides = response.data
                self.contents = slides
                self.totalPage = Math.ceil((response.data.length / self.itemPerPage) / 2)
                self.totalPage = self.totalPage > 0 ? self.totalPage : 1

                // reset content
                self.firstRow = []
                self.secondRow = []
                self.isFirstRow = true

                i = 1;
                $.each(slides, function(key, value){
                    if (self.isFirstRow) {
                        self.firstRow.push(value)
                        if (i===3) self.isFirstRow = false
                        } else {
                        self.secondRow.push(value)
                        if (i===3) self.isFirstRow = true
                    }
                    if (i===3) {
                        i = 0
                    }
                    i++
                })

                Vue.nextTick(function(){
                    self.initializeOwlCarousel()
                }.bind(self))
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        deleteDraftMessage() {
            let self = this
            this.$emit('input', this.loadingCount + 1)
            axios.delete('stepmail/draft_message/' + this.selectedItem.id)
            .then(function (response) {
                self.fillDraft()
            })
            .finally(() => this.$emit('input', this.loadingCount - 1))
        }
    }
});