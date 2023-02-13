<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\MessageUrl;
use App\MessageUrlAction;
use App\AccountFollower;

class UrlClickController extends Controller
{
    public function index(Request $request)
    {
        $cid = $request->input('cid');
        $fid = $request->input('fid');
        if (isset($cid) && isset($fid)) {
            $messageurl = MessageUrl::find($cid);
            $follower = AccountFollower::find($fid);
            // action 実行
            if (isset($messageurl) && isset($follower)) {
                $messageurl->executeActions($follower->pfUsers);
                return redirect($messageurl->url);
            }
        }
    }
}
