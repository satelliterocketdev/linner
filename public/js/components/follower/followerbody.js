Vue.component('followerbody',{
  props:['followers','onChange', 'checkAll', 'selectedFollowers', 'pfUsers','getConversionTitle', 'getScenarioName', 'getTagManagementTitle'],
  template:`<div class="mt-4" >
    <a-card class="mt-2" v-for="(pfUser,key) in pfUsers" :key="key">
      <div class="row" style="align-items: center;">
        <!--checkbox ava and name -->
        <div class="col-3" style="border-right:#e4e4e4 solid 1px;">
          <div class="row pr-3" style="align-items: center;">
            <div class="col-md-3">
              <a-checkbox @change="onChange" :v-model="selectedFollowers" :value="followers[key]" class="mr-1"></a-checkbox>
            </div>
            <div class="col-md-4">
              <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="pfUser.picture==null" />
              <a-avatar :src="pfUser.picture" v-if="pfUser.picture!=null" />
            </div>
            <div class="col-md-5">
              {{pfUser.display_name}}
            </div>
          </div>
        </div>
        <div class="col">
          {{timeDataFormated(followers[key].timedate_followed)}}
        </div>
        <div class="col">
          {{ getConversionTitle(followers[key].source_user_id) }}
        </div>
        <div class="col">
          {{ getScenarioName() }}
        </div>
        <div class="col">
          {{ getTagManagementTitle(key) }}
        </div>
        <div class="col">
          <a-button v-bind:class="{ disabled: !followers[key].source_user_id }" v-on:click="goToTalks(followers[key].id, followers[key].source_user_id)" class="rounded-real-blue" size="small">{{ $t('message.user_info') }}</a-button>
        </div>
      </div>
    </a-card>
  </div>`,
  methods:{
    timeDataFormated:function(timestamp){
      let date = new Date(parseInt(timestamp))
      return date.getFullYear()+"-"+date.getMonth()+"-"+date.getDay();
    },
    goToTalks(id, sourceUserId) {
      if (sourceUserId != null) {
        window.location.href = '/talk/' + id;
      }
    }
  },
  beforeMount(){
    console.log("dasd",this.followers);
  }
})