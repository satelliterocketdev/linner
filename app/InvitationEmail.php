<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\InvitationEmail
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string $content_message
 * @property string|null $destination
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereContentMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvitationEmail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvitationEmail extends Model
{
    protected $guarded = [];
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
