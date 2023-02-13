Vue.component('increase-accounts', {
  template:
    `<div class="card w-100 m-0 mx-sm-2">
      <div class="card-body align-items-center d-flex justify-content-center">
        <div class="row upgradecolor">
          <center>
            <p>{{ $t('message.increase_accounts') }}</p>
            <a href="/plan" class="btn btn-primary btn-sm px-2 py-1">
              {{$t('message.upgrade')}}
            </a>
          </center>
        </div>
      </div>
    </div>`,
  props: ['data', 'btnclass', 'type'],
  data() {
    return {
      visible: false
    }
  },
});
