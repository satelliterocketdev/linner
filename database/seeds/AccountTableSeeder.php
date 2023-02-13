<?php

use Illuminate\Database\Seeder;
use App\Account;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = New Account();
        $accounts->name = '開発用アカウント';
        $accounts->basic_id = "@259wamsd";
        $accounts->channel_id = '1653577169';
        $accounts->channel_secret = 'b033680d7dd05cae8ca5ff41713c5561';
        $accounts->channel_access_token = '9OCAKE6t8D/yZHl3mRQC67HbP85SQnmI1qAkwu0sJCcAYIsSRV70Jjgj3FV1FwlrbG6x1eoQpDILOKoS3h6m9niOwAPKngFnUKCSY6CwuJZUoFVX5l8eb8ls2xWm+u6F9skYCEH+vIljehlwh61++QdB04t89/1O/w1cDnyilFU=';
        $accounts->webhook_token = $accounts->channel_id;
        $accounts->bot_dest_id = '1';
        $accounts->link_token = '1';
        $accounts->line_follow_link = 'http://localhost:3000';
        $accounts->line_add = '1';
        $accounts->description = 'LINNE開発テスト用';
        $accounts->profile_image = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
        $accounts->save();

        $accounts = New Account();
        $accounts->name = '開発用サブアカウント';
        $accounts->basic_id = '@584vsyca';
        $accounts->channel_id = '1613536414';
        $accounts->channel_secret = 'f404435437029d1168cfa3aec23a851c';
        $accounts->channel_access_token = 'pMk9x1p+q7I6B5gAA8pI7e6rUXJKCk4KRBKTtDvJlXcbqIVX6qfQfskJOR/8u6ZoKFEkunKuen6Hol64FU1jge0/5R66CUDzJ1cUhxsv9YoDUR1JvxG9DXvdgKHz7hGIM35Cy0AmyUqcntIBhEovSAdB04t89/1O/w1cDnyilFU=';
        $accounts->webhook_token = $accounts->channel_id;
        $accounts->bot_dest_id = '1';
        $accounts->link_token = '1';
        $accounts->line_follow_link = 'http://localhost:3000';
        $accounts->line_add = '1';
        $accounts->description = 'LINNE開発ダミーアカウント';
        $accounts->profile_image = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
        $accounts->save();

        $accounts = New Account();
        $accounts->name = '伊藤';
        $accounts->basic_id = "@145zswvg";
        $accounts->channel_id = '16054935369';
        $accounts->channel_secret = '1f89b51ce60757eab7a8c10ca3f0fec7';
        $accounts->channel_access_token = '21l6jexBKdUVkKQb71R/ob2ZyasIBGJHz6SCc+sM84i24ZyHfe4YMATbuttL/54HSc9hu0QOpbBAC36MyOwLaiYHi9RwgdED27cCMSjwUJyY+OzvXW0n6lrRkgdxm0Kg+1EkQecH42M/S4xZ5ffPXQdB04t89/1O/w1cDnyilFU=';
        $accounts->webhook_token = $accounts->channel_id;
        $accounts->bot_dest_id = '1';
        $accounts->link_token = '1';
        $accounts->line_follow_link = 'http://localhost:3000';
        $accounts->line_add = '1';
        $accounts->description = '自己紹介';
        $accounts->profile_image = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
        $accounts->save();
    }
}
