<?php

use App\Account;
use App\MagazineTarget;
use App\PfUser;
use Illuminate\Database\Seeder;
use App\Magazine;
use App\Scenario;
use App\TagManagement;

class MagazineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $accounts = Account::all();

        $magazineExamples = [
            [
                'title' => '窃盗罪',
                'content_message' => '窃盗罪は、他人の物を盗む罪であり、万引きなどでも10年以下の懲役又は50万円以下の罰金に処される可能性のある、意外と恐ろしい犯罪です。 もし実刑判決ともなれば前科一犯となり、今後の生活において少なからず不利益になる可能性もあるので絶対にしてはいけない行為です',
                'schedule_at' => new DateTime()
            ],
            [
                'title' => '日本における死刑',
                'content_message' => '本稿では、日本における死刑の概要、歴史を述べる。 日本は死刑を法定刑のひとつとして位置づけている。その方法は絞首によると規定されている（刑法11条1項）。 死刑制度の廃止をめぐる問題に関しては死刑存廃問題に記す。',
                'schedule_at' => new DateTime('2019-04-05')
            ],
            [
                'title' => '徴兵制度',
                'content_message' => 'ちょうへい‐せい【徴兵制】 国家が一定年齢の国民に兵役義務を課して強制的に軍隊に入隊させる制度。 日本では、明治6年（1873）発布の徴兵令に始まり、昭和20年（1945）に廃止。',
                'schedule_at' => new DateTime('2018-04-05')
            ],
        ];

        foreach ($accounts as $account) {
            foreach ($magazineExamples as $magazineExample) {
                $magazine = $account->magazines()->create([
                    'title' => $magazineExample['title'],
                    'content_message' => $magazineExample['content_message'],
                    'formatted_message' => $magazineExample['content_message'],
                    'schedule_at' => $magazineExample['schedule_at'],
                    'content_type' => 'magazine'
                ]);
    
                foreach (collect($account->accountFollowers)->flatMap->pfUsers as $pfUser) {
                    $magazine->magazineDeliveries()->create([
                        'pf_user_id' => $pfUser->id,
                        'is_sent' => true
                    ]);
                }
    
                foreach (collect($account->tagsFolders)->flatMap->tagManagements as $tag) {
                    $magazine->magazineTargets()->create([
                        'tag_management_id' => $tag->id,
                        'index' => 0,
                        'option' => 'first'
                    ]);
                }
    
                $magazine->magazineTargets()->create([
                    'start_at' => date('Y-m-d'),
                    'end_at' => date('Y-m-d'),
                    'index' => 0
                ]);
            }
        }
    }
}
