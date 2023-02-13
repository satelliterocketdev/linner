<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ConversionTrackingRecord
 *
 * @property int $id
 * @property int $conversion_tracking_uuid_id
 * @property string $token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $expire_at
 * @property-read \App\ConversionTrackingUuid $conversionTrackingUuid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereConversionTrackingUuidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConversionTrackingRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConversionTrackingRecord extends Model
{
    protected $guarded = [];

    public function conversionTrackingUuid()
    {
        return $this->belongsTo(conversionTrackingUuid::class);
    }
}
