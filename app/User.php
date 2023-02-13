<?php

namespace App;

use App\Notifications\PasswordReset;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $register_token
 * @property int $admin
 * @property int|null $account_id
 * @property int|null $plan_id
 * @property int|null $settlement_id
 * @property int|null $request_plan_id 希望プラン
 * @property int $finished_tutorial
 * @property string $description 説明
 * @property-read \App\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Conversion[] $conversions
 * @property-read \App\LineAccountDetails $lineInfo
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \App\Plan|null $plan
 * @property-read \App\Plan|null $request_plan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RoleUser[] $roleUsers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioMessage[] $scenarioMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Scenario[] $scenarios
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Settlement[] $settlements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Source[] $sources
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFinishedTutorial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRegisterToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRequestPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSettlementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

//    /**
//     * @deprecated
//     * @return Account
//     */
//    public function getAccount():Account
//    {
//        return Account::find($this->account_id);
//    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class);
    }

    public function plan()
    {
        $account = $this->account;
        if (is_null($account)) {
            return $this->belongsTo(Plan::class);
        }
        
        $isAdmin = $this->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $account->id);
        
        //管理者権限の場合
        if ($isAdmin) {
            return $this->belongsTo(Plan::class);
        }

        //アカンウト管理者を取得
        $adminUser = RoleUser::where('account_id', $this->account->id)
                             ->where('role_id', Role::ROLE_ACCOUNT_ADMINISTRATOR)->first()->user;
        
        // アカウント管理者のプランを返却
        return $adminUser->belongsTo(Plan::class);
    }

    public function request_plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenarioMessages()
    {
        return $this->hasMany(ScenarioMessage::class);
    }

    public function conversions()
    {
        return $this->hasMany(Conversion::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lineInfo()
    {
        return $this->hasOne(LineAccountDetails::class);
    }

    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');
        }
    
        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }
    
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }

    public function hasRole($role, $account_id = null)
    {
        if (is_null($account_id)) {
            $account_id = $this->account_id;
        }
        return null !== $this->roleUsers()
                ->where('account_id', $account_id)
                ->where('role_id', $role)->first();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token, $this->email));
    }

    public function getRoles()
    {
        $roleIds = $this->roleUsers()->pluck('role_id');
        return Role::whereIn('id', $roleIds)->get();
    }

    public function setPassword($password)
    {
        $this->password = bcrypt($password);
    }

    public function getFinishedTutorialAttribute($value)
    {
        // チュートリアルをadminにしか表示させない
        if (!$this->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $this->account->id)) {
            return 1;
        }
        return $value;
    }
}
