<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\RichMenuItem
 *
 * @property int $id
 * @property int $rich_menu_type
 * @property string $title
 * @property string $action_value_data
 * @property int $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $account_id
 * @property string $rich_menu_id
 * @property-read \App\Account $account
 * @property-read \App\RichMenuAttachment $richMenuAttachment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RichMenuDelivery[] $richMenuDeliveries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RichMenuTarget[] $richMenuTargets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereActionValueData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereRichMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereRichMenuType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RichMenuItem extends Model
{
    use TargetTrait;

    /**
     * TargetTrait override
     * @return HasMany
     */
    protected function getTargets()
    {
        return $this->richMenuTargets();
    }

    protected $table  = "rich_menu_items";
    
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return HasMany
     */
    public function richMenuTargets()
    {
        return $this->hasMany(RichMenuTarget::class);
    }

    /**
     * @return HasMany
     */
    public function richMenuDeliveries()
    {
        return $this->hasMany(RichMenuDelivery::class);
    }

    /**
     * @return HasOne
     */
    public function richMenuAttachment()
    {
        return $this->hasOne(RichMenuAttachment::class);
    }
}
