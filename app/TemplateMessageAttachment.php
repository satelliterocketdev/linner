<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TemplateMessageAttachment
 *
 * @property int $id
 * @property int $template_message_id
 * @property int $media_file_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\MediaFile $mediaFile
 * @property-read \App\TemplateMessage $templateMessage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessageAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessageAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessageAttachment whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessageAttachment whereTemplateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateMessageAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TemplateMessageAttachment extends Model
{
    protected $guarded = [];
    
    public function templateMessage()
    {
        return $this->belongsTo(TemplateMessage::class);
    }

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }
}
