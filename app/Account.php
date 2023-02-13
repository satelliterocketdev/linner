<?php

namespace App;

use App\TagsFolders;
use DB;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Account
 *
 * @property int $id
 * @property string|null $name
 * @property int $channel_id
 * @property string $channel_secret
 * @property string $channel_access_token
 * @property string $webhook_token
 * @property string $bot_dest_id
 * @property string|null $link_token
 * @property string|null $line_follow_link
 * @property string|null $line_add
 * @property string|null $description
 * @property string|null $profile_image
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $account_user_id
 * @property string $basic_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountFollower[] $accountFollowers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountMessageAttachment[] $accountMessageAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountMessage[] $accountMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountNotification[] $accountNotifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AutoAnswer[] $autoAnswers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ClickrateItem[] $clickrateItems
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inquery[] $inqueries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\InvitationEmail[] $invitationEmails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Magazine[] $magazines
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RichMenuItem[] $richMenuItems
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RoleUser[] $roleUsers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Scenario[] $scenarios
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Source[] $sources
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Survey[] $surveys
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TagManagement[] $tagManagements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TagsFolders[] $tagsFolders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountMessageAttachment[] $talkAttachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountMessage[] $talksByTimestamp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TemplateMessage[] $templateMessages
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereAccountUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereBasicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereBotDestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereChannelAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereChannelSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereLineAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereLineFollowLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereLinkToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Account whereWebhookToken($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    protected $guarded = ['id'];

    public function accountFollowers()
    {
        return $this->hasMany(AccountFollower::class);
    }

    public function roleUsers()
    {
        return $this->hasMany('App\RoleUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function magazines()
    {
        return $this->hasMany(Magazine::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagsFolders()
    {
        return $this->hasMany(TagsFolders::class);
    }

    /**
     * Deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagManagements()
    {
        return $this->hasMany(TagManagement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function talksByTimestamp()
    {
        return $this->hasMany(AccountMessage::class)->orderBy('timestamp', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function talkAttachments()
    {
        return $this->hasMany(AccountMessageAttachment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function autoAnswers()
    {
        return $this->hasMany(AutoAnswer::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    public function save(array $options = [])
    {
        if (empty($this->webhook_token)) {
            $this->webhook_token = bin2hex(openssl_random_pseudo_bytes(16));
        }
        if (empty($this->bot_dest_id)) {
            $this->bot_dest_id = $this->account_user_id;
        }
        // アカウントIDのNullチェック
        if (!is_null($this->id)) {
            // 「未分類のフォルダ」存在チェック
            $isTagFolders = DB::table('tags_folders')->where('account_id', '=', $this->id)->where('folder_name', '=', "未分類のフォルダ")->exists();
            if (!$isTagFolders) {
                try {
                    $tags_Entry = new TagsFolders;
                    $tags_Entry->account_id = $this->id;
                    $tags_Entry->folder_name = "未分類のフォルダ";
                    $tags_Entry->system_folder = 1;
                    $tags_Entry->save();
                    return parent::save($options); // TODO: Change the autogenerated stub
                } catch (Exception $e) {
                    // Exception時の処理
                }
            }
        } else {
            // なければセーブ後に走らせる
            parent::save($options); // TODO: Change the autogenerated stub
            $tags_Entry = new TagsFolders;
            $tags_Entry->account_id = $this->id;
            $tags_Entry->folder_name = "未分類のフォルダ";
            $tags_Entry->system_folder = 1;
            return $tags_Entry->save();
        }
        return parent::save($options); // TODO: Change the autogenerated stub
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountMessages()
    {
        return $this->hasMany(AccountMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountMessageAttachments()
    {
        return $this->hasMany(AccountMessageAttachment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function templateMessages()
    {
        return $this->hasMany(TemplateMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inqueries()
    {
        return $this->hasMany(Inquery::class);
    }
    
    public function clickrateItems()
    {
        return $this->hasMany(ClickrateItem::class);
    }
    
    public function accountNotifications()
    {
        return $this->hasMany(AccountNotification::class);
    }

    public function richMenuItems()
    {
        return $this->hasMany(RichMenuItem::class);
    }

    public function invitationEmails()
    {
        return $this->hasMany(InvitationEmail::class);
    }
}
