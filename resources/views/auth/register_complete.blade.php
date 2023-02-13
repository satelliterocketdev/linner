@extends('auth.main')
@section('content')
    <div id="app">
        <div class="bg-white border rounded p-3">
            <div class="row px-1 align-items-center ">
                <div class="col-sm-8 align-items-center">
                    <div class="row justify-content-end align-items-center">
                        <div>
                            <a-modal :centered="true" v-model="visible" @ok="handleOk" :width="700" :footer="null" :destroyOnClose="true">
                                    <div class="container h-100">
                                            <div class="row justify-content-center align-items-center h-100">
                                                <div class="container-fluid">
                                                    <center>
                                                        @if(session()->get( 'data' ))
                                                            <div class="login-wrapper alert alert-danger" role="alert">
                                                                {{ session()->get( 'data' ) }}
                                                            </div>
                                                        @endif
                                                        <div class="panel-heading">
                                                            <h3 class="title">{{ Lang::get('register.register_completed_title') }}</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <p class="phrase_one">{{ Lang::get('register.register_completed_phrase_one') }}</p>
                                                            <p class="phrase_two">{{ Lang::get('register.register_completed_phrase_two') }}</p>
                                                        </div>
                                                        <div class="col text-center">
                                                        <a href="{{ route('plan') }}" class="btn choose_plan">{{ Lang::get('register.choose_plan') }}</a>
                                                        </div>
                                    
                                                    </center>
                                                </div>
                                    
                                            </div>
                                        </div>
                            </a-modal>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
    const messages = {
        en: {
            message: {
                auto_reply_message: 'auto reply message',
                new: 'new',
            }
        },
        ja: {
            message: {
                auto_reply_message: '自動応答メッセージ',
                new: '新規',
            }
        }
    }
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el: '#app',
        data: {
            data: [],
            filterData: [],
            selected: [],
            disableDelete: true,
            visible: false,
        },
        beforeMount() {
            this.showModal()
        },
        methods: {
            showModal() {
                this.visible = true
            },
            handleOk(e) {
                this.visible = true
            },
        }
    });
</script>
@endsection


@section('css-styles')
  <style>
  .title{
      color:#65CEFA;
      font-size: 2.5rem;
      margin: 0 0 20px;
  }
  .phrase_one{
     color: blue;
     font-size: 1.8rem;
     margin: 0;
  }
  .phrase_two{
    color: blue;
    margin: 0 0 20px;
  }
  .choose_plan{
    background: #FF6663;
    color: #fff;
  }
  </style>
@endsection

