<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Notification
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $start_at
 * @property string|null $end_at
 * @property int $is_draft
 * @property-read \App\AccountNotification $accountNotification
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{
    public function accountNotification()
    {
        return $this->hasOne(AccountNotification::class);
    }
}
