<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ConversionTrackingUuid
 *
 * @property int $id
 * @property string $uuid
 * @property int $account_follower_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $expire_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ConversionTrackingRecord[] $conversionTrackingRecords
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereAccountFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingUuid whereUuid($value)
 * @mixin \Eloquent
 */
class ConversionTrackingUuid extends Model
{
    protected $guarded = [];

    public function conversionTrackingRecords()
    {
        return $this->hasMany(ConversionTrackingRecord::class);
    }
}
