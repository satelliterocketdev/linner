@extends('layouts.app')

@section('content')
    <div id="invitation_email" v-cloak>
        <loading :visible="loadingCount > 0"></loading>
        <div class="bg-white rounded py-3">
            <div class="d-flex justify-content-center px-3 mb-3">
                <h2 class="text-center">@{{$t("message.title")}}</h2>
            </div>
            <div class="d-flex justify-content-center px-3 mb-0 mb-sm-3">
                <invitation-email v-bind:btnclass="'btn btn-outline-dark'" :type="'New'" :reload-invitations="reloadInvitations" v-model:loading-count="loadingCount"></invitation-email>
            </div>
        </div>
        <tutorial :state="tutorial_state"></tutorial>
        <div class="mt-4" >
            <a-card>
                <div class="row m-1 border rounded shadow bg-white col-12" v-for="(invitation, key) in invitations" :key="key">
                    <div class="bg-white col-sm-12 rounder py-2 px-0 mt-2 d-flex align-items-center font-size-table">
                        <p class="col-10 pl-0 pr-2">@{{ invitation.content_message }}</p>
                        <div class="col-2 text-right p-0">
                            <invitation-email v-bind:btnclass="'btn btn-outline-dark'" :type="'Edit'" :reload-invitations="reloadInvitations" :data="invitation" v-model:loading-count="loadingCount"></invitation-email>
                            <confirm-email v-bind:btnclass="'btn btn-outline-success'" :data="invitation" v-model:loading-count="loadingCount"></confirm-email>
                        </div>
                    </div>
                </div>
            </a-card>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="{{asset('js/components/invitation_email/invitation-email.js')}}"></script>
<script src="{{asset('js/components/invitation_email/confirm-email.js')}}"></script>
<script src="{{asset('js/components/tutorial/tutorial.js')}}"></script>
<script>
    const messages = {
        en: {
            message: {
                title: 'Invite people connected via email to LINE',
                send: 'Send'
            }
        },
        ja: {
            message: {
                title: 'メールで繋がった人々を、LINEに招待しましょう',
                send: '送信'
            }
        }
    }
    const i18n = new VueI18n({
        locale: '{{config('app.locale')}}',
        messages,
    })
    var app = new Vue({
        i18n,
        el:"#invitation_email",
        data: {
            loadingCount: 0,
            invitations: [],
            tutorial: {{ var_export(Auth::user()->finished_tutorial) }},
            tutorial_state: -1
        },
        beforeMount() {
            this.reloadInvitations()
        },
        methods: {
            reloadInvitations() {
                this.changeTutorialState(1)
                self = this
                this.loadingCount++
                axios.get("invitation/lists")
                .then(function (response){
                    self.invitations = response.data
                })
                .finally(() => this.loadingCount--)
            },
            changeTutorialState(step) {
                if(this.tutorial == 1) return
                if (this.tutorial_state == step || this.tutorial_state < 0) {
                    this.tutorial_state++
                }
            }
        }
    })
    $('#tutorialBtn1').click(function () {
        app.changeTutorialState(0)
    })
</script>
@endsection
@section('css-styles')
<style>
</style>
@endsection