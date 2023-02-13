<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Inquery
 *
 * @property int $id
 * @property int $account_id
 * @property string $body
 * @property string|null $answer
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inquery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Inquery extends Model
{
    protected $guarded = [];
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
