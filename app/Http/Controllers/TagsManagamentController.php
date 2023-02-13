<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\TagManagement;
use App\TagsFolders;
use App\Role;
use App\RoleUser;
use App\FriendsModel;
use App\LineUserManegerSetting;
use App\FollowersProps;
use DB;
use Log;

class TagsManagamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tags');
    }

    private function convertActionLabel($d_formattedTagActions)
    {
        $d_actions = [];
        foreach ($d_formattedTagActions['tag'] as $d_tag_serve) {
            foreach ($d_tag_serve->value as $val) {
                if ($d_tag_serve->option == 'first') {
                    $d_actions[] = $val . 'を追加';
                }
                if ($d_tag_serve->option == 'second') {
                    $d_actions[] = $val . 'を除去';
                }
            }
        }
        foreach ($d_formattedTagActions['scenario'] as $d_scenario_serve) {
            foreach ($d_scenario_serve->value as $val) {
                if ($d_scenario_serve->option == 'first') {
                    $d_actions[] = $val . 'を追加';
                }
                if ($d_scenario_serve->option == 'second') {
                    $d_actions[] = $val . 'を除去';
                }
            }
        }
        return $d_actions;
    }
    /**
     * フォルダ一覧の取得
     * 指定フォルダの情報を取得
     */
    public function folders(Request $request, $folderId = null)
    {
        $user = Auth::user();
        $tags_folders = $user->account->tagsFolders();
        if ($folderId) {
            $tags_folders = $tags_folders->where('id', $folderId);
        }
        
        $data = $tags_folders->get();
        if ($request->input('with', '') == 'tags') {
            // タグやタグアクションの情報も一緒に取得
            $d_folders = $data->toArray();

            array_walk($d_folders, function (&$d_folder, $index) {
                // 参照型のd_folderに直接tag_managements等の情報を差し込む
                $tags = TagManagement::where('tag_folder_id', $d_folder['id'])->get();
                if (!isset($tags)) {
                    $d_folder['tag_managements'] = [];
                    return;
                }

                $d_tags = [];
                foreach ($tags as $tag) {
                    $d_tag = $tag->toArray();
                    // タグ人数
                    $d_tag['count_tagged_user'] = $tag->countTaggedUser();
                    $d_actions = $this->convertActionLabel($tag->getFormattedActions());
                    $d_tag['actions'] = implode(" : ", $d_actions);
                    $d_tags[] = $d_tag;
                }

                $d_folder['tag_managements'] = $d_tags;
            });
            return response()->json($d_folders);
        }
        
        return response()->json($data);
    }

    /**
     * フォルダの作成
     */
    public function storeFolder(Request $request)
    {
        $user = Auth::user();
        $this->validate(
            $request,
            ['folder_name' => ['required',
            Rule::unique('tags_folders')->where(function ($query) use ($user) {
                $query->where('account_id', $user->account_id);
            })]],
            [],
            ['folder_name' => 'フォルダ名']
        );

        $folderEntry = new TagsFolders;
        $folderEntry->account_id = $user->account->id;
        $folderEntry->folder_name = $request->input('folder_name');
        $folderEntry->save();
        return response()->json($folderEntry);
    }

    /**
     * フォルダ内容の更新
     */
    public function updateFolder($folderId, Request $request)
    {
        $user = Auth::user();
        //uniqueチェックは自身は除外
        $this->validate(
            $request,
            ['folder_name' => ['required',
            Rule::unique('tags_folders')->where(function ($query) use ($user) {
                $query->where('account_id', $user->account_id);
            })->ignore($folderId)]],
            [],
            ['folder_name' => 'フォルダ名']
        );
        
        $folder = $user->account->tagsFolders()->where('id', $folderId)->first();
        if (!isset($folder)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        
        $folder->folder_name = $request->input('folder_name');
        $folder->save();
        return response()->json($folder);
    }

    /**
     * フォルダの削除 未使用
     */
    public function deleteFolder($folderId)
    {
        $user = Auth::user();
        $folder = $user->account->tagsFolders()->where('id', $folderId)->first();
        if (!isset($folder)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        
        $folder->delete();
        return response()->json($folder);
    }

    public function batchDelete(Request $request)
    {
        $user = Auth::user();
        $folderIds = $request->input('folder_ids');
        $tagIds = $request->input('tag_ids');
        DB::transaction(function () use ($user, $folderIds, $tagIds) {
            // DB::enableQueryLog();
            foreach ($folderIds as $folderId) {
                $folder = $user->account->tagsFolders()
                    ->where('id', $folderId)
                    ->where('system_folder', '0')
                    ->first();

                if (!isset($folder)) {
                    continue;
                }
                // フォルダ内のタグについてはDeletingEventを受け取ったTagsFolder Model内で行う
                $folder->delete();
            }
            foreach ($tagIds as $tagId) {
            // ３、チェックされたタグの削除
                $tag = $user->account->tagManagements()->where('id', $tagId)->first();
                if (!isset($tag)) {
                    continue;
                }
                // タグを参照している関連テーブルの削除はDeletingEventを受け取ったTagManagement Model内で行う
                $tag->delete();
            }
            // $log = DB::getQueryLog();
            // Log::debug('TagList batch-delete');
            // Log::debug($log);
        });

        return response()->json(null, Response::HTTP_OK);
    }
    /**
     * タグ編集画面用情報の取得
     */
    public function tagInfo($tagId)
    {
        $user = Auth::user();
        $tags = $user->account->tagManagements();
        if ($tagId) {
            $tags = $tags->where('id', $tagId);
        }
        $tags_data = $tags->get();
        $data = [];
        foreach ($tags_data as $tag) {
            $d_tag = $tag->toArray();
            $d_tag['formatted_tag_actions'] = $tag->getFormattedActions();
            $data[] = $d_tag;
        }

        return response()->json($data);
    }

    public function lists($checkedFoldersIds = null)
    {
        $user = Auth::user();
        $account = $user->account;
        $tags_folders = $user->account->tagsFolders;
        if ($checkedFoldersIds != null) {
            $filtered_tags = [];

            $checkedFoldersArr = explode(",", $checkedFoldersIds);
            // $filter_tags = $account->tagManagements()->whereIn('tag_folder_id', $checkedFoldersArr)->get();

            foreach ($checkedFoldersArr as $checkedFolderId) {
                $filter_tags = TagManagement::where('tag_folder_id', $checkedFolderId)->get();
                $filtered_tags = $filter_tags;
            }

            $data=['tags_folders' => $tags_folders, 'tags' => $filter_tags];
            return response()->json($data);
        }

        // フォルダが選択されてない場合
        // TODO: tagsを空にする
        $tags = collect($user->account->tagsFolders)->flatMap->tagManagements;

        $data=['tags_folders' => $tags_folders,'tags' => $tags];
        return response()->json($data);
    }


    public function listsAllAccount($checkedFoldersIds = null, Request $request)
    {
        if ($request->input('type') == "New") {
            $user         = Auth::user();
            $accoundIds   = RoleUser::where('user_id', $user->id)->where('role_id', Role::ROLE_ACCOUNT_ADMINISTRATOR)->pluck('account_id');
            $tags_folders = TagsFolders::whereIn('account_id', $accoundIds)->get();
        } else {
            $tags_folders = TagsFolders::where('account_id', $request->input('account_id'))->get();
        }

        if ($checkedFoldersIds != null) {
            $filtered_tags = [];

            $checkedFoldersArr = explode(",", $checkedFoldersIds);

            foreach ($checkedFoldersArr as $checkedFolderId) {
                $filter_tags = TagManagement::where('tag_folder_id', $checkedFolderId)->get();
                $filtered_tags = $filter_tags;
            }

            $data=['tags_folders' => $tags_folders, 'tags' => $filter_tags];
            return response()->json($data);
        }

        // フォルダが選択されてない場合
        // TODO: tagsを空にする
        $tags = collect($tags_folders)->flatMap->tagManagements;

        $data=['tags_folders' => $tags_folders,'tags' => $tags];
        return response()->json($data);
    }

    /**
     * タグに対するアクションレコードを作成
     * @param \App\TagManagement $tag
     * @param Array $tags
     * @param string $option
     * @param int $index
     */
    private function storeTagAction($tag, $tags, $option, $index)
    {
        $tagManagementIds = Auth::user()->account->tagManagements()->whereIn('title', $tags)->pluck('id');

        foreach ($tagManagementIds as $tagManagementId) {
            $tag->tagActions()->create([
                'type' => 0,
                'tag_management_id' => $tagManagementId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    /**
     * シナリオに対するアクションレコードを作成
     * @param \App\TagManagement $tag
     * @param Array $scenarios
     * @param string $option
     * @param int $index
     */
    private function storeScenarioAction($tag, $scenarios, $option, $index)
    {
        $scenarioTargetIds = Auth::user()->account->scenarios()->whereIn('name', $scenarios)->pluck('id');

        foreach ($scenarioTargetIds as $scenarioTargetId) {
            $tag->tagActions()->create([
                'type' => 1,
                'scenario_id' => $scenarioTargetId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    private function createStoreActions($tag, $action)
    {
        if (isset($action['tags'])) {
            $tags = $action['tags'];
            $tagsServes = $tags['serves'];
            $i = 0;
            foreach ($tagsServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeTagAction($tag, $serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
        }

        if (isset($action['scenarios'])) {
            $scenarios = $action['scenarios'];
            $scenariosServes = $scenarios['serves'];
            $i = 0;
            foreach ($scenariosServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeScenarioAction($tag, $serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $this->validate(
            $request,
            ['title' => ['required',
            Rule::unique('tag_managements')->where(function ($query) use ($user) {
                $query->where('account_id', $user->account_id);
            })]],
            [],
            ['title' => 'タグ名']
        );

        $folderId = $request->input('tag_folder_id');
        if (!isset($folderId)) {
            $defaultFolder = $user->account->tagsFolders()->where('system_folder', '1')->first();
            $folderId = $defaultFolder->id;
        }

        $tags_Entry = new TagManagement;
        $tags_Entry->account_id = Auth::user()->account_id;
        $tags_Entry->tag_folder_id = $folderId;
        $tags_Entry->title = $request->input('title');
        $tags_Entry->no_limit = $request->input('no_limit');
        $tags_Entry->limit = (int)$request->input('limit');
        // tag_actionsの設定前に一旦保存。
        $tags_Entry->save();

        $action = $request->input('actions');

        $this->createStoreActions($tags_Entry, $action);

        return response()->json($tags_Entry);
    }

    /**
     * タグ内容の更新
     */
    public function updateTag($tagId, Request $request)
    {
        $user = Auth::user();
        //uniqueチェックは自身は除外
        $this->validate(
            $request,
            ['title' => ['required',
            Rule::unique('tag_managements')->where(function ($query) use ($user) {
                $query->where('account_id', $user->account_id);
            })->ignore($tagId)]],
            [],
            ['title' => 'タグ名']
        );

        $tags_Entry = $user->account->tagManagements()->where('id', $tagId)->first();
        if (!isset($tags_Entry)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        
        $tags_Entry->tag_folder_id = $request->input('tag_folder_id');
        $tags_Entry->title = $request->input('title');
        $tags_Entry->no_limit = $request->input('no_limit');
        $tags_Entry->limit = (int)$request->input('limit');
        $tags_Entry->save();

        $action = $request->input('actions');
        //tag_actionsのレコードは全削除して作成し直す
        $tags_Entry->tagActions()->delete();

        $this->createStoreActions($tags_Entry, $action);
        
        return response()->json($tags_Entry);
    }

    /**
     * 対象タグ選択で使用するタグ一覧取得処理
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags()
    {
        $tags = Auth::user()->account->tagManagements;
        return response()->json($tags);
    }
}
