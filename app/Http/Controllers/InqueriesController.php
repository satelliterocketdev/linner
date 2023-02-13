<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Inquery;
use DB;

class InqueriesController extends Controller
{
    public function index()
    {
        return view("inqueries");
    }

    public function lists()
    {
        $inqueries = Inquery::where('account_id', Auth::user()->account->id)->get();
        return response()->json($inqueries, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $user->account->inqueries()->create($request->all());
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
