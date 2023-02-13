<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PfUserTagManagement;
use App\ScenarioTarget;
use App\MagazineTarget;
use App\ScenarioAction;
use App\TagAction;
use App\ActionTrait;

/**
 * App\TagManagement
 *
 * @property int $id
 * @property int|null $tag_folder_id
 * @property string $title
 * @property int $no_limit
 * @property string|null $action
 * @property int $limit
 * @property string|null $condition
 * @property string|null $followerslist
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $account_id
 * @property-read \App\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MagazineTarget[] $magazineTargets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PfUserTagManagement[] $pfUserTagManagements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TagAction[] $tagActions
 * @property-read \App\TagsFolders $tagsFolder
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereFollowerslist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereNoLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereTagFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagManagement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TagManagement extends Model
{
    use ActionTrait;

    protected $table = 'tag_managements';
    
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($tag) {
            // 削除しようとしているタグを参照しているデータを削除
            PfUserTagManagement::where('tag_managements_id', $tag->id)->delete();
            ScenarioTarget::where('tag_management_id', $tag->id)->delete();
            MagazineTarget::where('tag_management_id', $tag->id)->delete();
            MagazineAction::where('tag_management_id', $tag->id)->delete();
            ScenarioAction::where('tag_management_id', $tag->id)->delete();
            TagAction::where('tag_management_id', $tag->id)->delete();
            ConversionAction::where('tag_management_id', $tag->id)->delete();
            
            // 子テーブル削除
            $actions = $tag->tagActions;
            foreach ($actions as $action) {
                $action->delete();
            }
        });
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tagsFolder()
    {
        return $this->belongsTo(TagsFolders::class);
    }

    public function tagActions()
    {
        return $this->hasMany(TagAction::class, 'source_tag_management_id');
    }

    public function magazineTargets()
    {
        return $this->hasMany(MagazineTarget::class);
    }

    public function pfUserTagManagements()
    {
        return $this->hasMany(PfUserTagManagement::class, 'tag_managements_id');
    }

    public function getActions()
    {
        return $this->tagActions();
    }

    public function countTaggedUser()
    {
        return $this->pfUserTagManagements()->count();
    }
}
