<?php

use Illuminate\Database\Seeder;
use App\Conversion;

class ConversionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conversions = New Conversion();
        $conversions->user_id = \App\User::all()->first()->id;
        $conversions->title = 'title1';
        $conversions->conversion_token = '1';
        $conversions->is_active = '1';
        $conversions->access_count = '0';
        $conversions->save();
    }
}
