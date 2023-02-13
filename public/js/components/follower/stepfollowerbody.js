Vue.component('stepfollowerbody',{
  props:['followers','tags','onChange'],
  template:`
  <div class="mt-4" >
    <a-card class="mt-2">
      <div class="row">
        <div class="col-md-2">
          <table class="table table-hover">
            <tbody>
              <tr v-for="(tag,key) in tags" :key="key">
                <td style="text-align: center">{{tag.title}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-10">
          <div v-for="(follower,key) in followers" :key="key">
            <div class="row">
              <!--checkbox ava and name -->
              <div class="col-md-12 px-3 py-1">
                <div class="row d-flex justify-content-between align-items-center border rounded shadow bg-white px-3 py-2">
                  <div class="col-md-2" style="text-align: center">
                    <a-checkbox @change="onChange" class="mr-1"></a-checkbox>
                    <a-avatar style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="follower.picture==null" />
                    <a-avatar v-bind:src="follower.picture" v-if="follower.picture!=null" />
                  </div>
                  <div class="col-md-2" style="text-align: center">
                    {{follower.display_name}}
                  </div>
                  <div class="col-md-2" style="text-align: center">
                    <div v-if="follower.status !== null">
                      {{follower.status}}
                    </div>
                    <div v-else>
                      {{ $t('message.not_published') }}
                    </div>
                    {{follower.status}}
                  </div>
                  <div class="col-md-2" style="text-align: center">
                    tags
                  </div>
                  <div class="col-md-2" style="text-align: center">
                    <a-button class="rounded-real-blue" size="small" >{{ $t('message.user_info') }}</a-button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </a-card>
  </div>`,
  methods:{
    timeDataFormated:function(timestamp){
      let date = new Date(parseInt(timestamp))
      return date.getFullYear()+"-"+date.getMonth()+"-"+date.getDay();
    }
  },
  beforeMount(){
    console.log("dasd",this.followers);
  }
})