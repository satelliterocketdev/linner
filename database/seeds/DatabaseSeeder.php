<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AccountTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(PlanTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(SettlementTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(AccountSettingSeeder::class);
        $this->call(UserManegerSettingSeeder::class);
        $this->call(FollowerSeeder::class);
        $this->call(MediaFileTableSeeder::class);
        $this->call(AccountFollowersSeeder::class);
        $this->call(RichMenuSeeder::class);
        $this->call(NotificationTableSeeder::class);
        $this->call(TemplateMessageSeeder::class);
        $this->call(AccountMessageSeeder::class);
        $this->call(ConversionTableSeeder::class);
        $this->call(MagazineSeeder::class);
        $this->call(InquerieTableSeeder::class);
        $this->call(AutoAnswerTableSeeder::class);
        $this->call(AutoAnswerKeywordTableSeeder::class);
        // $this->call(InvitationEmailSeeder::class);
    }
}
