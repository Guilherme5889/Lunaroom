<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function viewLogin(): View
    {
        return view('auth.login');
    }

    public function viewRegister(): View
    {
        return view('auth.register');
    }
}
