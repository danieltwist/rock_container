<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            app()->setLocale(auth()->user()->language);
        }
        if(!is_null(\Auth::user())) {
            app()->setLocale(auth()->user()->language);
            session()->put('locale', auth()->user()->language);
        }
        return $next($request);
    }
}
