@extends('layouts.app')
@section('content')
  <div id="password">
    <div class="card mb-3">
      <div class="ant-card-body">
          <div class="h5 mb-4">@{{ $t('message.change_password') }}</div>
          <form class="form-horizontal" method="post" action="{{route('password')}}">
          {{ csrf_field() }}
              <div>
                  <div class="mb-4 form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                      <div class="row">
                          <div class="col-sm-12 col-md-4">
                              <label for="password" class="control-label">@{{ $t('message.new_pass') }}</label>
                          </div>
                          <div class="col-sm-12 col-md-8">
                              <input type="password" id="password" class="form-control" name="password" required>
                              @if ($errors->has('password'))
                              <div>
                                  <span class="help-block">
                                      <strong>{{ $errors->first('password') }}</strong>
                                  </span>
                              </div>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="mb-4 form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                      <div class="row">
                          <div class="col-sm-12 col-md-4">
                              <label for="password-confirm" class="control-label">@{{ $t('message.confirm_pass') }}</label>
                          </div>
                          <div class="col-sm-12 col-md-8">
                              <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required>
                              @if ($errors->has('password_confirmation'))
                              <div>
                                  <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                  </span>
                              </div>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="text-center">
                      <button type="submit" class="btn btn-primary">@{{ $t('message.send') }}</button>
                  </div>
              </div>
          </form>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/vue2-filters/dist/vue2-filters.min.js"></script>
<script>

const messages = {
    en: {
        message: {
            change_password: 'Change Password',
            logout: 'Logout',
            new_pass: 'New Password',
            confirm_pass: 'Confirm New Password',
            send: 'Send',
        }
    },
    ja: {
        message: {
            change_password: 'パスワード変更',
            logout: 'ログアウト',
            new_pass: '新しいパスワード',
            confirm_pass: 'パスワード再入力',
            send: '送信',
        }
    }
}

const i18n = new VueI18n({
    locale: '{{config('app.locale')}}', 
    messages, 
})

var password = new Vue({
  i18n,
  el:"#password",
})
</script>
@endsection
