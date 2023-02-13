<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserFollow
 *
 * @property int $id
 * @property string $channel_id
 * @property string $line_user_id
 * @property string|null $display_name
 * @property string|null $picture
 * @property string|null $status_message
 * @property string $event_type
 * @property string|null $reply_token
 * @property string|null $source_type
 * @property string|null $source_user_id
 * @property string|null $destination_user_id
 * @property int|null $status
 * @property string $timedate_followed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereDestinationUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereLineUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereSourceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereStatusMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereTimedateFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFollow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserFollow extends Model
{
    protected $table = 'user_follow';
}
