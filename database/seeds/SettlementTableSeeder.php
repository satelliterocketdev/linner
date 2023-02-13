<?php

use App\User;
use Illuminate\Database\Seeder;
use App\Plan;

class SettlementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $plans = Plan::all();

        foreach ($users as $user) {
            foreach ($plans as $plan) {
                $settlement = $user->settlements()->create([
                    'settlement_at' => date('Y-m-d H:i:s'),
                    'plan_id' => $plan->id,
                    'status' => rand(0, 2),
                    'amount' => rand(1, 1000000),
                    'token' => 'hello'
                ]);
            }
            if (isset($settlement)) {
                $user->settlement_id = $settlement->id;
                $user->save();
            }
        }
    }
}
