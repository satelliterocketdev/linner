<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use App\Notification;

class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        $notificationId = 1;
        foreach ($users as $user) {
            $account = $user->account;
            $accountNotification = $account->accountNotifications()->create([
                'notification_id' => $notificationId
            ]);
            $notification = $accountNotification->notification()->create([
                "title" => "お知らせタイトル１",
                'body' => 'お知らせお知らせお知らせお知らせお知らせお知らせお知らせ',
                'start_at' => new \DateTime('2018-01-01 09:00:00'),
                'end_at' => new \DateTime('2018-02-15 22:00:00'),
                'is_draft' => false
            ]);
            $notificationId = 1 + $notificationId;

            $accountNotification = $account->accountNotifications()->create([
                'notification_id' => $notificationId
            ]);
            $notification = $accountNotification->notification()->create([
                "title" => "お知らせタイトル2",
                'body' => 'お知らせお知らせお知らせお知らせお知らせお知らせお知らせ',
                'start_at' => new \DateTime('2018-01-01 09:00:00'),
                'end_at' => new \DateTime('2018-02-15 22:00:00'),
                'is_draft' => false
            ]);
            $notificationId = 1 + $notificationId;

            $accountNotification = $account->accountNotifications()->create([
                'notification_id' => $notificationId
            ]);
            $notification = $accountNotification->notification()->create([
                "title" => "お知らせタイトル3",
                'body' => 'お知らせお知らせお知らせお知らせお知らせお知らせお知らせ',
                'start_at' => new \DateTime('2018-01-01 09:00:00'),
                'end_at' => new \DateTime('2018-02-15 22:00:00'),
                'is_draft' => false
            ]);
            $notificationId = 1 + $notificationId;
        }
    }
}
