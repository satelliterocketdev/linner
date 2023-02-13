<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * App\Settlement
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $settlement_at
 * @property int $plan_id
 * @property int $status
 * @property int $amount
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $token
 * @property int $is_auto 自動決済フラグ：0=>手動、1=>自動
 * @property int $printed
 * @property-read mixed $day
 * @property-read mixed $month
 * @property-read mixed $year
 * @property-read mixed $yen_only
 * @property-read mixed $yen_price
 * @property-read \App\Plan $plan
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement myNewSettlement()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereIsAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement wherePrinted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereSettlementAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settlement whereUserId($value)
 * @mixin \Eloquent
 */
class Settlement extends Model
{
    /** @var int 未決済 */
    public const STATUS_UNSETTLED = 0;
    /** @var int 決済済み */
    public const STATUS_SETTLED = 1;
    /** @var int 決済失敗 */
    public const STATUS_FAILED = 2;

    protected $guarded = [];

    protected $appends = [
        'yen_price', 'yen_only', 'year', 'month', 'day'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function getYenOnlyAttribute()
    {
        return '¥ '. number_format($this->amount);
    }

    public function getYenPriceAttribute()
    {
        return number_format($this->amount);
    }

    public function getYearAttribute()
    {
        return date('Y', strtotime($this->settlement_at));
    }

    public function getMonthAttribute()
    {
        return date('n', strtotime($this->settlement_at));
    }

    public function getDayAttribute()
    {
        return date('j', strtotime($this->settlement_at));
    }

    public static function updateStatus($amount, $id)
    {
        $token = hash('sha256', $amount . $id);
        $settlement = Settlement::where('token', $token)->first();

        if (is_null($settlement)) {
            return false;
        }

        $settlement->status = self::STATUS_SETTLED;
        $settlement->settlement_at = Carbon::now();
        $settlement->token = null;
        $settlement->is_auto = false;
        $settlement->save();

        return true;
    }


    public function scopeMyNewSettlement($query)
    {
        $user = Auth::user();
        return $query->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
    }

}
