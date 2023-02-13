<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccountNotification
 *
 * @property int $id
 * @property int $account_id
 * @property int $notification_id
 * @property int $is_read
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Notification $notification
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereNotificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountNotification extends Model
{
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
