<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.index');
    }

    public function auth(Request $request)
    {
        $formFields = $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
            'password' => 'required|string'
        ]);

        if (auth()->attempt($formFields)) {
            $request->session()->regenerate();

            return redirect('/admin')->with('success', 'شما وارد شدید');
        }

        return back()->withErrors(['mobile' => 'شماره موبایل یا رمز عبور اشتباه است'])->onlyInput('mobile');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login')->with('success', 'با موفقیت خارج شدید!');
    }
    
}
