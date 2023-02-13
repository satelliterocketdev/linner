<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AutoAnswer
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string $content_type
 * @property string $content_message
 * @property string|null $from_time
 * @property string|null $to_time
 * @property int $is_draft
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $week
 * @property int $is_always 0=条件指定,1=常に
 * @property int $is_active 0=無効,1=有効
 * @property int $delivery_count 応答回数
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AutoAnswerDelivery[] $AutoAnswerDelivery
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AutoAnswerKeyword[] $AutoAnswerKeyword
 * @property-read \App\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereContentMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereDeliveryCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereFromTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereIsAlways($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereToTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswer whereWeek($value)
 * @mixin \Eloquent
 */
class AutoAnswer extends Model
{
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function AutoAnswerKeyword()
    {
        return $this->hasMany(AutoAnswerKeyword::class);
    }

    public function AutoAnswerDelivery()
    {
        return $this->hasMany(AutoAnswerDelivery::class);
    }

}
