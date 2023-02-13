<?php

namespace App\Providers;

use App\Role;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // アカウント管理者
        Gate::define(Role::ROLE_ACCOUNT_ADMINISTRATOR, function (User $user) {
            return $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR);
        });

        // シナリオ配信編集可能
        Gate::define(Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE);
        });

        // 一斉配信編集可能
        Gate::define(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE);
        });

        // 自動応答編集可能
        Gate::define(Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE);
        });

        // テンプレート編集可能
        Gate::define(Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE);
        });

        // タグ管理利用可能
        Gate::define(Role::ROLE_TAG_MANAGEMENT_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_TAG_MANAGEMENT_AVAILABLE);
        });

        // 友だち情報管理利用可能
        Gate::define(Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE);
        });

        // メール配信利用可能
        Gate::define(Role::ROLE_FRIEND_MAIL_INVITE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_FRIEND_MAIL_INVITE);
        });

        // トーク一覧利用可能
        Gate::define(Role::ROLE_FRIEND_TALK_LIST, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_FRIEND_TALK_LIST);
        });

        // コンバージョン理利用可能
        Gate::define(Role::ROLE_CONVERSION_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_CONVERSION_AVAILABLE);
        });

        // URLクリック測定利用可能
        Gate::define(Role::ROLE_URL_CLICK_MEASUREMENT_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_URL_CLICK_MEASUREMENT_AVAILABLE);
        });

        // アンケート結果利用可能
        Gate::define(Role::ROLE_SURVEYS_RESULT_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_SURVEYS_RESULT_AVAILABLE);
        });

        Gate::define(Role::ROLE_RICH_MENU_AVAILABLE, function (User $user) {
            return $this->isAdmin($user) ? true : $user->hasRole(Role::ROLE_RICH_MENU_AVAILABLE);
        });
    }

    private function isAdmin($user)
    {
        return $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
    }
}
