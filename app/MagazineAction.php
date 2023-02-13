<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MagazineAction
 *
 * @property int $id
 * @property int $magazine_id
 * @property int|null $type
 * @property int|null $tag_management_id
 * @property int|null $scenario_id
 * @property string|null $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $index
 * @property string $option
 * @property-read \App\Magazine $magazine
 * @property-read \App\PfUser $pfUser
 * @property-read \App\Scenario|null $scenario
 * @property-read \App\MagazineTarget|null $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MagazineAction extends Model
{
    protected $guarded = [];

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }

    public function tagManagement()
    {
        return $this->belongsTo(MagazineTarget::class);
    }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
}
