<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MessageModel
 *
 * @property int $id
 * @property string $channelId
 * @property string $replyToken
 * @property string $soruce_userId
 * @property string $soruce_type
 * @property string $message_id
 * @property string|null $message_type
 * @property string $message_text
 * @property string $destination
 * @property string $timestamp
 * @property int $message_flag
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereMessageFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereMessageText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereSoruceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereSoruceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageModel extends Model
{
    //
    protected $table = 'message_delivery';
}
