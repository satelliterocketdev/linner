<?php

namespace App\Http\Controllers;

use App\Settlement;
use Hamcrest\Core\Set;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // リンクタイプインターフェース仕様説明書.pdf
        // ref: https://www.paygent.co.jp/merchant_ad/02_%E3%83%AA%E3%83%B3%E3%82%AF%E3%82%BF%E3%82%A4%E3%83%97%E3%82%A4%E3%83%B3%E3%82%BF%E3%83%BC%E3%83%95%E3%82%A7%E3%83%BC%E3%82%B9%E4%BB%95%E6%A7%98%E8%AA%AC%E6%98%8E%E6%9B%B8.pdf

        // プランから金額を取得する TODO:削除してよい？
        if (\App::environment() === 'local' && is_null($user->plan_id)) {
            $user->plan_id = 2;
            $user->save();
        }

        if ($user->request_plan_id == null) { //新規登録
            $amount = $user->plan->price;
        } else { //プラン変更（決済するのはアップグレードのみ）差額決済
            $amount = $user->request_plan->price - $user->plan->price ; // 希望プラン額 - 現在プラン額
        }

        //フリープラン且つアカウント未登録
        if ($amount === 0 && $user->account_id === null) {
            return redirect()->to('/newaccount');
        }

        // 決済テーブルにデータを登録し、IDを取得する
        $settlement = new Settlement();
        $settlement->user_id = $user->id;

        if ($user->request_plan_id == null) { //新規登録
            $settlement->plan_id = $user->plan_id;
        } else { //プラン変更（決済するのはアップグレードのみ）
            $settlement->plan_id = $user->request_plan_id;
        }

        $settlement->status = Settlement::STATUS_UNSETTLED;
        $settlement->amount = $amount;
        $settlement->save();

        $user->settlement_id = $settlement->id;
        $user->save();

        $settlement->token = hash('sha256', $amount . $settlement->id);
        //$settlement->id = rand(); //ローカルのみ
        $settlement->save();

        if (App::environment() == 'local') {
            $trading_id = date('Ymd') . $settlement->id;
        } elseif (App::environment() == 'staging') {
            $trading_id = '9' . date('Ymd') . $settlement->id;
        } else {
            $trading_id = $settlement->id;
        }

        return view('payment_method', [
            'form_action'           => env('PAYJENT_LINK_URL', 'https://sandbox.paygent.co.jp/v/e/request'),
            'trading_id'            => $trading_id,
            'payment_type'          => env('PAYJENT_PAYMENT_TYPE'),
            'fix_params'            => env('PAYJENT_FIX_PARAMS'),
            'id'                    => $amount,
            'hc'                    => $this->createHash($trading_id, $amount, $user->id),
            'seq_merchant_id'       => env('PAYJENT_MERCHANT_ID'),
            'merchant_name'         => env('PAYJENT_MERCHANT_NAME'),
            'finish_disable'        => 1,
            // カード決済個別パラメータ
            'payment_class'         => env('PAYJENT_PAYMENT_CLASS'),
            'use_card_conf_number'  => env('PAYJENT_USE_CARD_CONF_NUMBER'),
            'stock_card_mode'       => env('PAYJENT_STOCK_CARD_MODE'),
            'customer_id'           => $user->id,
            'threedsecure_ryaku'    => env('PAYJENT_THREEDSECURE_RYAKU'),
            'sales_flg'             => env('PAYJENT_SALES_FLG'),
            'appendix'              => env('PAYJENT_APPENDIX'),
            // ハッシュ値使用項目
            'payment_term_day'      => env('PAYJENT_PAYMENT_TERM_DAY'),
            'payment_term_min'      => env('PAYJENT_PAYMENT_TERM_MIN'),
        ]);
    }

    //決済失敗データを元に再決済する時に使用
    public function redo($settlement_id)
    {

        $user       = Auth::user();
        $settlement = Settlement::find($settlement_id);

        //ユーザーID check or 決済ステータス check
        if ($user->id != $settlement->user_id or $settlement->status != 2) {
            return redirect()->to('/settlement');
        }

        $settlement->token = hash('sha256', $settlement->amount . $settlement->id);
        $settlement->save();

        $amount     = $settlement->amount;
        $trading_id = $settlement->id;

        return view('payment_method', [
            'form_action'           => env('PAYJENT_LINK_URL', 'https://sandbox.paygent.co.jp/v/e/request'),
            'trading_id'            => $trading_id,
            'payment_type'          => env('PAYJENT_PAYMENT_TYPE'),
            'fix_params'            => env('PAYJENT_FIX_PARAMS'),
            'id'                    => $amount,
            'hc'                    => $this->createHash($trading_id, $amount, $user->id),
            'seq_merchant_id'       => env('PAYJENT_MERCHANT_ID'),
            'merchant_name'         => env('PAYJENT_MERCHANT_NAME'),
            'finish_disable'        => 1,
            // カード決済個別パラメータ
            'payment_class'         => env('PAYJENT_PAYMENT_CLASS'),
            'use_card_conf_number'  => env('PAYJENT_USE_CARD_CONF_NUMBER'),
            'stock_card_mode'       => env('PAYJENT_STOCK_CARD_MODE'),
            'customer_id'           => $user->id,
            'threedsecure_ryaku'    => env('PAYJENT_THREEDSECURE_RYAKU'),
            'sales_flg'             => env('PAYJENT_SALES_FLG'),
            'appendix'              => env('PAYJENT_APPENDIX'),
            // ハッシュ値使用項目
            'payment_term_day'      => env('PAYJENT_PAYMENT_TERM_DAY'),
            'payment_term_min'      => env('PAYJENT_PAYMENT_TERM_MIN'),
        ]);
    }

    public function result(Request $request, $amount, $id)
    {
        if (App::environment() == 'local') {
            $id = str_replace(date('Ymd'), '', $id);
        } elseif (App::environment() == 'staging') {
            $id = str_replace('9' . date('Ymd'), '', $id);
        }
        
        $settlement = Settlement::find($id);

        if (!Settlement::updateStatus($amount, $id)) {
            return response()->redirectTo('paymentmethod/fails');
        }

        $user = Auth::user();

        if ($user->request_plan_id != null) { //変更
            $user->plan_id         = $user->request_plan_id;
            $user->request_plan_id = null;
            $user->save();
            $request_type = "update";
        } else {
            if ($settlement->status == 2) { //再決済
                $request_type = "redo";
            } else { //新規決済
                $request_type = "register";
            }
        }

        return view('payment_method_result', ['request_type'=>$request_type]);
    }

    private function createHash($trading_id, $amount, $user_id)
    {
        // create hash hex string
        $org_str = $trading_id .
            env('PAYJENT_PAYMENT_TYPE') .
            env('`PAYJENT_FIX_PARAMS`'). // fix_params
            $amount .
            env('PAYJENT_MERCHANT_ID') .
            env('PAYJENT_PAYMENT_TERM_DAY') . // $payment_term_day .
            env('PAYJENT_PAYMENT_TERM_MIN') . // $payment_term_min .
            env('PAYJENT_PAYMENT_CLASS') . // $payment_class .
            env('PAYJENT_USE_CARD_CONF_NUMBER') . //$use_card_conf_number .
            $user_id . // $customer_id .
            env('PAYJENT_THREEDSECURE_RYAKU') . // $threedsecure_ryaku .
            env('PAYJENT_LINK_HASH_KEY'); //$hash_key;

        $hash_str = hash("sha256", $org_str);
        $rand_str = "";
        $rand_char = array('a','b','c','d','e','f','A','B','C','D','E','F','0','1','2','3','4','5','6','7','8','9');
        for ($i=0; ($i < 20 && rand(1, 10) != 10); $i++) {
            $rand_str .= $rand_char[rand(0, count($rand_char)-1)];
        }

        return $hash_str . $rand_str;
    }
}
