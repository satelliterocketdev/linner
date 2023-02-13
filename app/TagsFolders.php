<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TagsFolders
 *
 * @property int $id
 * @property int $account_id
 * @property string|null $folder_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $system_folder 0=通常,1=未分類フォルダ
 * @property-read \App\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TagManagement[] $tagManagements
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereFolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereSystemFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TagsFolders whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TagsFolders extends Model
{
    protected $table = 'tags_folders';
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tagManagements()
    {
        return $this->hasMany(TagManagement::class, 'tag_folder_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($folder) {
            // フォルダに紐づくタグの削除
            $tags = $folder->tagManagements;
            foreach ($tags as $tagInFolder) {
                $tagInFolder->delete();
            }
        });
    }
}
