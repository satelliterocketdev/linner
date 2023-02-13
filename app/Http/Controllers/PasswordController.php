<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function index(Request $request)
    {
        return view("password");
    }

    public function update(Request $request)
    {
        try {
            $this->validate($request, $this->rules());

            Auth::user()->fill([
                'password' => bcrypt($request->input('password')),
                'remember_token' => Str::random(60),
            ])->save();

            return redirect('logout');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator);
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'password' => 'required|string|min:6|confirmed|alpha_num',
        ];
    }
}
