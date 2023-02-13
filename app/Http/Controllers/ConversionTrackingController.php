<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Conversion;
use App\AccountFollower;
use App\ConversionTrackingUuid;
use App\ConversionTrackingRecord;
use Carbon\Carbon;

class ConversionTrackingController extends Controller
{
    private function generateUuid($fid)
    {
        return md5(uniqid('TRACK-'. $fid, true));
    }

    public function prepare($token, Request $request)
    {
        $conv = Conversion::where('conversion_token', $token)->where('is_active', 1)->first();
        if (!isset($conv)) {
            return \App::abort(404);
        }

        $fid = $request->input('fid');
        $follower = AccountFollower::find($fid);
        if (!isset($follower)) {
            // 何も返さない
            return;
        }

        $uuid = '';
        if (isset($_COOKIE['UUID'])) {
            $uuid = $_COOKIE['UUID'];
        }

        // クッキー有効期限　30日
        $cookie_expire = time()+60*60*24*30;
        $expire_at = Carbon::createFromTimestamp($cookie_expire);
        // UUIDが取得できる場合はそのまま使用する。取得できなかったり、存在しないUUIDの場合は新規に発行。
        $trackingUuid = ConversionTrackingUuid::firstOrNew(
            ['uuid' => $uuid, 'account_follower_id' => $fid]
        );
        if (!$trackingUuid->exists) {
            $trackingUuid->uuid = $this->generateUuid($fid);
        }
        $trackingUuid->expire_at = $expire_at;
        $trackingUuid->save();

        // リクエストtokenが新しいものの場合、UUID紐付け処理を行う。
        // UUIDに紐付け済みtokenの場合はそのまま使用する。（同一コンバージョンURLを複数回クリックしたケース）
        $trackingRecord = $trackingUuid->conversionTrackingRecords()->where('token', $token)->first();
        if (!isset($trackingRecord)) {
            ConversionTrackingRecord::create(
                [ 'conversion_tracking_uuid_id' => $trackingUuid->id, 'token' => $token, 'expire_at' => $expire_at]
            );
        }

        setcookie('UUID', $trackingUuid->uuid, $cookie_expire, '/cv');
        return redirect($conv->redirect_url);
    }

    public function impression(Request $request)
    {
        $token = $request->input('token');
        if (isset($token) && isset($_COOKIE['UUID'])) {
            $uuid = $_COOKIE['UUID'];
            $now = Carbon::now();
            $trackingUuid = ConversionTrackingUuid::where('uuid', $uuid)
                ->where('expire_at', '>=', $now)
                ->first();

            if (isset($trackingUuid)) {
                $trackingRecord = $trackingUuid->conversionTrackingRecords()
                    ->where('token', $token)
                    ->where('expire_at', '>=', $now)
                    ->first();
            }

            if (isset($trackingRecord)) {
                $conv = Conversion::where('conversion_token', $trackingRecord->token)->where('is_active', 1)->first();
                $follower = AccountFollower::find($trackingUuid->account_follower_id);
                if (isset($conv) && isset($follower)) {
                    // 計測人数UP
                    $conv->access_count++;
                    $conv->save();

                    // 設定されているアクションを行う。
                    $conv->executeActions($follower->pfUsers);

                    // trackingRecord削除
                    $trackingRecord->delete();
                } else {
                    // すでに削除されているCVやユーザーの場合は何もしない
                }
            }
        }

        //1x1 pixel gifを返す
        return response(base64_decode('R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='))
            ->header('Content-Type', 'image/gif');
    }
}
