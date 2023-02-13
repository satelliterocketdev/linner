<?php

namespace App\Http\Controllers;

use App\InvitationEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendMailCred;

class InvitationEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('invitation_email');
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
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            Auth::user()->account->invitationEmails()->create($request->all());
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvitationEmail  $invitationEmail
     * @return \Illuminate\Http\Response
     */
    public function show(InvitationEmail $invitationEmail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvitationEmail  $invitationEmail
     * @return \Illuminate\Http\Response
     */
    public function edit(InvitationEmail $invitationEmail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $invitation = InvitationEmail::findOrFail($id);
            DB::beginTransaction();
            $invitation->update($request->all());
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvitationEmail  $invitationEmail
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvitationEmail $invitationEmail)
    {
        //
    }

    public function lists()
    {
        $invitations = Auth::user()->account->invitationEmails;
        foreach ($invitations as $invitation) {
            $invitation->destination = json_decode($invitation->destination);
        }
        return response()->json(Auth::user()->account->invitationEmails, Response::HTTP_OK);
    }

    public function sendEmails($id)
    {
        $invitation = InvitationEmail::findOrFail($id);
        if ($invitation->destination) {
            \Mail::to(json_decode($invitation->destination))->send(new SendMailCred($invitation));
        }
    }
}
