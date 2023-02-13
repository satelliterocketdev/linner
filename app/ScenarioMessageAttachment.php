<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScenarioMessageAttachment
 *
 * @property int $id
 * @property int $scenario_message_id
 * @property int $media_file_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\MediaFile $mediaFile
 * @property-read \App\ScenarioMessage $scenarioMessages
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessageAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessageAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessageAttachment whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessageAttachment whereScenarioMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessageAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenarioMessageAttachment extends Model
{
    protected $fillable = [
        'scenario_message_id', 'media_file_id',
    ];

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function scenarioMessages()
    {
        return $this->belongsTo(ScenarioMessage::class);
    }

    // public function setMediaFileIdAttribute($value)
    // {
    //     // dd($this);
    //     // $this->attributes['media_file_id'] = $this->id;
    // }
}
