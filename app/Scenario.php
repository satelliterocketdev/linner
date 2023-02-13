<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Scenario
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string|null $target
 * @property string|null $action
 * @property string|null $message
 * @property int $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $is_draft
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineTarget[] $magazineTargets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioAction[] $scenarioActions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioMessage[] $scenarioMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioTarget[] $scenarioTargets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Scenario whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Scenario extends Model
{
    use TargetTrait, ActionTrait;

    /**
     * TargetTrait override
     * @return HasMany
     */
    protected function getTargets()
    {
        return $this->scenarioTargets();
    }

    protected function getActions()
    {
        return $this->scenarioActions();
    }

    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scenarioMessages()
    {
        return $this->hasMany(ScenarioMessage::class);
    }

    public function scenarioTargets()
    {
        return $this->hasMany(ScenarioTarget::class, 'source_scenario_id');
    }

    public function scenarioActions()
    {
        return $this->hasMany(ScenarioAction::class, 'source_scenario_id');
    }

    public function magazineTargets()
    {
        return $this->hasMany(MagazineTarget::class);
    }
}
