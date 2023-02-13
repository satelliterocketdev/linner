<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\PfUserTagManagement
 *
 * @property int $id
 * @property int $pf_user_id
 * @property int $tag_managements_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\PfUser $pfUser
 * @property-read \App\TagManagement $tagManagement
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUserTagManagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUserTagManagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUserTagManagement wherePfUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUserTagManagement whereTagManagementsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PfUserTagManagement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PfUserTagManagement extends Model
{
    protected $guarded = [];
    /**
     * @return HasOne
     */
    public function pfUser()
    {
        return $this->belongsTo(PfUser::class);
    }
    
    public function tagManagement()
    {
        return $this->belongsTo(TagManagement::class, 'tag_managements_id');
    }
}
