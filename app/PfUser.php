<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

/**
 * App\PfUser
 *
 * @property int $id
 * @property string $display_name
 * @property string|null $picture
 * @property string $status_message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\AccountFollower $accountFollower
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineDelivery[] $magazineDeliveries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineTarget[] $magazineTargets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PfUserTagManagement[] $pfUserTagManagements
 * @property-read \App\RichMenuDelivery $richMenuDeliveries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioDelivery[] $scenarioDeliveries
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\PfUser onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereStatusMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PfUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\PfUser withoutTrashed()
 * @mixin \Eloquent
 */
class PfUser extends Model
{
    use SoftDeletes;

    protected $table  = "pf_users";
    protected $dates = ["deleted_at"];

    /**
     * @return HasOne
     */
    public function pfUserTagManagements()
    {
        return $this->hasMany(PfUserTagManagement::class);
    }

    /**
     * @return HasOne
     */
    public function accountFollower()
    {
        return $this->hasOne(AccountFollower::class);
    }

    public function magazineTargets()
    {
        return $this->hasMany(MagazineTarget::class);
    }

    public function magazineDeliveries()
    {
        return $this->hasMany(MagazineDelivery::class);
    }

    public function scenarioDeliveries()
    {
        return $this->hasMany(ScenarioDelivery::class);
    }

    public function richMenuDeliveries()
    {
        return $this->hasOne(RichMenuDelivery::class);
    }
}
