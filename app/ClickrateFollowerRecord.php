<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClickrateFollowerRecord
 *
 * @property int $id
 * @property int $clickrate_message_record_id
 * @property int $clickrate_item_id
 * @property int $account_follower_id
 * @property string|null $access_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\ClickrateMessageRecord $clickrateMessageRecord
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereAccessAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereAccountFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereClickrateItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereClickrateMessageRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateFollowerRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClickrateFollowerRecord extends Model
{
    protected $guarded = [];
    
    public function clickrateMessageRecord()
    {
        return $this->belongsTo(ClickrateMessageRecord::class);
    }
}
