<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\ClickrateItem;
use App\AccountFollower;
use App\ClickrateMessageRecord;

class ClickrateMeasurementController extends Controller
{
    public function index($token, Request $request)
    {
        $item = ClickrateItem::where('clickrate_token', $token)->first();
        if (!isset($item)) {
            return \App::abort(404);
        }

        $fid = $request->input('fid');
        $follower = AccountFollower::find($fid);
        if (!isset($follower)) {
            // 何も返さない
            return;
        }

        $mid = $request->input('mid');
        $messageRecord = ClickrateMessageRecord::find($mid);
        if (!isset($messageRecord)) {
            // 何も返さない
            return;
        }

        // アクセスカウントアップ
        $messageRecord->access_count += 1;
        $messageRecord->save();

        // 個人別アクセス
        $followerRecord = $messageRecord->clickrateFollowerRecords()->where('account_follower_id', $fid)->first();
        if (isset($followerRecord)) {
            $followerRecord->access_at = \Carbon\Carbon::now();
            $followerRecord->save();
        }

        return redirect($item->redirect_url);
    }
}
