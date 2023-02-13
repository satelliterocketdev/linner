<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClickrateMessageRecord
 *
 * @property int $id
 * @property int $clickrate_item_id
 * @property int $record_type
 * @property int $method
 * @property int $source_message_id
 * @property int $send_count
 * @property int $access_count
 * @property string $send_at
 * @property string|null $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ClickrateFollowerRecord[] $clickrateFollowerRecords
 * @property-read \App\ClickrateItem $clickrateItem
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereAccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereClickrateItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereRecordType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereSendCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereSourceMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ClickrateMessageRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClickrateMessageRecord extends Model
{
    protected $guarded = [];
    
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($messageRecord) {
            // messageRecordに紐づくタグの削除
            $followerRecords = $messageRecord->clickrateFollowerRecords;
            foreach ($followerRecords as $followerRecord) {
                $followerRecord->delete();
            }
        });
    }

    public function clickrateItem()
    {
        return $this->belongsTo(ClickrateItem::class);
    }

    public function clickrateFollowerRecords()
    {
        return $this->hasMany(ClickrateFollowerRecord::class);
    }
}
