<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SurveyAnswer
 *
 * @property int $id
 * @property int $survey_id アンケートID
 * @property int $answer_no 回答結果No
 * @property int $pf_user_id 回答者ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\PfUser $pfUser
 * @property-read \App\Survey $survey
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer whereAnswerNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SurveyAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SurveyAnswer extends Model
{
    protected $guarded = [];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }
}
