<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LineAccountDetails
 *
 * @property int $id
 * @property int $user_id
 * @property string $line_user_id
 * @property string $display_name
 * @property string|null $picture
 * @property string|null $status_message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\LineUserManegerSetting $user_manager_data
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereLineUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereStatusMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LineAccountDetails whereUserId($value)
 * @mixin \Eloquent
 */
class LineAccountDetails extends Model
{
    protected $hidden = [
        'user_id', 'created_at', 'updated_at',
    ];

    /* @deprecated  */
    public function user_manager_data(){
        return $this->hasOne(LineUserManegerSetting::class);
    }
}
