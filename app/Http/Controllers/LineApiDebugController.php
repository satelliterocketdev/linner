<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineApiDebugController extends Controller
{
    public function show()
    {
        return view('lineApiDebug')
            ->with('webhook_token', \Auth::user()->account->webhook_token);
    }
}
