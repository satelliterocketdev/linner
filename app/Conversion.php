<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ActionTrait;

/**
 * App\Conversion
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $conversion_token
 * @property int $is_active
 * @property int $access_count
 * @property \Carbon\Carbon|null $deleted_at
 * @property string $redirect_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ConversionAction[] $conversionActions
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Conversion onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereAccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereConversionToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Conversion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Conversion withoutTrashed()
 * @mixin \Eloquent
 */
class Conversion extends Model
{
    use SoftDeletes;
    use ActionTrait;

    protected $dates = ["deleted_at"];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversionActions()
    {
        return $this->hasMany(ConversionAction::class);
    }

    public function getActions()
    {
        return $this->conversionActions();
    }
}
