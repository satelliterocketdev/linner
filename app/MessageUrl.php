<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActionTrait;

/**
 * App\MessageUrl
 *
 * @property int $id
 * @property int $account_id
 * @property string $url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $magazine_id
 * @property int|null $scenario_message_id
 * @property int $index
 * @property int|null $template_message_id
 * @property-read \App\Magazine|null $magazine
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageUrlAction[] $messageUrlActions
 * @property-read \App\ScenarioMessage|null $scenarioMessage
 * @property-read \App\TemplateMessage|null $templateMessage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereScenarioMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereTemplateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MessageUrl whereUrl($value)
 * @mixin \Eloquent
 */
class MessageUrl extends Model
{
    use ActionTrait;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($MessageUrl) {
            // 子テーブル削除
            $actions = $MessageUrl->messageUrlActions;
            foreach ($actions as $action) {
                $action->delete();
            }
        });
    }

    public function messageUrlActions()
    {
        return $this->hasMany(MessageUrlAction::class);
    }

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function scenarioMessage()
    {
        return $this->belongsTo(scenarioMessage::class);
    }

    public function templateMessage()
    {
        return $this->belongsTo(TemplateMessage::class);
    }

    public function getActions()
    {
        return $this->messageUrlActions();
    }
}
