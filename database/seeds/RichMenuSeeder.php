<?php

use Illuminate\Database\Seeder;
use App\RichMenu;
use App\Account;
use App\MediaFile;
use \Storage as Storage;

use Intervention\Image\ImageManagerStatic as Image;

class RichMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // APIにリクエストする場合のリッチメニューの幅と高さは2500×1686pxまたは2500×843px
        // https://dev.classmethod.jp/etc/line-messaging-api-rich-menu-tutorial/
        
        //　パターン
        $types = [
            //1枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH,
                    "height" => RichMenu::HEIGHT
                ]
            ],
            //2枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT
                ]
            ],
            //３枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ],
            // 4枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => 0,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ],
            // ５枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ],
            // ６枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => 0,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::HEIGHT / 2,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT
                ]
            ],
            // ７枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => 0,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 2,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 2,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ],
            // ８枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => 0,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 3,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => (RichMenu::WIDTH / 2) + (RichMenu::WIDTH / 3 / 2),
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ],
            // ９枚目
            [
                [
                    "x" => 0,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 3,
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => (RichMenu::WIDTH / 2) + (RichMenu::WIDTH / 3 / 2),
                    "y" => 0,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => 0,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => RichMenu::WIDTH / 3,
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ],
                [
                    "x" => (RichMenu::WIDTH / 2) + (RichMenu::WIDTH / 3 / 2),
                    "y" => RichMenu::HEIGHT / 2,
                    "width" => RichMenu::WIDTH / 3,
                    "height" => RichMenu::HEIGHT / 2
                ]
            ]
        ];

        foreach ($types as $key => $type) {
            foreach ($type as $image) {
                RichMenu::create([
                    "rich_menu_type" => $key + 1,
                    "x" => $image["x"],
                    "y" => $image["y"],
                    "width" => $image["width"],
                    "height" => $image["height"]
                ]);
            }
        }

        // とりあえず
        $source = Image::canvas(2500, 1686, '#ccc');
        $storage = Storage::disk('s3');

        $source->encode('jpg');
        $filename = time() . '.jpg';
        $storage->put($filename, $source);
        $url = $storage->url($filename);

        $mediaFile = MediaFile::create([
            'name' => 'sample',
            'type' => 'richmenu',
            'url' => $url,
            'featured_url' => $url
        ]);

        $accounts = Account::all();
        foreach ($accounts as $account) {
            $richMenuType = 1;
            foreach ($types as $type) {
                $images = [];
                $actions = [];
                for ($i = 0; $i < count($type); $i++) {
                    $images[] = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
                    $action = new \stdClass();
                    $action->type = "uri";
                    $action->uri = "https://www.google.com/";
                    $actions[] = $action;
                }
                $richMenuItem = $account->richMenuItems()->create([
                    'rich_menu_type' => $richMenuType,
                    'title' => 'layoutNo '.$richMenuType++,
                    'action_value_data' => json_encode($actions)
                ]);
                $richMenuItem->richMenuAttachment()->create([
                    'rich_menu_file' => json_encode($images),
                    'media_file_id' => $mediaFile->id,
                ]);
            }
        }
    }
}
