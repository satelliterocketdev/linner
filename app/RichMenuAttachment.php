<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RichMenuAttachment
 *
 * @property int $id
 * @property int $rich_menu_item_id
 * @property int $media_file_id
 * @property string $rich_menu_file
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\MediaFile $mediaFile
 * @property-read \App\RichMenuItem $richMenuItem
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereRichMenuFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereRichMenuItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RichMenuAttachment extends Model
{
    protected $guarded = [];

    public function richMenuItem()
    {
        return $this->belongsTo(RichMenuItem::class);
    }

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }
}
