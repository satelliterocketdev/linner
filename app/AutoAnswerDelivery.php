<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AutoAnswerDelivery
 *
 * @property-read \App\AutoAnswer $AutoAnswer
 * @mixin \Eloquent
 */
class AutoAnswerDelivery extends Model
{
    public function AutoAnswer()
    {
        return $this->belongsTo(AutoAnswer::class);
    }
}
