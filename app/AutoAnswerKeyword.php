<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AutoAnswerKeyword
 *
 * @property int $id
 * @property int $auto_answer_id
 * @property string $keyword
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\AutoAnswer $AutoAnswer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswerKeyword whereAutoAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswerKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswerKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswerKeyword whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutoAnswerKeyword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AutoAnswerKeyword extends Model
{
    protected $guarded = [];

    public function AutoAnswer()
    {
        return $this->belongsTo(AutoAnswer::class);
    }
}
