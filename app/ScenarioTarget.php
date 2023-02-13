<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScenarioTarget
 *
 * @property int $id
 * @property int|null $scenario_id
 * @property int|null $pf_user_id
 * @property int|null $tag_management_id
 * @property int $is_exclude
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $start_at
 * @property string|null $end_at
 * @property int $source_scenario_id
 * @property int $index
 * @property string $option
 * @property-read \App\PfUser $pfUsers
 * @property-read \App\Scenario|null $scenarios
 * @property-read \App\TagManagement $tagManagements
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereIsExclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereSourceScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioTarget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenarioTarget extends Model
{
    protected $guarded = [];
    
    public function scenarios()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function pfUsers()
    {
        return $this->belongsTo(PfUser::class);
    }

    public function tagManagements()
    {
        return $this->belongsTo(TagManagement::class);
    }
}
