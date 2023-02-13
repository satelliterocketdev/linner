<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClickrateItem
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string $redirect_url
 * @property string $clickrate_token
 * @property int $send_count
 * @property int $access_count
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ClickrateMessageRecord[] $clickrateMessageRecords
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereAccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereClickrateToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereSendCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClickrateItem extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($item) {
            // itemに紐づくタグの削除
            $messages = $item->clickrateMessageRecords;
            foreach ($messages as $message) {
                $message->delete();
            }
        });
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function clickrateMessageRecords()
    {
        return $this->hasMany(ClickrateMessageRecord::class);
    }
}
