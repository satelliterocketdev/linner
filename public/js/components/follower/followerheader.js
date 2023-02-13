Vue.component('followerheader',{
  props:['indeterminate','checkAll','onCheckAllChange','handleMenuClick', 'filterByEventType', 'filterByStatus'],
  template:`<a-card :bordered="false">
    <div class="row" id="head">
      <div class="col-sm-6">
        <h2>{{ $t('message.followers') }}</h2>
      </div>
      <!-- search section -->
      <div class="col-sm-4 text-left">
        <div class="row">
          <div class="col-md-12">
            <a-input-search
              :placeholder="$t('message.search')"
            />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <a-button type="primary" size="small" class="mt-1 px-2 float-md-right">{{ $t('message.search_option') }}</a-button>
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
          <addtagmodal :btn-label="$t('message.add_tag')" :title="$t('message.tag_settings')" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-real-blue ant-btn ant-btn-default ant-btn-sm" :selected-followers="filterByEventType"/>
          <addscenariomodal :btn-label="$t('message.add_scenario')" :title="$t('message.scenario_settings')" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-green ant-btn ant-btn-default ant-btn-sm" :selected-followers="filterByStatus"/>
          <addrichmenumodal :btn-label="$t('message.add_menu')" :title="$t('message.rich_menu_settings')" btnsize="large" btnclass="mt-1 px-2 btn-prime-loopstep rounded-grey ant-btn ant-btn-default ant-btn-sm"/>
          <a-button type="danger" class="mt-1 px-2 btn-prime-loopstep rounded-red ant-btn ant-btn-default ant-btn-sm">{{ $t('message.blocked') }}</a-button>
        </div>
      </div>
    <!-- start below -->
    <div class="row mt-2" id="lower">
      <div class="col-3">
        <div class="row">
          <div class='col-md-3'>
            <a-checkbox
              @click="onCheckAllChange"
              :checked="checkAll"
            ></a-checkbox>
          </div> 
          <div class='col-md-4'>&nbsp;</div>
          <div class='col-md-5'>
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
        </div>
      </div>
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.added_date') }}<a-icon type="down" />
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
        <div class='col'>
          <a-dropdown>
            <a class="ant-dropdown-link" href="#">{{ $t('message.source') }}<a-icon type="down" />
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
    </div>
  </a-card>`
})