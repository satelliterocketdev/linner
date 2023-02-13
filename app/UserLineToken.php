<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserLineToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $access_token
 * @property string $token_type
 * @property string $scope
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereTokenType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLineToken whereUserId($value)
 * @mixin \Eloquent
 */
class UserLineToken extends Model
{
    //
}
