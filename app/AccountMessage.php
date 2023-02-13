<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

/**
 * App\AccountMessage
 *
 * @property int $id
 * @property int $account_id
 * @property string $destination
 * @property string $reply_token
 * @property string $type
 * @property string $timestamp
 * @property string $source_type
 * @property string $source_user_id
 * @property string $message_id
 * @property string $message_type
 * @property string $message_json_data
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $message_body
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountMessageAttachment[] $accountMessageAttachments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereMessageBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereMessageJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereSourceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountMessage extends Model
{
    protected $guarded = [];
    /**
     * @return HasMany
     */
    public function accountMessageAttachments() {
        return $this->hasMany(AccountMessageAttachment::class);
    }

    /**
     * @return array JSON
     */
    public function getMessageJsonData() {
        return json_decode($this->message_json_data);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function isReceivedMessage()
    {
        // システムが受信したメッセージの場合、destinationにはシステム側アカウントのid
        // システムが送信したメッセージの場合、destinationには送り先source_user_id
        return $this->destination != $this->source_user_id;
    }
}
