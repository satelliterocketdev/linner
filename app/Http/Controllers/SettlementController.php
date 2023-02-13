<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Settlement;
use Carbon\Carbon;

use TCPDF;
use TCPDF_FONTS;

class SettlementController extends Controller
{
    public function index()
    {
        return view("settlement");
    }

    public function lists()
    {
        $user = Auth::user();
        $settlements = $user->settlements;
        foreach ($settlements as $settlement) {
            $settlement->plan_level = $settlement->plan->level;
        }

        $latest = null;
        if ($user->settlement_id != null) {
            $latest = Settlement::findOrFail($user->settlement_id);
            $latest->plan_level = $latest->plan->level;

            // -- 次回決済日時を作成（毎月25日） -- //
            $latest_date = new Carbon($latest->created_at);
            $latest_day   = $latest_date->format('d');
            if ($latest_day >= 25) {
                // 日付が25日以降であれば、翌月の25日を設定
                $next_date  = $latest_date->addMonths(1);
                $next_year  = $next_date->format('Y');
                $next_month = $next_date->format('m');
            } else {
                // 日付が24日以前であれば、当月の25日を設定
                $next_date  = $latest_date;
                $next_year  = $next_date->format('Y');
                $next_month = $next_date->format('m');
            }
            $latest->next_date = new Carbon($next_year.'-'.$next_month.'-25');
            $latest->next_date = $latest->next_date->format("Y-m-d H:i:s");
            Log::debug($latest->next_date);
        }

        $data = [
            'settlements' => $settlements,
            'latest' => $latest
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function downloadPdf($id, $client)
    {
        try {
            $settlement = Settlement::findOrFail($id);

            if ($settlement->printed == 1) {
                return;
            }
            $settlement->update(['printed' => true]);

            $settlement->client = $client;

            // PDF 生成メイン　－　A4 縦に設定
            $pdf = new TCPDF("P", "mm", "A4", true, "UTF-8");
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // PDF プロパティ設定
            $pdf->SetTitle('領収書');
            $pdf->SetAuthor('LINNER');
            $pdf->SetSubject('領収書');
            $pdf->SetKeywords('領収書');
            $pdf->SetCreator('LINNER');

            // 日本語フォント設定
            $pdf->setFont('kozminproregular', '', 10);

            // ページ追加
            $pdf->addPage();

            // HTMLを描画、viewの指定と変数代入
            $pdf->writeHTML(view("receipt_pdf", ['settlement' => $settlement])->render());

            // 出力指定 ファイル名、拡張子、D(ダウンロード)
            $pdf->output('receipt' . '.pdf', 'D');

            return;
        } catch (Exception $e) {
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
