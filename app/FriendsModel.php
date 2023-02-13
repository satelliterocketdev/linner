<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FriendsModel
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
 * @property string|null $deleted_at
 * @property int $message_status
 * @property int $is_tester
 * @property string|null $notes
 * @property int $is_blocked
 * @property string|null $blocked_date
 * @property int $is_visible
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereBlockedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereDestinationUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereIsTester($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereMessageStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereSourceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereTimedateFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FriendsModel extends Model
{
    //
    protected $table = 'account_followers';
    // public $timestamps = false;
}
