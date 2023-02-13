<?php

namespace App\Http\Controllers;

use App\LineEvents\LineUtils;
use App\Role;
use App\RoleUser;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\User;
use App\Account;

class LineAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('line_accounts');
    }

    public function lists()
    {
        $user = Auth::user();
        $accountIds = $user->roleUsers()->distinct()->pluck('account_id');

        $accounts = Account::whereIn('id', $accountIds)->get();

        foreach ($accounts as $account) {
            // TODO: プラン名の取得処理
            $account->plan_name = "プラン名";
        }
        // TODO: 決済情報取得

        return response()->json(["user" => $user, "accounts" => $accounts], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $account = $request->toArray();
            $account = Account::create($account);

            $roleUser = new RoleUser;
            $roleUser->role_id = Role::ROLE_ACCOUNT_ADMINISTRATOR;
            $roleUser->account_id = $account->id;
            $roleUser->user_id = Auth::id();
            $roleUser->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->roleUsers()->delete();
            $account->accountFollowers()->delete();

            foreach ($account->accountMessages as $accountMessage) {
                $accountMessage->accountMessageAttachments()->delete();
            }
            $account->accountMessages()->delete();

            foreach ($account->scenarios as $scenario) {
                foreach ($scenario->scenarioMessages as $scenarioMessage) {
                    $scenarioMessage->messageAttachments()->delete();
                    $scenarioMessage->deliveries()->delete();
                }
                $scenario->scenarioMessages()->delete();
                $scenario->scenarioTargets()->delete();
            }
            $account->scenarios()->delete();

            foreach ($account->magazines as $magazine) {
                $magazine->magazineTargets()->delete();
                $magazine->magazineDeliveries()->delete();
                $magazine->magazineAttachments()->delete();
            }
            $account->magazines()->delete();
            
            foreach ($account->templateMessages as $templateMessage) {
                $templateMessage->templateMessageAttachments()->delete();
            }
            $account->templateMessages()->delete();
            $account->inqueries()->delete();
            $account->delete();
            
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function selectAccount($id)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $user->account_id = $id;
            $user->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $account = Account::findOrFail($id);
            $account->fill($request->toArray());
            $account->save();

            DB::commit();
            return response()->json($account, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            // TODO: エラー処理
            return response()->json(null, Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();
            // TODO: エラー処理
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
    }
}
