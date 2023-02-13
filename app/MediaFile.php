<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MediaFile
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $type
 * @property string|null $tab
 * @property string $url
 * @property string $featured_url
 * @property int|null $size
 * @property string|null $duration
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $package_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineAttachment[] $magazineAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RichMenuAttachment[] $richMenuAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TemplateMessageAttachment[] $templateMessageAttachments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereFeaturedUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MediaFile whereUserId($value)
 * @mixin \Eloquent
 */
class MediaFile extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'url', 'featured_url', 'size', 'duration',
        'tab',
    ];

    protected $hidden = [
        'user_id', 'created_at', 'updated_at'
    ];

    public function magazineAttachments()
    {
        return $this->hasMany(MagazineAttachment::class);
    }

    public function templateMessageAttachments()
    {
        return $this->hasMany(TemplateMessageAttachment::class);
    }

    public function richMenuAttachments()
    {
        return $this->hasMany(RichMenuAttachment::class);
    }
}
