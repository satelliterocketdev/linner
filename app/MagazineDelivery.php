<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\MagazineDelivery
 *
 * @property int $id
 * @property int $magazine_id
 * @property int $pf_user_id
 * @property int $is_sent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $is_attachment
 * @property-read \App\Magazine $magazine
 * @property-read \App\PfUser $pfUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery isSent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereIsAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MagazineDelivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MagazineDelivery extends Model
{
    protected $guarded = [];
    
    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function scopeIsSent($query)
    {
        return $query->where('is_sent', 1);
    }

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }
}
