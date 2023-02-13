Vue.component('stepfollowerheader',{
  props:['indeterminate','checkAll','onCheckAllChange','handleMenuClick'],
  template:`<a-card :bordered="false">
    <div class="row" id="head">
      <div class="col-sm-6">
        <h2>{{ $t('message.followers') }}</h2>
      </div>
      <!-- search section -->
      <div class="col-sm-4  text-left">
        <div class="row">
          <div class="col-md-12">
            <a-input-search
              placeholder='友達検索'
              style="width: 200px"
            />
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 offset-5">
            <a-button type="primary" size="small" class="mt-1 px-2">{{ $t('message.search_option') }}</a-button>
          </div>
        </div>
      </div>
      <div class="col-sm-2  text-left">
        <div class="row">
            <div class="col d-flex justify-content-end">
              <a-dropdown>
                  <a-menu slot="overlay" @change="handleMenuClick">
                    <a-menu-item key="1">{{ $t('message.sort_by_tag') }}</a-menu-item>
                    <a-menu-item key="2">{{ $t('message.sort_by_scenario') }}</a-menu-item>
                    <a-menu-item key="3">{{ $t('message.sort_by_source') }}</a-menu-item>
                    <a-menu-item key="4">{{ $t('message.show_only_new_friends_today') }}</a-menu-item>
                    <a-menu-item key="5">{{ $t('message.show_only_new_friends_week') }}</a-menu-item>
                    <a-menu-item key="6">{{ $t('message.show_only_friend_with_unread_messages') }}</a-menu-item>
                  </a-menu>
                  <a-button style="margin-left: 8px">{{ $t('message.sort_option') }}<a-icon type="down" />
                  </a-button>
                </a-dropdown>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col d-flex justify-content-end">
              <a-dropdown>
                <a-menu slot="overlay" @click="handleMenuClick">
                  <a-menu-item key="1"><a-icon type="user" />1st menu item</a-menu-item>
                  <a-menu-item key="2"><a-icon type="user" />2nd menu item</a-menu-item>
                  <a-menu-item key="3"><a-icon type="user" />3rd item</a-menu-item>
                </a-menu>
                <a-button style="margin-left: 8px">{{ $t('message.show_blocked_users') }}<a-icon type="down" />
                </a-button>
              </a-dropdown>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-1" id="middle">
        <div class="col-md-6 form-inline">
          <addtagmodal btn-label="タグ追加" title="タグ設定" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-real-blue ant-btn ant-btn-default ant-btn-sm"/>
          <addscenariomodal btn-label="シナリオ追加" title="シナリオ設定" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-green ant-btn ant-btn-default ant-btn-sm"/>
          <addrichmenumodal btn-label="メニュー追加" title="リッチメニュー設定" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-grey ant-btn ant-btn-default ant-btn-sm"/>
          <a-button type="mt-1 px-2 btn-prime-loopstep rounded-red ant-btn ant-btn-default ant-btn-sm" class="mt-1 px-2">{{ $t('message.blocked') }}</a-button>
        </div>
      </div>
    <!-- start below -->
    <div class="row mt-2" style="text-align: center" id="lower">
        <div class='col-md-2'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.tag') }}<a-icon type="down" /></a>
            <a-menu slot="overlay">
              <a-menu-item>
                <a href="javascript:;">1st menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">2nd menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">3rd menu item</a>
              </a-menu-item>
            </a-menu>
            </a-dropdown>
        </div>
        <!-- drop 1 -->
        <div class='col'>
          <a-checkbox
            :indeterminate="this.indeterminate"
            @change="onCheckAllChange"
            :checked="checkAll"
          ></a-checkbox>
        </div> 
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.name') }}<a-icon type="down" />
            </a>
            <a-menu slot="overlay">
              <a-menu-item>
                <a href="javascript:;">1st menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">2nd menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">3rd menu item</a>
              </a-menu-item>
            </a-menu>
          </a-dropdown>
        </div>
        <!--end drop 1 -->
        <!-- drop 2 -->
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.state') }}<a-icon type="down" />
            </a>
            <a-menu slot="overlay">
              <a-menu-item>
                <a href="javascript:;">1st menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">2nd menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">3rd menu item</a>
              </a-menu-item>
            </a-menu>
          </a-dropdown>
        </div>
        <!--end drop 2 -->
        <!-- drop 3 -->
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.tag') }}<a-icon type="down" />
            </a>
            <a-menu slot="overlay">
              <a-menu-item>
                <a href="javascript:;">1st menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">2nd menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">3rd menu item</a>
              </a-menu-item>
            </a-menu>
            </a-dropdown>
        </div>
        <!--end drop 3 -->
        <!-- drop 4 -->
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.talk') }}<a-icon type="down" />
            </a>
            <a-menu slot="overlay">
              <a-menu-item>
                <a href="javascript:;">1st menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">2nd menu item</a>
              </a-menu-item>
              <a-menu-item>
                <a href="javascript:;">3rd menu item</a>
              </a-menu-item>
            </a-menu>
            </a-dropdown>
        </div>
        <!--end drop 4 -->
    </div>
  </a-card>`
})