<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Source
 *
 * @property int $id
 * @property int $account_id
 * @property string $type
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereValue($value)
 * @mixin \Eloquent
 */
class Source extends Model
{
    const TYPE = [
        1 => 'NAME',
        2 => 'URL',
    ];

    protected $fillable = [
        'user_id', 'type', 'value'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
