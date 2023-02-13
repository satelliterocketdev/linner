<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\RoleUser
 *
 * @property int $id
 * @property int|null $role_id
 * @property int $user_id
 * @property int|null $account_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account|null $account
 * @property-read \App\Role|null $role
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoleUser whereUserId($value)
 * @mixin \Eloquent
 */
class RoleUser extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
