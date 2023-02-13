<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Conversion;

class ConversionController extends Controller
{
    private function convertActionLabel($d_formattedTagActions)
    {
        $d_tag_actions = [];
        foreach ($d_formattedTagActions['tag'] as $d_tag_serve) {
            foreach ($d_tag_serve->value as $val) {
                if ($d_tag_serve->option == 'first') {
                    $d_tag_actions[] = $val . 'を追加';
                }
                if ($d_tag_serve->option == 'second') {
                    $d_tag_actions[] = $val . 'を除去';
                }
            }
        }
        $d_scenario_actions = [];
        foreach ($d_formattedTagActions['scenario'] as $d_scenario_serve) {
            foreach ($d_scenario_serve->value as $val) {
                if ($d_scenario_serve->option == 'first') {
                    $d_scenario_actions[] = $val . 'を追加';
                }
                if ($d_scenario_serve->option == 'second') {
                    $d_scenario_actions[] = $val . 'を除去';
                }
            }
        }
        return ['tags' => $d_tag_actions, 'scenarios' => $d_scenario_actions];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('conversion');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * タグに対するアクションレコードを作成
     * @param \App\Conversion $conv
     * @param Array $tags
     * @param string $option
     * @param int $index
     */
    private function storeTagAction($conv, $tags, $option, $index)
    {
        $tagManagementIds = Auth::user()->account->tagManagements()->whereIn('title', $tags)->pluck('id');

        foreach ($tagManagementIds as $tagManagementId) {
            $conv->conversionActions()->create([
                'type' => 0,
                'tag_management_id' => $tagManagementId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    /**
     * シナリオに対するアクションレコードを作成
     * @param \App\Conversion $conv
     * @param Array $scenarios
     * @param string $option
     * @param int $index
     */
    private function storeScenarioAction($conv, $scenarios, $option, $index)
    {
        $scenarioTargetIds = Auth::user()->account->scenarios()->whereIn('name', $scenarios)->pluck('id');

        foreach ($scenarioTargetIds as $scenarioTargetId) {
            $conv->conversionActions()->create([
                'type' => 1,
                'scenario_id' => $scenarioTargetId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    private function createStoreActions($conv, $action)
    {
        if (isset($action['tags'])) {
            $tags = $action['tags'];
            $tagsServes = $tags['serves'];
            $i = 0;
            foreach ($tagsServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeTagAction($conv, $serve['value'], $serve['option'], $i);
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
                    $this->storeScenarioAction($conv, $serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $conversion = new Conversion;
        $conversion->title = $request->title;
        $conversion->access_count = 0;
        $conversion->is_active = 0;
        $conversion->conversion_token = $request->conversion_token;
        $conversion->redirect_url = $request->redirect_url;

        // actionsの設定前に一旦保存。
        $user->conversions()->save($conversion);

        $action = $request->input('actions');

        $this->createStoreActions($conversion, $action);

        return response()->json($conversion);
    }

    public function generateToken()
    {
        $token = md5(uniqid('CONV'. mt_rand(), true));
        $data = [
            'conversion_token' => $token,
            'url' => route('conversion.route', ['token' => $token])
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function start(Request $request)
    {
        try {
            $user = Auth::user();
            $conversions = $user->conversions()->whereIn('id', $request->ids)->get();
            foreach ($conversions as $conversion) {
                $conversion->is_active = 1;
                $conversion->save();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    public function stop(Request $request)
    {
        try {
            $user = Auth::user();
            $conversions = $user->conversions()->whereIn('id', $request->ids)->get();
            foreach ($conversions as $conversion) {
                $conversion->is_active = 0;
                $conversion->save();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $convs_data = $user->conversions()->where('id', $id)->first();
        
        if (isset($convs_data)) {
            $d_conv = $convs_data->toArray();
            $d_conv['formatted_conversion_actions'] = $convs_data->getFormattedActions();
            $d_conv['url'] = route('conversion.route', ['token' => $convs_data->conversion_token]);
            return response()->json($d_conv);
        }

        return response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $conversion = $user->conversions()->where('id', $id)->first();
        if (!isset($conversion)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        
        $conversion->title = $request->title;
        $conversion->redirect_url = $request->redirect_url;
        $conversion->save();

        $action = $request->input('actions');
        //tag_actionsのレコードは全削除して作成し直す
        $conversion->conversionActions()->delete();

        $this->createStoreActions($conversion, $action);
        
        return response()->json($conversion);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // 論理削除
        $user = Auth::user();
        $conversion = $user->conversions()->where('id', $id)->first();
        if (!isset($conversion)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        $conversion->delete();
        return response()->json($conversion);
    }

    public function lists()
    {
        $conversions = Auth::user()->conversions()->get();

        $data = [];
        foreach ($conversions as $conversion) {
            $d_conv = $conversion->toArray();
            // アクション
            $d_conv['actions'] = $this->convertActionLabel($conversion->getFormattedActions());
            $d_conv['url'] = route('conversion.route', ['token' => $conversion->conversion_token]);
            $d_conv['tag_code'] = '<img src="'. route('conversion.imagetag', ['token' => $conversion->conversion_token]). '"/>';
             
            $data[] = $d_conv;
        }
        
        return response()->json($data, Response::HTTP_OK);
    }
}
