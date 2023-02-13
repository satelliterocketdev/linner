<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\MagazineTarget
 *
 * @property int $id
 * @property int|null $magazine_id
 * @property int|null $pf_user_id
 * @property int|null $tag_management_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $scenario_id
 * @property int $is_exclude 除外判定
 * @property int $is_all
 * @property int $index
 * @property string|null $start_at
 * @property string|null $end_at
 * @property string $option
 * @property-read \App\Magazine|null $magazine
 * @property-read \App\PfUser|null $pfUser
 * @property-read \App\Scenario|null $scenario
 * @property-read \App\MagazineTarget|null $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereIsAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereIsExclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineTarget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MagazineTarget extends Model
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

    public function scenario() {
        return $this->belongsTo(Scenario::class);
    }
}
