<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RichMenuTarget
 *
 * @property int $id
 * @property int|null $pf_user_id
 * @property int|null $tag_management_id
 * @property int $rich_menu_item_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $scenario_id
 * @property string|null $start_at
 * @property string|null $end_at
 * @property int $index
 * @property int $is_exclude
 * @property string $option
 * @property-read \App\PfUser|null $pfUser
 * @property-read \App\RichMenuItem $richMenuItem
 * @property-read \App\TagManagement|null $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereIsExclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereRichMenuItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereTagManagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenuTarget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RichMenuTarget extends Model
{
    protected $table  = "rich_menu_targets";

    protected $guarded = [];

    public function richMenuItem()
    {
        return $this->belongsTo(RichMenuItem::class);
    }

    public function tagManagement()
    {
        return $this->belongsTo(TagManagement::class);
    }

    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }
}
