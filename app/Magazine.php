<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use \App\MessageUrlTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Magazine
 *
 * @property int $id
 * @property int $account_id
 * @property string|null $title
 * @property string|null $content_message
 * @property string|null $schedule_at
 * @property int $is_active
 * @property int $is_draft
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $content_type message（メッセージ）,surveys（アンケート）
 * @property string $formatted_message
 * @property string|null $time_after
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineAction[] $magazineActions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineAttachment[] $magazineAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineDelivery[] $magazineDeliveries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineTarget[] $magazineTargets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageUrl[] $messageUrls
 * @property-read \App\Survey $survey
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereContentMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereFormattedMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereScheduleAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereTimeAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Magazine extends Model
{
    use TargetTrait, ActionTrait, MessageUrlTrait;

    /**
     * @inheritDoc
     */
    protected function getTargets()
    {
        return $this->magazineTargets();
    }

    protected function getActions()
    {
        return $this->magazineActions();
    }

    protected $guarded = [];

    public function magazineDeliveries()
    {
        return $this->hasMany(MagazineDelivery::class);
    }

    public function magazineTargets()
    {
        return $this->hasMany(MagazineTarget::class);
    }

    public function magazineActions()
    {
        return $this->hasMany(MagazineAction::class);
    }

    public function magazineAttachments()
    {
        return $this->hasMany(MagazineAttachment::class);
    }

    public function messageUrls()
    {
        return $this->hasMany(MessageUrl::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
