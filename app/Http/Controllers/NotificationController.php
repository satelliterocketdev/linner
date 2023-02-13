<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        return view('notification');
    }

    /**
     * お知らせ一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function lists()
    {
        $user = Auth::user();
        $accountNotifications = $user->account->accountNotifications;

        $notifications = [];
        foreach ($accountNotifications->sortByDesc('created_at') as $accountNotification) {
            $notification = $accountNotification->notification;
            $notification->is_read = $accountNotification->is_read;
            $notifications[] = $notification;
        }

        return response()->json($notifications, Response::HTTP_OK);
    }

    public function page(Request $request)
    {
        $notifications = [];
        foreach (Auth::user()->account->accountNotifications->sortByDesc('created_at') as $accountNotification) {
            $notification = $accountNotification->notification;
            $notification->is_read = $accountNotification->is_read;
            $notifications[] = $notification;
        }
        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($notifications, $offset, $perPage, true),
            count($notifications),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function show($id)
    {
        try {
            $notification = $this->updateNotification($id);
        } catch (ModelNotFoundException $e) {
            \Log::error(['error:' => $e ]);
            return view('notification');
        }

        return view('notification', ['notification' => $notification]);
    }

    public function update(Request $request, $id)
    {
        try {
            $notification = $this->updateNotification($id);
        } catch (ModelNotFoundException $e) {
            \Log::error(['error:' => $e ]);
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_OK);
    }


    private function updateNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $accountNotification = $notification->accountNotification;
        $accountNotification->is_read = 1;
        $accountNotification->save();
        return $notification;
    }
}
