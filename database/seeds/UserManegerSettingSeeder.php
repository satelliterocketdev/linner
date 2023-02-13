<?php

use Illuminate\Database\Seeder;
use App\LineUserManegerSetting;


class UserManegerSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user_maneger_settings = New LineUserManegerSetting();
        $user_maneger_settings->user_id = 1;
        $user_maneger_settings->channel_id = '1605493569';
        $user_maneger_settings->channel_secret = '8f89b51ce60757eab7a8c10ca3f0fec7';
        $user_maneger_settings->channel_access_token = '11l6jexBKdUVkKQb71R/ob2ZyasIBGJHz6SCc+sM84i24ZyHfe4YMATbuttL/54HSc9hu0QOpbBAC36MyOwLaiYHi9RwgdED27cCMSjwUJyY+OzvXW0n6lrRkgdxm0Kg+1EkQecH42M/S4xZ5ffPXQdB04t89/1O/w1cDnyilFU=';
        $user_maneger_settings->webhook_uRL = url('').'/line/bot/callback/';
        $user_maneger_settings->save();
    }
}
