Vue.component('select-richmenu-layout', {
    template:`
    <div>
        <button type="button" @click="showModal" v-bind:class="buttonclass">{{$t('message.change')}}</button>
        <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
            <div class="row">
                <div v-for="i in 9" class="col-6 col-sm-4 pt-3">
                    <img :src="'img/rich_menu/layout' + i + '.png'" @click="imageSelect(i)" :class="(type === i ? 'selected' : '')" style="width:100%; max-width:200px;" v-bind:alt="'layout' + i">
                </div>
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout2.png" @click="imageSelect(2)" style="width:100%; max-width:200px" alt="layout2">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout3.png" @click="imageSelect(3)" style="width:100%; max-width:200px" alt="layout3">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout4.png" @click="imageSelect(4)" style="width:100%; max-width:200px" alt="layout4">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout5.png" @click="imageSelect(5)" style="width:100%; max-width:200px" alt="layout5">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout6.png" @click="imageSelect(6)" style="width:100%; max-width:200px" alt="layout6">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout7.png" @click="imageSelect(7)" style="width:100%; max-width:200px" alt="layout1">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout8.png" @click="imageSelect(8)" style="width:100%; max-width:200px" alt="layout2">-->
<!--                </div>-->
<!--                <div class="col-6 col-sm-4 pt-3">-->
<!--                    <img src="img/rich_menu/layout9.png" @click="imageSelect(9)" style="width:100%; max-width:200px" alt="layout3">-->
<!--                </div>-->
            </div>
          <div class="footer">
              <div class="row justify-content-center">
                  <button type="button" class="btn rounded-green m-1" @click="onSelect" >{{$t('message.select')}}</button>
              </div>
          </div>
        </a-modal>
    </div>
    `,
        i18n: { // `i18n` option, setup locale info for component
            messages: {
                en: {
                    message: {
                        change: "Change",
                        select: "Select",
                    }
                },
                ja: {
                    message: {
                        change: "変更",
                        select: "選択",
                    }
                }
            }
        },

        props: ["layoutType", "selectLayoutType"],
        data() {
            return {
                visible: false,
                buttonclass: "btn rounded-white m-1 " + this.btnclass,
                type: this.layoutType
            }
        },
    methods: {
        imageSelect(layout_type) {
            this.type = layout_type
        },
        showModal() {
            this.visible = true;
        },
        handleOk(e) {
            this.visible = false

        },
        onSelect() {
            this.selectLayoutType(this.type)
            this.visible = false;
        }
    }
}
);