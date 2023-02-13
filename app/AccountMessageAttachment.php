<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\AccountMessageAttachment
 *
 * @property int $id
 * @property int $account_id
 * @property int $account_message_id
 * @property string $message_id
 * @property string|null $file_name
 * @property string|null $file_url
 * @property string|null $preview_file_url
 * @property int $file_size
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @property-read \App\AccountMessage $accountMessages
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereAccountMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment wherePreviewFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountMessageAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountMessageAttachment extends Model
{
    /**
     * @return HasOne
     */
    public function accountMessages() {
        return $this->hasOne(AccountMessage::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
