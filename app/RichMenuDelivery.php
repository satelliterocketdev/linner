<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RichMenuDelivery
 *
 * @property int $id
 * @property string $rich_menu_item_id
 * @property string|null $pf_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\PfUser|null $pfUser
 * @property-read \App\RichMenuItem $richMenuItem
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuDelivery wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuDelivery whereRichMenuItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuDelivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RichMenuDelivery extends Model
{
    protected $guarded = [];

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }

    public function richMenuItem()
    {
        return $this->belongsTo(RichMenuItem::class);
    }
}
