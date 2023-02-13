<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScenarioAction
 *
 * @property int $id
 * @property int $source_scenario_id
 * @property int $type
 * @property int $index
 * @property string $option
 * @property int|null $tag_management_id
 * @property int|null $scenario_id
 * @property string $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereSourceScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenarioAction extends Model
{
    protected $guarded = [];
}
