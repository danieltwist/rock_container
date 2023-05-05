<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\Task;
use App\Models\WorkRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Yandex\Disk\DiskClient;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @param Guard $auth
     * @return void
     */
    public function boot(Guard $auth) {
        view()->composer('*', function($view) use ($auth) {

            $currentUser = $auth->user();

            if($auth->user()){

                $role = $currentUser->getRoleNames()[0];

                if(!isset($_COOKIE['role'])) {
                    if (in_array($role, ['super-admin','director'])) {
                        setcookie("role", '3d4e992d8d8a7d848724aa26ed7f417697d495c42bda72e0ccf02d4c4294161d52380db7e19ed2885174d868a763fe93');
                    }
                    else {
                        setcookie("role", 'b2cc53c66b4d0388f4f3fd1a6e9e788d1c2e2399c23a79e790c9501629c03994f551ddd5657bcdefdd8da8656321768c');
                    }
                }

                $view->with([
                    'current_user_avatar' => Storage::url($currentUser->avatar),
                    'current_user_name' => $currentUser->name,
                    'current_user_position' => $currentUser->position,
                    'current_user_id' => $currentUser->id,
                    'role' => $role,
                    'agree_invoice_users_count' => Setting::where('name', 'agree_invoice_users_count')->first()->toArray()['value']
                ]);
            }

        });
    }
}
