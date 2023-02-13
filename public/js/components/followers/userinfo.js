Vue.component ('user-info', {
    template: `
    <a-modal :centered="true" v-model="visible" width="100%" style="max-width: 900px;" :footer="null" v-if="userInfo">
        <div class="row justify-content-center">
            <a-avatar :size="60" style="color: var(--text-second); backgroundColor:var(--color-fink);" icon="user" v-if="userInfo.avator_picture==null" ></a-avatar>
            <a-avatar :size="60" :src="userInfo.avator_picture" v-if="userInfo.avator_picture!=null" ></a-avatar>
        </div>
        <div class="row justify-content-center">
            <span>{{ userInfo.display_name }}</span>
        </div>
        <hr>
        <div class="row justify-content-between">
            <div class="col-sm-5 align-items-center content-1st">
                <div class="row">
                    <div class="col mt-2"> {{$t('message.friend_added_date')}} </div>
                    <div class="col mt-2"> {{ userInfo.timedate_followed }} </div>
                </div>
                <div class="row">
                    <div class="col mt-2"> {{$t('message.tags')}} </div>
                    <div class="col mt-2"> {{ userInfo.tags }} </div>
                </div>
                <div class="row">
                    <div class="col mt-2"> {{$t('message.notes')}} </div>
                    <div class="col mt-2"> </div>
                </div>
                <a-textarea v-model="userInfo.notes" :rows="3" class="border my-2 w-100" @blur="updateNotes"></a-textarea>
            </div>
            <div class="col-sm-7">
                <div class="row py-1">
                    <div class="col-4 mt-2">
                        <span>{{$t('message.scenario_delivery_status')}} </span>
                    </div>
                    <div class="col-8 mt-2"> 
                        <div class="row" v-for="delivery in userInfo.delivery_status" :key="delivery.id">
                            <div class="col-6"><span>{{ delivery.name }}</span></div>
                            <div class="col-5" style="text-align:right;">
                                <span>{{ delivery.status == '1' ? $t('message.delivery_status_done') : $t('message.delivery_status_doing') }} </span>
                            </div>
                            <div class="col-1"></div>
                        </div>
                    </div>
                </div>
                <div class="row py-1">
                    <div class="col-4 mt-2">
                        <span>{{$t('message.delivery_tester')}} </span>
                    </div>
                    <div class="col-4 mt-2">
                        <span>{{ userInfo.is_tester == 1 ? $t('message.is_tester') : $t('message.is_not_tester')}} </span>
                    </div>
                    <div class="col-4 mt-2 d-flex justify-content-center">
                        <button v-if="userInfo.is_tester == 1" class="btn mx-1 btn-danger small-text" @click="removeTester">{{ $t('message.remove_tester') }}</button>
                        <button v-else class="btn mx-1 btn-success small-text" @click="addTester">{{ $t('message.add_tester') }}</button>
                        <button class="btn mx-1 btn-info small-text" @click="goTalk" :disabled="!userInfo.source_user_id">{{ $t('message.transition') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="row justify-content-between pt-3">
                <div></div>
                <button class="btn mx-1 rounded-green small-text px-4" @click="done">{{ $t('message.confirm') }}</button>
                <follower-block-button :targets="blocktarget" class="mt-1 px-2 "></follower-block-button>
            </div>
        </div>
    </a-modal>`,
    i18n: { // `i18n` option, setup locale info for component
        messages: {
            en: { 
                message: { 
                    friend_added_date: 'Friend Added Date',
                    tags: 'Tags',
                    notes: 'Notes',
                    scenario_delivery_status: 'Scenario Delivery',
                    delivery_status_doing: 'Doing',
                    delivery_status_done: 'Done',
                    delivery_tester: 'Tester',
                    is_tester: 'Yes',
                    is_not_tester: 'No',
                    add_tester: 'Add',
                    remove_tester: 'Remove',
                    transition: 'Talk',
                    confirm: 'Confirm',
                    block_user:'Block User',
                    getting_data: "Getting Data",
                    fetch_fail: "Fail to fetch data"
                }
            },
            ja: { 
                message: { 
                    friend_added_date: '友達追加日',
                    tags: 'タグ',
                    notes: 'メモ',
                    scenario_delivery_status: '配信状態',
                    delivery_status_doing: '配信中',
                    delivery_status_done: '配信完了',
                    delivery_tester: 'テスト配信対象',
                    is_tester: '対象',
                    is_not_tester: '対象外',
                    add_tester: '追加',
                    remove_tester: '除外',
                    transition: '表示',
                    confirm: '完了',
                    block_user:'ブロックする',
                    getting_data: "データの取得",
                    fetch_fail: "データの取得に失敗しました。"
                } 
            }
        }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props: ['loadingCount'],
    data() {
      return {
      visible: false,
      userInfo: null,
      loading: false,
      }
    },
    computed:{ 
        // block-buttonのインターフェイス用に加工
        blocktarget(){
            return [this.userInfo]
        }
    },
    methods: {
        openNotificationWithIcon(type, message, desc) {
          this.$notification[type]({
            message: message,
            description: desc,
          });
        },
        show(followerId) {
            this.userInfo = null
            this.getData(followerId)
            this.visible = true
        },
        translateDeliveryStatus(status){
            return status == '1' ? i18n.message.delivery_status_done : i18n.message.delivery_status_doing
        },
        getData(id){
            this.$emit('input', this.loadingCount + 1)
            axios.get("follower/user-info/" + id)
            .then((res)=>{
                this.userInfo = res.data
            })
            .catch(e=> this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateInfo(data, callback){
            this.$emit('input', this.loadingCount + 1)
            axios.put("follower/user-info/" + this.userInfo.id, data)
            .then(callback)
            .catch(e=> this.openNotificationWithIcon('error',this.$t('message.getting_data'),this.$t('message.fetch_fail')))
            .finally(() => this.$emit('input', this.loadingCount - 1))
        },
        updateNotes(e){
            let data = { notes : this.userInfo.notes }
            this.updateInfo(data, (res) => {this.userInfo.notes = res.data.notes })
        },
        addTester(){
            let data = { is_tester : '1' }
            this.updateInfo(data, (res) => {this.userInfo.is_tester = res.data.is_tester })
        },
        removeTester(){
            let data = { is_tester : '0' }
            this.updateInfo(data, (res) => {this.userInfo.is_tester = res.data.is_tester })
        },
        goTalk() {
            window.location.href = '/talk/' + this.userInfo.id;
        },
        done(){
            this.visible = false
        },
    }
});
