<?php

use Illuminate\Database\Seeder;
use App\RoleUser;
use App\Role;
use App\Account;
use App\User;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allRoles = Role::where('id', '!=', 1)->pluck('id')->toArray();
        foreach (User::all() as $user) {
            foreach (array_random($allRoles, rand(1, 6)) as $roleId) {
                $user->roleUsers()->create([
                    'role_id' => $roleId,
                    'account_id' => $user->account->id
                ]);
            }
        }
    }
}
