<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MagazineAttachment
 *
 * @property int $id
 * @property int $magazine_id
 * @property int $media_file_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Magazine $magazine
 * @property-read \App\MediaFile $mediaFile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAttachment whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAttachment whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MagazineAttachment extends Model
{
    protected $guarded = [];
    
    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }
}
