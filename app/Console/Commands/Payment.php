<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

// DB
use App\RoleUser;
use App\Settlement;
use App\Plan;

use Carbon\Carbon;

// ペイジェントモジュール
use PaygentModule\System\PaygentB2BModule;


class Payment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment Processing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * protected $signatureで指定したコマンド打つと、この処理が実施
     * @return mixed
     */
    public function handle()
    {
        //決済対象ユーザーIDを取得（role_id==1はアカウント管理者）
        $roleUsers = RoleUser::with(['User', 'User.Plan'])->get()->where('role_id', 1);
        $now = Carbon::now(); //現在時刻

        foreach ($roleUsers as $key => $roleUser) {
            
            if ($roleUser->user->plan_id == Plan::FREE) {
                continue; //フリープランを除外
            }

            /*
            * 現在の月で、すでに自動決済（支払い済）がある場合 => 2重決済になる？ => continue
            */
            $alreadySettlement = Settlement::where('user_id', $roleUser->id)
                                            ->where('status', 1)
                                            ->where('is_auto', 1)
                                            ->whereYear('settlement_at', '=', $now->year)
                                            ->whereMonth('settlement_at', '=', $now->month)
                                            ->get();
                                            
            if (count($alreadySettlement) > 0) {
                continue;
            }

            //前回の決済を取得（自動・手動は問わない）
            $beforeSettlement = Settlement::where('user_id', $roleUser->user_id)->orderBy('settlement_at', 'desc')->first();
 
            if ($beforeSettlement != null) {
                if ($beforeSettlement->status == 1 && $beforeSettlement->plan_id != Plan::FREE) {
                    $newSettlement = $this->newSettlement($roleUser->user); //マーチャントID設定
                    $paymentResult = $this->paymentProcessing($beforeSettlement, $newSettlement) ; //決済処理
                    
                    if ($paymentResult) {
                        Settlement::where('id', $newSettlement->id)->update(['status'=> Settlement::STATUS_SETTLED,'settlement_at'=> $now]);
                    } else { //失敗フラグとする
                        Settlement::where('id', $newSettlement->id)->update(['status'=> Settlement::STATUS_FAILED,'settlement_at'=> $now]);
                    }
                } elseif ($beforeSettlement->status == 0 || $beforeSettlement->status == 2) {
                    //TODO:前回決済が完了していない or 失敗場合の処理があるか検討
                }
            }
        }
    }

    /*
    * Settlement 新規セット
    */
    public function newSettlement($user)
    {
        // 決済テーブルにデータを登録し、IDを取得する
        $settlement = new Settlement();
        $settlement->user_id = $user->id;
        $settlement->plan_id = $user->plan_id;
        $settlement->status  = Settlement::STATUS_UNSETTLED;
        $settlement->amount  = $user->plan->price;
        $settlement->is_auto = true;
        $settlement->save();
        
        //最新の決済データを保存
        $user->settlement_id = $settlement->id;
        $user->save();

        return $settlement;
    }


    /*
    * 決済処理{カードオーソリ（telegram_kind = 020）＋ 同時売上（ sales_mode = 1）}
    */
    public function paymentProcessing($beforeSettlement, $newSettlement)
    {
        require("/var/www/html/vendor/autoload.php");
        $paygent = new PaygentB2BModule();
        $paygent->init();

        /*
        * 共通ヘッダ部（ 02_PG外部インターフェース仕様説明書.pdf p.40参照）
        */
        $paygent->reqPut('merchant_id', env('PAYJENT_MERCHANT_ID')); //マーチャントID：加盟店を一意に管理するID
        $paygent->reqPut('connect_id', 'test39542');                 //接続ID
        $paygent->reqPut('connect_password', 'Xl99ocyJ4z');          //接続パスワード
        $paygent->reqPut('telegram_kind', '020');                    //電文種別ID  例）020：カードオーソリ, 022：カード決済売上
        $paygent->reqPut('telegram_version', '1.0');                 //電文バージョン番号
        $paygent->reqPut('trading_id', $newSettlement->id);          //マーチャント取引ID
        $paygent->reqPut('payment_id', '');                          //決済ID

        /*
        * 個別パラメータ（カード決済オーソリ電文 p.45参照）
        */
        $paygent->reqPut('payment_amount', $newSettlement->amount);   //決済金額（必須）TODO::データひっぱってくる
        $paygent->reqPut('payment_class', 10);                        //支払区分（必須）10（1回払い）
        $paygent->reqPut('3dsecure_ryaku', 1);                        //3Dセキュア不要区分（条件付必須）
        $paygent->reqPut('3dsecure_use_type', 1);                     //3Dセキュア利用タイプ
        //$paygent->reqPut('http_accept', 1);                         //HttpAccept（3Dセキュアにおいて必要です。）
        //$paygent->reqPut('http_user_agent', 1);                     //HttpUserAgent（3Dセキュアにおいて必要です。）
        //$paygent->reqPut('term_url', 1);                            //3-Dセキュア戻りURL（3Dセキュア実施時は、必須です。）
        $paygent->reqPut('ref_trading_id', $beforeSettlement->id);    //参照マーチャント取引ＩＤ（前回の決済からカード情報を取得するために必須）
        $paygent->reqPut('stock_card_mode', 0);                       //カード情報お預かりモード
        $paygent->reqPut('customer_id', null);                        //顧客ID（カード情報お預り機能のための拡張パラメータです。）
        $paygent->reqPut('customer_card_id', null);                   //顧客カードID（カード情報お預り機能のための拡張パラメータです。）
        $paygent->reqPut('site_id', null);                            //サイトID
        //$paygent->reqPut('card_token', $request->token);            //カード情報トークン
        $paygent->reqPut('sales_mode', 1);                            //同時売上モード
        $paygent->reqPut('security_code_token', 0);                   //セキュリティーコードトークン利用
        $paygent->reqPut('security_code_use', 0);                     //セキュリティコード利用

        return $paygent->post();

        //処理結果 0=正常終了, 1=異常終了
        //$resultStatus = $paygent->getResultStatus(); 
        
        //異常終了時、レスポンスコードが取得できる
        //$responseCode   = $paygent->getResponseCode(); 
        
        //異常終了時、レスポンス詳細が取得できる
        //$responseDetail = $paygent->getResponseDetail(); 

    }

}
