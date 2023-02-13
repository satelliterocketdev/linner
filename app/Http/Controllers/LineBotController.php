<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\LineEvents\EventHandler;
use Illuminate\Support\Facades\Auth;
use App\LineUserManegerSetting;
use App\LineEvents\MessageEvent;

//line webhooks controller
class LineBotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)//EventHandler $lineEvent
    {
        //
    }

    public function sendMsg(Request $request)
    {
        $type = $request['type']; // magazine or scenario が入る
        $msg = $request->getContent();
        $lineMsg = new MessageEvent();
        return $lineMsg->sendMsgToTester(json_decode($msg)->message, $type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function lineHooks(Request $request, $webhook_token)
    {
        if (\App::environment() == 'local' || $request->header('X-Line-Signature')) {
            $httpRequestBody = json_decode($request->getContent());
            if (isset($httpRequestBody->destination)) {
                $handler = new EventHandler();
                $response = $handler->sellectEvent($request->getContent(), $request->header('X-Line-Signature'), $webhook_token);
                return $response;
            } else {
                Log::info("NO destination request");
                //get all user
            }
            return Response::json([], 200);
        } else {
            Log::info("invalid user access");
            return Response::json([], 404);
        }
    }
}
