<?php

use Illuminate\Database\Seeder;
use App\TemplateMessage;
use App\User;

class TemplateMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            $account = $user->account;
            $template = $account->templateMessages()->create([
                'title' => '猛烈な台風19号は関東、東海を直撃　三連休初日の12日(土)から警戒',
//                'content_type' => 'message',
                'content_message' => '台風19号は9日(水)9時現在、大型で猛烈な勢力を保ちながらマリアナ諸島近海を北西に進んでいます。明日10日(木)朝の時点でも猛烈な勢力を維持する予想です。',
                'is_active' => '0',
                'is_draft' => '0',
            ]);
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '1']
            );
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '2']
            );

            $template = $account->templateMessages()->create([
                'title' => 'ラヴ・イズ・オーヴァー',
//                'content_type' => 'message',
                'content_message' => 'Love is over 悲しいけれど、終りにしよう きりがないから、Love is over わけなどないよ、ただひとつだけ あなたのため、
                Love is over 若いあやまちと、笑って言える 時が来るから、Love is over 泣くな男だろう、私のことは 早く忘れて',
                'is_active' => '0',
                'is_draft' => '0',
            ]);
            $template->templateMessageAttachments()->create([
                'media_file_id' => '4'
            ]);

            $template = $account->templateMessages()->create([
                'title' => '木村佳乃の“体当たり芸”女芸人は脅威　ガンバレルーヤ「仕事を取られる…」',
//                'content_type' => 'message',
                'content_message' => '女優の木村佳乃（43）、お笑いコンビのガンバレルーヤが9日、都内で行われたノンアルビール『キリン カラダFREE』新CM完成披露発表会に出席。日本テレビ系バラエティー『世界の果てまでイッテQ！』で体を張った姿を見せている木村が、女芸人界の“脅威”となっていると、ガンバレルーヤの2人が証言した。',
                'is_active' => '0',
                'is_draft' => '0',
            ]);

            $template = $account->templateMessages()->create([
                'title' => 'ティファニー広告に中国人抗議＝片目姿は「香港デモ支持」',
//                'content_type' => 'message',
                'content_message' => '【上海AFP時事】米高級宝飾ブランドのティファニーが7日、中国人女性モデルが手で右目を覆う広告写真をソーシャルメディア上に掲載したところ、香港のデモを支持していると中国人の非難を浴び、削除に追われる騒動があった。',
                'is_active' => '0',
                'is_draft' => '0',
            ]);
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '1']
            );
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '2']
            );
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '5']
            );
            $template->templateMessageAttachments()->create(
                ['media_file_id' => '6']
            );
        }
    }
}
