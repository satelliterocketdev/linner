<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LineUserManegerSetting
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $channel_id
 * @property string|null $bot_dest_id
 * @property string|null $link_token
 * @property string|null $channel_secret
 * @property string|null $channel_access_token
 * @property string|null $webhook_uRL
 * @property string|null $line_follow_link
 * @property string|null $lineAtId
 * @property string|null $account_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FriendsModel[] $followers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereBotDestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereChannelAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereChannelSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereLineAtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereLineFollowLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereLinkToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineUserManegerSetting whereWebhookURL($value)
 * @mixin \Eloquent
 */
class LineUserManegerSetting extends Model
{
    //
    protected $fillable = [
        'user_id',
        'channel_id',
        'channel_secret',
        'channel_access_token',
        'webhook_uRL',
        'line_follow_link',
        'lineAtId',
        'account_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated
     */
    public function followers()
    {
        return $this->hasMany('App\FriendsModel', 'channel_id', 'channel_id');
    }

    // public function userLineAccountDetails()
    // {
    //     return $this->hasOne('App\FriendsModel', 'channel_id', 'channel_id');
    // }
}
