<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ConversionAction
 *
 * @property int $id
 * @property int $conversion_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $type
 * @property int $index
 * @property string $option
 * @property int $tag_management_id
 * @property int $scenario_id
 * @property string $message
 * @property-read \App\Conversion $conversion
 * @property-read mixed $scenario_name
 * @property-read mixed $tag_title
 * @property-read \App\Scenario $scenario
 * @property-read \App\TagManagement $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereConversionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConversionAction extends Model
{
    protected $guarded = [];

    public function conversion()
    {
        return $this->belongsTo(Conversion::class);
    }
    
    //TODO: actions周りの処理はtraitで共通化できそう
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
