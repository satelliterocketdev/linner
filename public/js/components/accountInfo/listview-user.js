Vue.component ('listview-user', {
    template: 
    `<div class="bg-white rounded border my-1">
        <div class="row justify-content-between align-items-center p-3">
            <div class="col-2 text-center font-size-table wordbreak-all px-small"> 
                <b>{{ data.name }}</b>
            </div>
            <div class="col-4 text-center font-size-table wordbreak-all px-small">           
                {{ data.email }}
            </div>
            <div class="col-4 text-center font-size-table wordbreak-all px-small" style="text-overflow: ellipsis; white-space: nowrap; overflow:hidden;">
                <span v-for="(role, index) in data.roles">
                    <span v-if="role">{{ role.name }} : {{role.description }}</span><span v-if="index + 1 < data.roles.length">、 </span>
                    <span v-else>{{ $t('message.user') }}</span>
                </span>
            </div>
            <div class="col-2 px-small">
                <div class="row justify-content-center align-items-center">
                    <edit-user v-if="isAdmin" :user="data" :reload-user="reloadUser" v-model:loading-count="loadingCountData"></edit-user>
                    <confirmation-delete v-if="isAdmin && data.id != userId" @delete-user="confirmDelete"></confirmation-delete>
                    <!-- <preview-user v-if="!!isAdmin && data.id == userId" :user-id="data.id" :reload-user="reloadUser"></preview-user> -->
                </div>
            </div>
        </div>
    </div>`,
    i18n: {
      messages: {
        en: {
          message: {
            administrator: 'Administrator',
            user: 'User',
            error: 'A system error has occurred'
          }
        },
        ja: {
          message: {
            administrator: '管理者',
            user: 'ユーザー',
            error: 'システムエラーが発生しました'
        }
        }
      }
    },
    model: {
        prop: 'loadingCount',
        event: 'input'
    },
    props:['data', 'reloadUser','isAdmin', 'userId', 'loadingCount'],
    data() {
        return {
            defaults: {},
            id: '',
            name: '',
            email: '',
            buttonclass: "btn mx-1 " + this.btnclass,
            BtnSuccess: "btn-success small-text",
            BootstrapRed: "btn-danger",
            disableDelete: true,
        }
    },
    methods: {
        render() {
            this.id = this.data.id
            this.name = this.data.name
            this.email = this.data.email
        },
        confirmDelete() {
            self = this
            this.$emit('input', this.loadingCount + 1)
            axios.delete('accountinfo/' + this.id )
            .then((response) => {
              self.reloadUser()
            }).catch((error) => {
              console.log(error)
              alert(this.$t('message.error'))
            })
            .finally(() =>this.$emit('input', this.loadingCount - 1))
          },
    },
    filters: {
        count() {
            return 0
        }
    },
    created() {
        this.render()
    },
    updated() {
        this.render()
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
    }
});