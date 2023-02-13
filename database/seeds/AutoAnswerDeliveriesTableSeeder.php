<?php

use Illuminate\Database\Seeder;
use App\AutoAnswerDelivery;

class AutoAnswerDeliveriesTableSeeder extends Seeder
{
    public function run()
    {
        for ($i=0; $i < 5 ; $i++) { 
            AutoAnswerDelivery::create([
                "auto_answer_id" => "1",
                "pf_user_id" => "1",
                "is_attachment" => "0",
                "is_sent" => "1",
            ]);
        }
    }
}
