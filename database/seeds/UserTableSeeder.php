<?php

use App\Account;
use App\Plan;
use App\RoleUser;
use App\Settlement;
use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = Account::all();

        // テスト用
        $accounts = $accounts->reject(function ($account) {
            return $account->name == '伊藤';
        });

        $count = 0;
        foreach ($accounts as $account) {
            $count++;
            $user = new User();
            $user->name = "demo{$count}";
            $user->email = $count == 1 ? 'demo@linner.jp' : "dummy{$count}@linner.jp";
            $user->password = bcrypt('12345678');
            $user->account_id = $account->id;
            $user->register_token = '1';
            $user->admin = true;
            $user->plan_id = Plan::inRandomOrder()->first()->id;
            $plan_id = $user->plan_id;
            $user->save();

            $roleUser = new RoleUser();
            $roleUser->role_id = Role::ROLE_ACCOUNT_ADMINISTRATOR;
            $roleUser->user_id = $user->id;
            $roleUser->account_id = $account->id;
            $roleUser->save();

            for ($i = 0; $i < 10; $i++) {
                $user = new User();
                $user->name = "dummy{$count}{$i}";
                $user->email = "dummy{$count}{$i}@linner.jp";
                $user->password = bcrypt('12345678');
                $user->account_id = $account->id;
                $user->register_token = '1';
                $user->admin = false;
                $user->plan_id = $plan_id;
                $user->save();
            }
        }

        // テスト用
        $roleUser = new RoleUser();
        $roleUser->role_id = Role::ROLE_ACCOUNT_ADMINISTRATOR;
        $roleUser->user_id = 1;
        $roleUser->account_id = 3;
        $roleUser->save();
    }
}
