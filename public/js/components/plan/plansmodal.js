Vue.component('plansmodal',{
  props:['btnsize','btnclass','btnLabel', 'plans', 'currentIndex', 'submit'],
  data() {
    return {
      visible: false,
      confirmLoading: false,
      isLimited:false,
    }
  },
  methods: {
    showModal() {
      this.visible = true
    },
    sendData() {
      this.submit(this.plans[this.currentIndex])
      this.visible = false
    }
  },
  template:`
  <div>
    <a-button style="margin-bottom: 24px" type="default" :size="this.btnsize" :class="this.btnclass"  @click="showModal">{{this.btnLabel}}</a-button>
    <a-modal
      :visible="visible"
      :footer="null"
      :centered="true"
      v-model="visible"
      width="60%">
      <nav class="navbar navbar-expand-md navbar-light">      
        <div class="collapse navbar-collapse justify-content-around">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" v-bind:class='{active:currentIndex === 0}' v-on:click="currentIndex = 0">START</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" v-bind:class='{active:currentIndex === 1}' v-on:click="currentIndex = 1">BUSINESS</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" v-bind:class='{active:currentIndex === 2}' v-on:click="currentIndex = 2">EXPERT</a>
            </li>
          </ul>
        </div>
      </nav>
      <div class="d-flex justify-content-center">
        <div class="flex-column">
          <div class="p-2 text-center"><h1>{{plans[currentIndex].title}}</h1></div>
          <div class="p-2"><h3>{{plans[currentIndex].details}}</h3></div>
        </div>
      </div>
      <div class="row p-2">
        <div class="col d-flex justify-content-center">
          <a-button class="rounded-green" @click="sendData">{{ $t('message.select_this_plan') }}</a-button>
        </div>
      </div>
    </a-modal>
  </div>`
})
