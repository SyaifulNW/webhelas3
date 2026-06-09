<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect user setelah login sukses ke halaman selamat datang / pemilihan menu.
     */
    protected function authenticated(Request $request, $user)
    {
        // Jika login dari halaman SMI
        if ($request->has('smi_login')) {
            return redirect('/home');
        }

        return redirect('/');
    }



    /**
     * Override: jika login gagal tampilkan SweetAlert.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('loginError', 'Email atau password yang Anda masukkan salah!');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override credentials to check is_active status.
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['is_active'] = 1;
        return $credentials;
    }
}
