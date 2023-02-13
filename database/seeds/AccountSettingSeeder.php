<?php

use Illuminate\Database\Seeder;
use App\LineAccountDetails;

class AccountSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user_maneger_settings = New LineAccountDetails();
        $user_maneger_settings->user_id = \App\User::all()->first()->id;
        $user_maneger_settings->line_user_id = 'Uc3f25779c1dfe78f8e77fe52f3418f2f';
        $user_maneger_settings->display_name = 'demo';
        $user_maneger_settings->picture = NULL;
        $user_maneger_settings->status_message = NULL;
        $user_maneger_settings->save();
    }
}
