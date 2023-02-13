<?php

use Illuminate\Database\Seeder;
use App\Plan;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $plan = new Plan;

        $plan->type = 'trial';
        $plan->name = 'お試し';
        $plan->description1 = '月額無料';
        $plan->description2 = '月額無料で、この性能';
        $plan->level = 'free';
        $plan->price = 0;
        $plan->account_count = 1;
        $plan->delivery_count = 0;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'personal';
        $plan->name = '個人様向け';
        $plan->description1 = '個人でのお仕事に最適';
        $plan->description2 = '成長し続ける、冒険の始まり。';
        $plan->level = 'light';
        $plan->price = 4980;
        $plan->account_count = 1;
        $plan->delivery_count = 5000;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'personal';
        $plan->name = '個人様向け';
        $plan->description1 = '個人でのお仕事に最適';
        $plan->description2 = '成長し続ける、冒険の始まり。';
        $plan->level = 'standard';
        $plan->price = 9800;
        $plan->account_count = 1;
        $plan->delivery_count = 15000;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'personal';
        $plan->name = '個人様向け';
        $plan->description1 = '個人でのお仕事に最適';
        $plan->description2 = '成長し続ける、冒険の始まり。';
        $plan->level = 'pro';
        $plan->price = 29800;
        $plan->account_count = 3;
        $plan->delivery_count = 45000;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'corporation';
        $plan->name = '法人様向け';
        $plan->description1 = '法人様のご利用に最適';
        $plan->description2 = 'ビジネスを加速させる、最大の武器に。';
        $plan->level = 'startup';
        $plan->price = 49800;
        $plan->account_count = 5;
        $plan->delivery_count = 100000;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'corporation';
        $plan->name = '法人様向け';
        $plan->description1 = '法人様のご利用に最適';
        $plan->description2 = 'ビジネスを加速させる、最大の武器に。';
        $plan->level = 'business';
        $plan->price = 99800;
        $plan->account_count = 10;
        $plan->delivery_count = 300000;
        $plan->save();

        $plan = new Plan;
        $plan->type = 'corporation';
        $plan->name = '法人様向け';
        $plan->description1 = '法人様のご利用に最適';
        $plan->description2 = 'ビジネスを加速させる、最大の武器に。';
        $plan->level = 'enterprise';
        $plan->price = 149800;
        $plan->account_count = 30;
        $plan->delivery_count = 1000000;
        $plan->save();
    }
}
