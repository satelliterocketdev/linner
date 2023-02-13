<?php

use Illuminate\Database\Seeder;
use App\Account;

class InvitationEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emails = [
            [
                'title' => 'DatabaseSeeder class',
                'content_message' => 'Within the DatabaseSeeder class, you may use the call method to execute additional seed classes. Using the call method allows you to break up your database seeding into multiple files so that no single seeder class becomes overwhelmingly large.',
                'destination' => '["test@test.com"]'
            ],
            [
                'title' => 'migrate',
                'content_message' => 'You may also seed your database using the migrate:fresh command, which will drop all tables and re-run all of your migrations.',
                'destination' => '["test@test.com"]'
            ],
            [
                'title' => 'operations',
                'content_message' => 'Some seeding operations may cause you to alter or lose data. In order to protect you from running seeding commands against your production database, you will be prompted for confirmation before the seeders are executed. ',
                'destination' => '["test@test.com"]'
            ]
        ];

        foreach (Account::all() as $account) {
            foreach ($emails as $email) {
                $account->invitationEmails()->create($email);
            }
        }
    }
}
