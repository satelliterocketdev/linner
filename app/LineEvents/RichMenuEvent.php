<?php

namespace App\LineEvents;

use Illuminate\Support\Facades\Auth;
use App\RichMenu;

use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;

use Intervention\Image\ImageManagerStatic as Image;

class RichMenuEvent extends LineEvent
{
    public function __construct()
    {
        $account = Auth::user()->account;

        parent::__construct($account->channel_secret, $account->channel_access_token);
    }

    public function sendRichMenu($richMenuItem)
    {
        $deliveries = $richMenuItem->richMenuDeliveries;
        $attachments = $richMenuItem->richMenuAttachment;

        $type = RichMenu::where('rich_menu_type', $richMenuItem->rich_menu_type)->get();

        $actions = json_decode($richMenuItem->action_value_data, true);

        $areaBuilders = [];
        foreach ($type as $key => $t) {
            $boundsBuilder = new RichMenuAreaBoundsBuilder($t->x, $t->y, $t->width, $t->height);
            $action = $actions[$key]['type'] == 'uri' ?
                new UriTemplateActionBuilder('test'.$key, $actions[$key]['uri'])  :
                new MessageTemplateActionBuilder('test'.$key, $actions[$key]['uri']);
            $areaBuilders[] = new RichMenuAreaBuilder($boundsBuilder, $action);
        }

        // Rich Menu作成
        $richMenuBuilder = new RichMenuBuilder(
            RichMenuSizeBuilder::getFull(),
            true,
            $richMenuItem->title,
            $richMenuItem->title,
            $areaBuilders
        );
        $response = $this->lineBot->createRichMenu($richMenuBuilder);
        $richMenuId = $response->getJSONDecodedBody()['richMenuId'];

        // Rich Menuに画像をつける
        $richMenuFile = json_decode($attachments->rich_menu_file);
        if (\App::environment() == 'local') {
            $file = Image::make(file_get_contents(str_replace('localhost', 'minio', $richMenuFile->featured_url)))
                ->resize(RichMenu::WIDTH, RichMenu::HEIGHT);
        } else {
            $file = Image::make(file_get_contents($richMenuFile->featured_url))
                ->resize(RichMenu::WIDTH, RichMenu::HEIGHT);
        }
        $file->encode('jpg');
        file_put_contents(public_path('img/richmenu.jpg'), $file);
        $result = $this->lineBot->uploadRichMenuImage($richMenuId, public_path('img/richmenu.jpg'), 'image/jpeg');
        unlink(public_path('img/richmenu.jpg'));

        // ユーザーとリッチメニューを紐付け
        foreach ($deliveries as $delivery) {
            $userId = $delivery->pfUser->accountFollower->source_user_id;
            if ($userId) {
                $result = $this->lineBot->linkRichMenu($userId, $richMenuId);
                // \Log::info(['deliveries' => $result]);
            }
        }

        return $richMenuId;
    }

    public function deleteRichMenu($richMenuId)
    {
        $this->lineBot->deleteRichMenu($richMenuId);
    }

    public function getRichMenuList()
    {
        $this->lineBot->getRichMenuList();
    }

    public function getRichMenu($richMenuId)
    {
        $this->lineBot->getRichMenu($richMenuId);
    }

    public function unlinkRichMenu($userId)
    {
        $this->lineBot->unlinkRichMenu($userId);
    }
    
    public function linkRichMenu($userId, $richMenuId)
    {
        $this->lineBot->linkRichMenu($userId, $richMenuId);
    }
}
