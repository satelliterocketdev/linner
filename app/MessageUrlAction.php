<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MessageUrlAction
 *
 * @property int $id
 * @property int $message_url_id
 * @property int $type
 * @property int $index
 * @property string $option
 * @property int $tag_management_id
 * @property int $scenario_id
 * @property string $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $scenario_name
 * @property-read mixed $tag_title
 * @property-read \App\MessageUrl $messageUrl
 * @property-read \App\Scenario $scenario
 * @property-read \App\TagManagement $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereMessageUrlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrlAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageUrlAction extends Model
{
    protected $guarded = [];

    public function messageUrl()
    {
        return $this->belongsTo(MessageUrl::class);
    }

    public function tagManagement()
    {
        return $this->belongsTo(TagManagement::class, 'tag_management_id');
    }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    protected $appends = ['tag_title', 'scenario_name'];

    public function getTagTitleAttribute()
    {
        $tag = $this->tagManagement()->first();
        if (isset($tag)) {
            return $tag->title;
        }
        return null;
    }
    public function getScenarioNameAttribute()
    {
        $scenario = $this->scenario()->first();
        if (isset($scenario)) {
            return $scenario->name;
        }
        return null;
    }
}
