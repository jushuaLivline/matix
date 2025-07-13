<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Mail\PasswordReminderMail;
use App\Mail\IdReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login()
    {
        return view('pages.auth.login_form');
    }

    public function processLogin(Request $request)
    {
        $user = Employee::where('employee_code', $request->name)->first();

        if (!$user) {
            return redirect()->back()->with("error", "ユーザーIDかパスワードに誤りがあります")->withInput($request->input());
        }

        if ($user->password == null) {
            $params = ['id' => Crypt::encryptString($user->id)];
            return redirect()->route("auth.resetPassword.index", $params);
        }

        if (Hash::check($request->password, $user->password)) {
            Auth::loginUsingId($user->id);
            if ($request->rememberId) {

                return redirect()->intended('dashboard')->with('remember', '1');
                // return redirect()->route('dashboard.index')->with('remember', '1');
            }
            return redirect()->intended('dashboard')->with('remember', '0');
        }

        return redirect()->back()->with("error", "ユーザーIDかパスワードに誤りがあります")->withInput($request->input());
    }

    public function complete(Request $request)
    {
        if ($request->type === 'id') {
            return view('notifies.complete_id');
        } else if ($request->type === 'password') {
            return view('notifies.complete_password');
        }
        return view('notifies.complete');
    }

    public function logout()
    {
        Session::flush();

        Auth::logout();

        return redirect(route('auth.login'));
    }
}
