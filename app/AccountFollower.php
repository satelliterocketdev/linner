<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\AccountFollower
 *
 * @property int $id
 * @property string $account_id
 * @property string $channel_id
 * @property string $pf_user_id
 * @property string|null $display_name
 * @property string $event_type
 * @property string|null $reply_token
 * @property string|null $source_type
 * @property string $source_user_id
 * @property string|null $destination_user_id
 * @property string|null $status
 * @property string $timedate_followed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int $message_status
 * @property int $is_tester
 * @property string|null $notes
 * @property int $is_blocked
 * @property string|null $blocked_date
 * @property int $is_visible
 * @property-read \App\Account $account
 * @property-read \App\PfUser $pfUsers
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\AccountFollower onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereBlockedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereDestinationUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereIsTester($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereMessageStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereSourceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereTimedateFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountFollower whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AccountFollower withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\AccountFollower withoutTrashed()
 * @mixin \Eloquent
 */
class AccountFollower extends Model
{
    use SoftDeletes;

    protected $table = "account_followers";
    protected $dates = ["deleted_at"];

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo
     */
    public function pfUsers()
    {
        return $this->belongsTo(PfUser::class, 'pf_user_id');
    }
}
