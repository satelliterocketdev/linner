<?php

use Illuminate\Database\Seeder;
use App\AccountFollower;
use App\PfUser;
use App\PfUserTagManagement;
use App\ScenarioDelivery;
use App\TagManagement;
use App\ScenarioMessage;
use App\Account;

class AccountFollowersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = Account::all();
        $count = 0;
        foreach ($accounts as $account) {
            //　伊藤さんを作る
            $accountFollower = $account->accountFollowers()->create([
                "channel_id" => "2",
                "pf_user_id" => ++$count,
                "display_name" => "Itou",
                "event_type" => "Autumn",
                "source_user_id" => "U4af4980629...",
                "status" => "未対応",
                "timedate_followed" => date('Y-m-d H:i:s')
            ]);

            $pfUser = $accountFollower->pfUsers()->create([
                "display_name" => "Itou",
                "picture" => "/img/user-admin.png",
                "status_message" => "既読"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "1"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "2"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "3"
            ]);

            // Fredさんを作る
            $accountFollower = $account->accountFollowers()->create([
                "channel_id" => "2",
                "pf_user_id" => ++$count,
                "display_name" => "Fred",
                "event_type" => "Winter",
                "source_user_id" => "U4af4980630...",
                "status" => "要対応",
                "timedate_followed" => date('Y-m-d H:i:s'),
            ]);

            $pfUser = $accountFollower->pfUsers()->create([
                "display_name" => "Fred",
                "picture" => "/img/user-admin.png",
                "status_message" => "未読"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "2"
            ]);

            // Ronaldoさんを作る
            $accountFollower = $account->accountFollowers()->create([
                "channel_id" => "2",
                "pf_user_id" => ++$count,
                "display_name" => "Ronaldo",
                "event_type" => "Summer",
                "source_user_id" => "Ued720f562e570b344f3906ef6c1880c7",
                "timedate_followed" => date('Y-m-d H:i:s'),
            ]);

            $pfUser = $accountFollower->pfUsers()->create([
                "display_name" => "Ronaldo",
                "picture" => "/img/user-admin.png",
                "status_message" => "未読"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "1"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "3"
            ]);

            // Richardさんを作る
            $accountFollower = $account->accountFollowers()->create([
                "channel_id" => "2",
                "pf_user_id" => ++$count,
                "status" => "要対応",
                "display_name" => "Richard",
                "event_type" => "Spring",
                "source_user_id" => "U1b8b84679f34b9765c967e2f265b0782",
                "timedate_followed" => date('Y-m-d H:i:s')
            ]);

            $pfUser = $accountFollower->pfUsers()->create([
                "display_name" => "Richard",
                "picture" => "/img/user-admin.png",
                "status_message" => "未読"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "2"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "3"
            ]);

            // OldFriendを作る
            $accountFollower = $account->accountFollowers()->create([
                "channel_id" => "2",
                "pf_user_id" => ++$count,
                "status" => "要対応",
                "display_name" => "Old Friend",
                "event_type" => "Spring",
                "source_user_id" => "Uebe9adca8785a4ff0358f8105c530826",
                "timedate_followed" => date('2018-10-11 11:53:20')
            ]);

            $pfUser = $accountFollower->pfUsers()->create([
                "display_name" => "Old Friend",
                "picture" => "/img/user-admin.png",
                "status_message" => "未読"
            ]);

            $pfUserTagManagement = $pfUser->pfUserTagManagements()->create([
                "tag_managements_id" => "3"
            ]);

            // Tags
            $tagFolder = $account->tagsFolders()->create([
                'folder_name' => 'Diary of a lover'
            ]);

            $tagFolder->tagManagements()->create([
                "title" => "タグ１",
                "action" => "アクション１",
                "limit" => "10",
                "no_limit" => false
            ]);

            $tagFolder->tagManagements()->create([
                "title" => "タグ２",
                "action" => "アクション２",
                "limit" => "10",
                "no_limit" => false
            ]);

            $tagFolder->tagManagements()->create([
                "title" => "タグ3",
                "action" => "アクション3",
                "limit" => "10",
                "no_limit" => false
            ]);
        }
    }
}
