<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TagManagement;

/**
 * App\TagAction
 *
 * @property int $id
 * @property int $source_tag_management_id
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
 * @property-read \App\Scenario $scenario
 * @property-read \App\TagManagement $sourceTagManagement
 * @property-read \App\TagManagement $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereSourceTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TagAction extends Model
{
    protected $guarded = [];

    public function sourceTagManagement()
    {
        return $this->belongsTo(TagManagement::class, 'source_tag_management_id');
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
