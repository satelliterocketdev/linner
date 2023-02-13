<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\MessageUrlTrait;

/**
 * App\ScenarioMessage
 *
 * @property int $id
 * @property int|null $scenario_id
 * @property string|null $title
 * @property string $content_type
 * @property string|null $content_message
 * @property string|null $schedule_type
 * @property int|null $schedule_number
 * @property string|null $schedule_date
 * @property int $is_active
 * @property int $is_draft
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $formatted_message
 * @property string|null $time_after
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioDelivery[] $deliveries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ScenarioMessageAttachment[] $messageAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageUrl[] $messageUrls
 * @property-read \App\Scenario|null $scenario
 * @property-read \App\Survey $survey
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereContentMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereFormattedMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereScheduleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereScheduleNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereScheduleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereTimeAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenarioMessage extends Model
{
    use MessageUrlTrait;

    /** @var string 登録直後 */
    public const SCHEDULE_TYPE_IMMEDIATELY_AFTER_REGISTRATION = "0";
    /** @var string 登録後時間指定 */
    public const SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION = "1";
    /** @var string 経過時間指定 */
    public const SCHEDULE_TYPE_ELAPSED_TIME = "2";
    /** @var string スケジュールタイプ 日時指定 */
    public const SCHEDULE_TYPE_TIME_SPECIFICATION = "3";

    protected $fillable = [
        'scenario_id', 'title', 'content_type', 'content_message',
        'schedule_type', 'schedule_number', 'schedule_date', 'is_active', 'formatted_message',
        'is_draft', 'generated_url','time_after'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    // public static function create($data)
    // {
    //     $data['generated_url'] = url('scenario/message', self::randomPassword());
    //     return parent::create($data);
    // }

    /**
     * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php/31284266
     */
    public static function randomPassword($length = 12) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function deliveries()
    {
        return $this->hasMany(ScenarioDelivery::class);
    }

    public function messageAttachments()
    {
        return $this->hasMany(ScenarioMessageAttachment::class);
    }

    public function setContentMessageAttribute($value)
    {
        $string = (is_array($value) || is_object($value)) ? json_encode($value) : $value;
        $this->attributes['content_message'] = $string;
    }

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    // ?????
    // public function setScheduleDateAttribute($value)
    // {
    //     $date = new \Datetime($value);
    //     $this->attributes['schedule_date'] = $date->format('Y-m-d H:i:s');
    // }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    public function messageUrls()
    {
        return $this->hasMany(MessageUrl::class);
    }
}
