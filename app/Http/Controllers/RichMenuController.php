<?php

namespace App\Http\Controllers;

use App\AccountFollower;
use DB;
use App\RichMenu;
use App\RichMenuItem;
use App\TagManagement;
use App\Scenario;
use App\PfUser;
use App\MediaFile;
use App\LineEvents\RichMenuEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Storage;

use Intervention\Image\ImageManagerStatic as Image;

class RichMenuController extends Controller
{
    /** @var \App\User|\Illuminate\Contracts\Auth\Authenticatable|null */
    private $user;

    /** @var RichMenuEvent */
    private $richMenuEvent;

    /** @var \Illuminate\Database\Eloquent\Relations\HasMany */
    private $richMenuTargets;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->richMenuEvent = new RichMenuEvent();
            return $next($request);
        });
    }

    public function index()
    {
        return view('richmenu');
    }

    public function actionList(Request $request, $menuType)
    {
        $richMenus = RichMenu::where('rich_menu_type', $menuType)->get();
        return response()->json($richMenus, Response::HTTP_OK);
    }

    public function lists()
    {
        $richMenuItems = $this->user->account->richMenuItems;
        foreach ($richMenuItems as $richMenuItem) {
            $richMenuItem->rich_menu_file = $richMenuItem->richMenuAttachment->rich_menu_file;
            if (\App::environment() == 'local') {
                $richMenuItem->rich_menu_file = str_replace("minio", 'localhost', $richMenuItem->rich_menu_file);
            }
        }

        foreach ($richMenuItems as $richMenuItem) {
            $serves = new \stdClass();
            $excludes = new \stdClass();
            $serveDates = new \stdClass();
            $excludeDates = new \stdClass();
            foreach ($richMenuItem->richMenuTargets as $target) {
                if (isset($target->tag_management_id)) {
                    if ($target->is_exclude == 0) {
                        $serves->tag_management_id[$target->index]['value'][] = $target->tag_management_id;
                        $serves->tag_management_id[$target->index]['option'] = $target->option;
                    } else {
                        $excludes->tag_management_id[$target->index]['value'][] = $target->tag_management_id;
                        $excludes->tag_management_id[$target->index]['option'] = $target->option;
                    }
                } elseif (isset($target->scenario_id)) {
                    if ($target->is_exclude == 0) {
                        $serves->scenario_id[$target->index]['value'][] = $target->scenario_id;
                        $serves->scenario_id[$target->index]['option'] = $target->option;
                    } else {
                        $excludes->scenario_id[$target->index]['value'][] = $target->scenario_id;
                        $excludes->scenario_id[$target->index]['option'] = $target->option;
                    }
                } elseif (isset($target->start_at) && isset($target->end_at)) {
                    if ($target->is_exclude == 0) {
                        $serveDates = new \stdClass();
                        $serveDates->from = $target->start_at;
                        $serveDates->to = $target->end_at;
                        $serves->date[$target->index]['value'] = $serveDates;
                    } else {
                        $excludeDates = new \stdClass();
                        $excludeDates->from  = $target->start_at;
                        $excludeDates->to = $target->end_at;
                        $excludes->date[$target->index]['value'] = $excludeDates;
                    }
                }
            }

            $target = new \stdClass();
            $target->tags = new \stdClass();
            $target->tags->serves = [];
            $target->scenarios = new \stdClass();
            $target->scenarios->serves = [];
            $target->dates = new \stdClass();
            $target->dates->serves = [];

            $target->tags->excludes = [];
            $target->scenarios->excludes = [];

            if (isset($serves->tag_management_id)) {
                foreach ($serves->tag_management_id as $tagManagementId) {
                    $targetTagServeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
                    $targetTagServeValues['option'] = $tagManagementId['option'];
                    $target->tags->serves[] = $targetTagServeValues;
                }
            } else {
                $targetTagServeValues['value'] = [];
                $targetTagServeValues['option'] = 'first';
                $target->tags->serves[] = $targetTagServeValues;
            }

            if (isset($serves->scenario_id)) {
                foreach ($serves->scenario_id as $scenarioId) {
                    $targetScenarioServeValues['value'] = Scenario::findMany((array)$scenarioId['value'])->pluck('name');
                    $targetScenarioServeValues['option'] = $scenarioId['option'];
                    $targetScenarioServeValues['day'] = 0;
                    $target->scenarios->serves[] = $targetScenarioServeValues;
                }
            } else {
                $targetScenarioServeValues['value'] = [];
                $targetScenarioServeValues['option'] = 'first';
                $target->scenarios->serves[] = $targetScenarioServeValues;
            }

            if (isset($serves->date)) {
                foreach ($serves->date as $date) {
                    $target->dates->serves[] = $date;
                }
            } else {
                $serveTargetDateValues['value'] = [];
                $target->dates->serves[] = $serveTargetDateValues;
            }

            if (isset($excludes->tag_management_id)) {
                foreach ($excludes->tag_management_id as $tagManagementId) {
                    $tagExcludeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
                    $tagExcludeValues['option'] = $tagManagementId['option'];
                    $target->tags->excludes[] = $tagExcludeValues;
                }
            } else {
                $tagExcludeValues['value'] = [];
                $tagExcludeValues['option'] = 'first';
                $target->tags->excludes[] = $tagExcludeValues;
            }

            if (isset($excludes->scenario_id)) {
                foreach ($excludes->scenario_id as $scenarioId) {
                    $scenarioExcludeValues['value'] = Scenario::findMany((array)$scenarioId['value'])->pluck('name');
                    $scenarioExcludeValues['option'] = $scenarioId['option'];
                    $target->scenarios->excludes[] = $scenarioExcludeValues;
                }
            } else {
                $scenarioExcludeValues['value'] = [];
                $scenarioExcludeValues['option'] = 'first';
                $target->scenarios->excludes[] = $scenarioExcludeValues;
            }

            if (isset($excludes->date)) {
                foreach ($excludes->date as $date) {
                    $target->dates->excludes[] = $date;
                }
            } else {
                $excludeTargetDateValues['value'] = [];
                $target->dates->excludes[] = $excludeTargetDateValues;
            }

            $richMenuItem->target = $target;
        }

        return response()->json([
            'richMenuItems' => $richMenuItems,
            'richMenus' => RichMenu::all()
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // $mediaFile = $this->mergeImages(json_decode($request->rich_menu_file), $request->rich_menu_type);

            /** @var RichMenuItem $richMenu */
            $richMenu = $this->user->account->richMenuItems()->create([
                'rich_menu_type' => $request->rich_menu_type,
                'title' => $request->title,
                'action_value_data' => $request->action_value_data
            ]);

            $richMenu->richMenuAttachment()->create([
                'rich_menu_file' => $request->rich_menu_file,
                // 'media_file_id' => $mediaFile->id,
                'media_file_id' => 'null',
            ]);

            $this->richMenuTargets = $richMenu->richMenuTargets();

            $target = json_decode($request->target, true);

            $richMenu->createStoreTargets($target);

            $pfUsers = $richMenu->getPfUsers(
                isset($tagsServes) ? $tagsServes : null,
                isset($scenariosServes) ? $scenariosServes : null,
                isset($dateServes) ? $dateServes : null
            );

            foreach ($pfUsers as $pfUser) {
                $pfUser->richMenuDeliveries()->create([
                    'rich_menu_item_id' => $richMenu->id
                ]);
            }

            $richMenuId = $this->richMenuEvent->sendRichMenu($richMenu);
            if ($richMenuId) {
                $richMenu->update([
                    'rich_menu_id' => $richMenuId,
                    'is_active' => count($pfUsers) > 0 ? true : false
                    ]);
            }

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit(Request $request, $id)
    {
        $richMenuItem = RichMenuItem::find($id);
        $richMenus = RichMenu::where('rich_menu_type', $richMenuItem->rich_menu_type)->get();
        return response()->json([$richMenuItem, $richMenus], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // $mediaFile = $this->mergeImages(json_decode($request->rich_menu_file), $request->rich_menu_type);

            $richmenu = RichMenuItem::find($id);

            $richmenu->update([
                'rich_menu_type' => $request->rich_menu_type,
                'title' => $request->title,
                'action_value_data' => $request->action_value_data
            ]);

            $richmenu->richMenuAttachment()->delete();
            $richmenu->richMenuAttachment()->create([
                'rich_menu_file' => $request->rich_menu_file,
                // 'media_file_id' => $mediaFile->id
                'media_file_id' => 'null',
            ]);

            $this->richMenuEvent->deleteRichMenu($richmenu->rich_menu_id);

            $richmenu->richMenuTargets()->delete();
            $target = json_decode($request->target, true);

            $richmenu->createStoreTargets($target);

            $pfUsers = $richmenu->getPfUsers(
                isset($targetTagsServes) ? $targetTagsServes : null,
                isset($targetScenariosServes) ? $targetScenariosServes : null,
                isset($dateServes) ? $dateServes : null,
                'serves'
            );

            $richmenu->richMenuDeliveries()->delete();
            foreach ($pfUsers as $pfUser) {
                $this->richMenuEvent->unlinkRichMenu($pfUser->accountFollower->source_user_id);
                $pfUser->richMenuDeliveries()->create([
                    'rich_menu_item_id' => $richmenu->id
                ]);
            }

            $richMenuId = $this->richMenuEvent->sendRichMenu($richmenu);
            if ($richMenuId) {
                $richmenu->update([
                    'rich_menu_id' => $richMenuId,
                    'is_active' => count($pfUsers) > 0 ? true : false
                    ]);
            }

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function copy(Request $request)
    {
        try {
            DB::beginTransaction();

            $richMenuItem = RichMenuItem::findOrFail($request->id);
            $richMenuItemCopy = $this->user->account->richMenuItems()->create([
                "rich_menu_type" => $richMenuItem->rich_menu_type,
                "title" => $richMenuItem->title.' - copy',
                "action_value_data" => $richMenuItem->action_value_data
            ]);

            // $originalMediaFile = MediaFile::findOrFail($richMenuItem->richMenuAttachment->media_file_id);
            // $mediaFileCopy = $this->copyFile($originalMediaFile);
            // $richMenuFilesCopy = $this->copyFiles(json_decode($richMenuItem->richMenuAttachment->rich_menu_file));
            
            $richMenuItemCopy->richMenuAttachment()->create([
                "rich_menu_file" => $richMenuItem->richMenuAttachment->rich_menu_file,
                "media_file_id" => 'null'
            ]);

            foreach ($richMenuItem->richMenuTargets as $target) {
                $targetCopy = $target->toArray();
                $richMenuItemCopy->richMenuTargets()->create(array_except($targetCopy, ['id']));
            }
            DB::commit();
            return response()->json([], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($ids)
    {
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $richMenuItem = RichMenuItem::find($id);
            $richMenuItem->richMenuTargets()->delete();
            $this->richMenuEvent->deleteRichMenu($richMenuItem->rich_menu_id);
            $this->deleteFiles($richMenuItem->richMenuAttachment);
            $richMenuItem->richMenuAttachment()->delete();
            $richMenuItem->richMenuDeliveries()->delete();
            $richMenuItem->delete();
        }
        return response()->json([], Response::HTTP_OK);
    }

    public function updateActivity(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $richMenuItem = RichMenuItem::findOrFail($id);
            $richMenuItem->update(['is_active' => $request->is_active]);
            if ($request->is_active == 1) {
                foreach ($richMenuItem->richMenuDeliveries as $richMenuDelivery) {
                    $pfUserId = $richMenuDelivery->pf_user_id;
                    $accountFollower = $this->user->account->accountFollowers->first(
                        function ($accountFollower, $key) use ($pfUserId) {
                            return $accountFollower->pf_user_id == $pfUserId;
                        }
                    );
                    $richMenuItem = $this->user->account->richMenuItems->find($richMenuDelivery->rich_menu_item_id);
                    $this->richMenuEvent->linkRichMenu($accountFollower->source_user_id, $richMenuItem->rich_menu_id);
                }
            } else {
                foreach ($richMenuItem->richMenuDeliveries as $richMenuDelivery) {
                    $pfUserId = $richMenuDelivery->pf_user_id;
                    $accountFollower = $this->user->account->accountFollowers
                        ->first(function ($accountFollower, $key) use ($pfUserId) {
                            return $accountFollower->pf_user_id == $pfUserId;
                        });
                    $this->richMenuEvent->unlinkRichMenu($accountFollower->source_user_id);
                }
            }
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function mergeImages(array $imgs, $type)
    {
        $richMenu = RichMenu::where('rich_menu_type', $type)->get();

        $source = Image::canvas(RichMenu::WIDTH, RichMenu::HEIGHT, RichMenu::COLOR);
        $bounds = $this->getTypeBounds($type);

        // foreach ($imgs as $key => $img) {
        //     $img = Image::make(file_get_contents($img))->resize($richMenu[$key]->width, $richMenu[$key]->height);
        //     if (isset($bounds[$key]['offset'])) {
        //         $source->insert(
        //             $img,
        //             $bounds[$key]['offset'][0],
        //             (int)$bounds[$key]['offset'][1],
        //             (int)$bounds[$key]['offset'][2]
        //         );
        //     } else {
        //         $source->insert($img, $bounds[$key]);
        //     }
        // }

        return $this->storeFile($source);
    }

    private function getTypeBounds($type)
    {
        switch ($type) {
            case 1:
                return ['center'];
            case 2:
                return ['left', 'right'];
            case 3:
                return ['top', 'bottom'];
            case 4:
                return ['top', 'bottom-left', 'bottom-right'];
            case 5:
                return ['left', 'top-right', 'bottom-right'];
            case 6:
                return ['top-left', 'bottom-left', 'right'];
            case 7:
                return ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
            case 8:
                return [
                    'top', 'bottom-left',
                    ['offset' => ['bottom-left', RichMenu::WIDTH / 3, 0]],
                    'bottom-right'
                ];
            case 9:
                return [
                    'top-left',
                    ['offset' => ['top-left', RichMenu::WIDTH / 3, 0]],
                    'top-right',
                    'bottom-left',
                    ['offset' => ['bottom-left', RichMenu::WIDTH / 3, 0]],
                    'bottom-right'
                ];
        }
    }

    // RichMenuの保存
    private function storeFile($source)
    {
        $storage = Storage::disk('s3');

        $source->encode('jpg');
        $filename = time() . '.jpg';
        $path = $this->user->id . '/richmenus/' . $filename;
        $storage->put($path, $source);
        $url = $storage->url($path);
        
        return MediaFile::create([
            'user_id' => $this->user->id,
            'name' => $filename, // ここなんでもいいかな？？
            'type' => 'richmenu', //　とりあえず
            'url' => $url, // thumbnailつくったがいいかな？？
            'featured_url' => $url,
            'size' => $source->filesize()
        ]);
    }

    // RichMenuのコピー
    private function copyFile($mediaFile)
    {
        $richMenu = Image::make(file_get_contents($mediaFile->url));
        return $this->storeFile($richMenu);
    }

    // Filesのコピー
    private function copyFiles($paths)
    {
        $storage = Storage::disk('s3');
        
        $mediaFilesPaths = [];
        foreach ($paths as $path) {
            $file = Image::make(file_get_contents($path));
            $file->encode('jpg');
            $pathInfo = pathinfo($path);
            $destPath = $this->user->id . '/' . $pathInfo['filename'] . '-copy.jpeg';
            $storage->put($destPath, $file);
            $url = $storage->url($destPath);

            MediaFile::create([
                'user_id' => $this->user->id,
                'name' => $pathInfo['filename'] . ' - copy',
                'type' => 'image',
                'url' => $url, // thumbnailつくったがいいかな？？
                'featured_url' => $url,
                'size' => $file->filesize()
            ]);

            $mediaFilesPaths[] = $url;
        }

        return $mediaFilesPaths;
    }

    private function deleteFiles($richMenuAttachment)
    {
        $storage = Storage::disk('s3');
        $richMenuFiles = json_decode($richMenuAttachment->rich_menu_file);

        //full pathで保存する必要はなかったの思うけどね。。。しゃーない
        foreach ($richMenuFiles as $richMenuFile) {
            $pathInfo = pathinfo($richMenuFile);
            if ($fileName = strstr($pathInfo['filename'], '_', true)) {
                $storage->delete($this->user->id.'/'.$fileName.'.'.$pathInfo['extension']);
            }
            $storage->delete($this->user->id.'/'.$pathInfo['basename']);
        }

        if ($mediaFile = $richMenuAttachment->mediaFile) {
            $richMenuFileInfo = pathinfo($richMenuAttachment->mediaFile->url);
            $storage->delete($this->user->id.'/richmenus/'.$richMenuFileInfo['basename']);

            $richMenuAttachment->mediaFile()->delete();
        }
    }
}
