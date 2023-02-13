<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\MessageUrlTrait;

/**
 * App\TemplateMessage
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string $content_message
 * @property int $is_active
 * @property int $is_draft
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $formatted_message
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageUrl[] $messageUrls
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TemplateMessageAttachment[] $templateMessageAttachments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereContentMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereFormattedMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TemplateMessage extends Model
{
    use MessageUrlTrait;
    
    protected $guarded = [];

    public function messageUrls()
    {
        return $this->hasMany(MessageUrl::class);
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function templateMessageAttachments()
    {
        return $this->hasMany(TemplateMessageAttachment::class);
    }
}
