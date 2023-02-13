<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\ScenarioDelivery
 *
 * @property int $id
 * @property int $scenario_message_id
 * @property string $type
 * @property int|null $pf_user_id
 * @property \Carbon\Carbon $schedule_date
 * @property int $is_attachment
 * @property int $is_sent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\PfUser|null $pfUser
 * @property-read \App\ScenarioMessage $scenarioMessage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereIsAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereScenarioMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereScheduleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScenarioDelivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenarioDelivery extends Model
{
    protected $table = 'scenario_delivery';

    protected $guarded = [];

    protected $dates = [
        'schedule_date',
        'created_at',
        'updated_at'
    ];
    // const LIMIT_FOLLOWER_ID = 3;

    public static function preCreate($request)
    {
        $message = $request['message'];
        $content_message = $message->content_message;
        
        $attachment = isset($request['attachment']) ? $request['attachment'] : null;
        $is_attachment = ($attachment) ? 1 : 0;
        
        $type = !$is_attachment ? $message->content_type : $attachment['type'];
        
        $schedule_date = new \Datetime($message->schedule_date);
        
        $content = [];
        switch ($type) {
            case 'image':
            case 'video':
                $content = [
                    'url' => $attachment['url'],
                    'featured_url' => $attachment['featured_url'],
                ];
                break;
            case 'audio':
                $content = [
                    'url' => $attachment['url'],
                    'duration' => $attachment['duration'],
                ];
                break;
        }
        $data = [
            'scenario_message_id' => $message->id,
            // 'follower_user_id' => $user->id,
            // 'follower_user_id' => json_encode($followerUserIds),
            'type' => $type,
            'pf_user_id' => isset($request['pf_user_id']) ? $request['pf_user_id'] : null,
            'schedule_date' => $schedule_date->format('Y-m-d H:i:s'),
            'is_attachment' => $is_attachment,
            'is_sent' => 0
        ];
        return parent::create($data);
    }

    /**
     * @return BelongsTo
     */
    public function scenarioMessage()
    {
        return $this->belongsTo(ScenarioMessage::class);
    }

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }

    public function getScenarioId()
    {
        return $this->scenarioMessage->scenario->id;
    }
}
