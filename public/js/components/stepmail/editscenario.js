Vue.component ('edit-scenario', {
    template: 
    `<div>
    <button @click="showModal" v-bind:class="buttonclass">{{$t('message.edit')}}</button>
    <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null">
        <div>
            Scenario Delivery Set
            <b><input class="borderless-input form-control" type="text" placeholder="Title" style="font-size: 24px" v-model="name"></b>
        </div>
        <div class="row align-items-center">
            <div class="col align-items-center">
                Served
            </div>
            <div class="col align-items-center">
                Tags
            </div>
            <div class="col">
                <div class="row justify-content-end align-items-center">
                    <message-target> </message-target>
                    <button class="btn rounded-white m-1">SendToAll</button>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col align-items-center">
                Delivery completion
            </div>
            <div class="col align-items-center">
                Tags
            </div>
            <div class="col align-items-center">
                <div class="row justify-content-end">
                  <message-action></message-action>
                </div>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-6 align-items-center">
                Delivery Statement
            </div>
            <div class="col-sm-6 align-items-center text-right">
                <div class="row justify-content-end">
                  <new-message class="mr-1 ml-1" :type="type"></new-message>
                  <confirmation-delete class="mr-1 ml-1"></confirmation-delete>
                </div>
            </div>
        </div>
        <div id="scenario-list" class="p-2 m-2 fixed-container">
            <div v-for="(data,key) in this.data" :key="key" data-spy="scroll">
                <editscenario-list></editscenario-list>
            </div>
        </div>
        <div class="footer">
            <div class="row justify-content-center">
                <button class="btn rounded-green m-1" @click="update">Update</button>
            </div>
        </div>
    </a-modal>
    </div>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: {
                message: {
                    edit: 'Edit',
                    scenarioDeliverySet: 'Scenario Delivery Set',
                }
            },
            ja: {
                message: {
                    edit: '編集',
                    scenarioDeliverySet: 'Scenario Delivery Set',
                }
              }
        }
    },

    props: ['data', 'btnclass', 'type'],
    data() {
      return {
        id: '',
        name: '',
        visible: false,
        buttonclass: "btn mx-1 " + this.btnclass,
      }
    },
    methods: {
      showModal() {
        this.visible = true
        this.id = this.data.id
        this.name = this.data.name
      },
      handleOk(e) {
        this.visible = false
      },
      update() {
        self = this
        axios.put('/stepmail/' + this.id, {
            name: this.name
        })
        .then(function(response){
            self.$parent.$parent.loadScenario()
            self.visible = false
        })
      }
    }
});
