<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Plan
 *
 * @property int $id ID
 * @property string $type タイプ[trial=>お試し,personal=>個人向け,corporation=>法人向け]
 * @property string $name プラン名 お試し,個人向け,法人向け 等
 * @property string $description1 説明1
 * @property string $description2 説明2
 * @property string $level レベル[free,light,standard,pro,expart,business,enterprise]
 * @property int $account_count アカウント数
 * @property int|null $delivery_count 配信可能数
 * @property int $price 価格
 * @property int $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account $plan
 * @property-read \App\Settlement $settlement
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereAccountCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereDeliveryCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereDescription1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereDescription2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Model
{

    public const FREE       = 1;/** @var int フリープラン */
    public const LIGHT      = 2;/** @var int LIGHTプラン */
    public const STANDARD   = 3;/** @var int STANDARDプラン */
    public const PRO        = 4;/** @var int PROプラン */
    public const STARTUP    = 5;/** @var int STARTUPプラン */
    public const BUSINESS   = 6;/** @var int BUSINESSプラン */
    public const ENTERPRISE = 7;/** @var int ENTERPRISEプラン */

    public function plan()
    {
        return $this->belongsTo(Account::class);
    }

    public function settlement()
    {
        return $this->hasOne(Settlement::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
