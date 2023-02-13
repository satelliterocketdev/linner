Vue.component ('previously-uploaded', {
    template: 
    `<div>
    <button @click="showModal" class="btn m-1 btn-primary btn-block">
        {{$t('message.select_from_content')}}
    </button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="500" :footer="null" :destroyOnClose="true">
        <div class="slide-wrapper row p-4">
            <div class="owl-carousel mb-2">
                <div v-for="content in firstRow" :key="content.id" class="item" @click="selectItem(content)">
                    <img :src="content.featured_url" :data-value="content.url" />
                    <span class="small" v-if="type=='audio'">{{ content.name }}</span>
                </div>
            </div>

            <div class="owl-carousel mb-2">
                <div v-for="content in secondRow" :key="content.id" class="item" @click="selectItem(content)">
                    <img :src="content.featured_url" :data-value="content.url" />
                    <span class="small" v-if="type=='audio'">{{ content.name }}</span>
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
        <div class="footer">
            <div class="row justify-content-center pt-2">
                <button class="btn rounded-green" @click="close">{{$t('message.done')}}</button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    done: 'Done',
                    send_video: 'Send a Video',
                    add_video: 'Add Video',
                    playback_option: 'Upload Image',
                    select_from_content: 'Previously Uploaded',
                    configuration: 'Configuration'
                } 
            },
            ja: { 
                message: { 
                    done: '完了',
                    send_video: '動画を送る',
                    add_video: '動画を追加',
                    playback_option:  '再生オプション',
                    select_from_content: 'コンテンツから選択', //Previously Uploaded
                    configuration: '設定'
                } 
            }
        }
    },
    props: ["title", 'type'],
    data() {
        return {
            visible: false,
            contents: {},
            currentPage: 1,
            totalPage: 1,
            selectedItem: null,
            itemPerPage: 3,
            slider: null,
            isFirstRow: true,
            firstRow: [],
            secondRow: [],
            tmp: {},
        }
    },
    methods: {
        close() {
            if (this.selectedItem) {
                this.$emit('callback', this.selectedItem)
            }

            this.visible = false
        },
        selectItem(item) {
            this.selectedItem = item
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
            self = this
            this.currentPage = 1
          axios.get("upload/lists/" + this.type)
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

              // delay
              setTimeout(function(){
                  self.slider = $(".owl-carousel").owlCarousel({
                    margin: 8,
                    items: self.itemPerPage,
                    slideBy: self.itemPerPage,
                    mouseDrag: false,
                    touchDrag: false,
                    pullDrag: false,
                    freeDrag: false,
                    dots: false,
                    responsive: {
                        0: {
                            items: self.itemPerPage,
                            slideBy: self.itemPerPage
                        }
                    }
                })
              }, 100)
          })
          this.visible = true
        },
        handleOk(e) {
          this.visible = false
        },
    }
});