<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use FinanceTrait;

    public function all()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        return view('user.index',[
            'users' => $users,
        ]);
    }

    public function create_user()
    {
        $roles = Role::get()->whereNotIn('name', ['super-admin']);

        return view('user.create',[
            'roles' => $roles,
        ]);
    }

    public function store_new_user(Request $request)
    {
        $user = new User();
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->name = $request->name;
        $user->position = $request->position;
        $user->birthday = $request->birthday;
        $user->folder_on_yandex_disk = $request->folder_on_yandex_disk;

        $user->save();

        $user->assignRole($request->role);

        return redirect()->back()->withSuccess(__('user.added_successfully'));
    }

    public function edit_user($id)
    {
        $user = User::find($id);
        $roles = Role::get()->whereNotIn('name', ['super-admin']);
        $user->role = $user->roles->pluck('name')[0];
        $all_permissions = Permission::all();
        $role_permissions = $user->getPermissionsViaRoles();

        return view('user.edit',[
            'user' => $user,
            'roles' => $roles,
            'all_permissions' => $all_permissions,
            'role_permissions' => $role_permissions
        ]);
    }

    public function update_user(Request $request, $id)
    {
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->position = $request->position;
        $user->birthday = $request->birthday;

        $user->folder_on_yandex_disk = $request->folder_on_yandex_disk;

        if ($request->type == 'change_password'){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $user->removeRole($user->roles->pluck('name')[0]);
        $user->assignRole($request->role);

        return redirect()->back()->withSuccess(__('user.updated_successfully'));

    }

    public function update_profile(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if ($request->type == 'change_password'){
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        $update_success = true;

        if($request->notification_channel == 'Telegram'){
            if(!is_null($user->telegram_chat_id)){
                $user->update([
                    'notification_channel' => $request->notification_channel
                ]);
            }
            else {
                $update_success = false;
            }
        }
        else {
            $user->update([
                'notification_channel' => $request->notification_channel
            ]);
        }

        if($update_success)
            return redirect()->back()->withSuccess(__('user.updated_successfully'));
        else
            return redirect()->back()->withError(__('user.first_link_telegram'));

    }

    public function update_user_permissions(Request $request)
    {
        $user = User::find($request->user_id);
        $message = '';

        if($request->action == 'add_permission'){
            $user->givePermissionTo($request->permission_name);
            $message = __('user.permission_added', ['name' => $request->permission_ru_name]);
        }

        if($request->action == 'remove_permission'){
            $user->revokePermissionTo($request->permission_name);
            $message = __('user.permission_removed', ['name' => $request->permission_ru_name]);
        }

        return $toast = [
            'type'=>'bg-success',
            'user'=>$user->name,
            'message'=>$message
        ];

    }

    public function delete_user($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->back()->withSuccess(__('user.delete_user_successfully'));

    }

    public function my_profile()
    {
        $user = Auth::user();
        $roles = Role::get()->whereNotIn('name', ['super-admin','director']);
        $user->role = $user->roles->pluck('ru_name')[0];
        $all_permissions = Permission::all();
        $role_permissions = $user->getPermissionsViaRoles();

        return view('user.my_profile',[
            'user' => $user,
            'roles' => $roles,
            'all_permissions' => $all_permissions,
            'role_permissions' => $role_permissions
        ]);
    }

    public function upload_avatar(Request $request)
    {
        $user = Auth::user();

        if($request->hasFile('avatar')) {

            $path = Storage::putFile('public/avatars', $request->avatar);
            $user->avatar = $path;

            $user->save();

            return redirect()->back()->withSuccess(__('user.upload_avatar_successfully'));
        }
        else {
            return redirect()->back()->withError(__('general.first_choose_file'));
        }
    }

    public function change_language(Request $request)
    {
        $user = Auth::user();

        $user->language = $request->language;
        $user->save();

        app()->setLocale($request->language);
        session()->put('locale', $request->language);

        return redirect()->back()->withSuccess(__('user.language_changed_successfully'));

    }

    public function getUserStatistic($id){
        $user = User::findOrFail($id);
        $statistic = $this->getUserInfoForStatistic($id);

        return view('user.statistic',[
            'user' => $user,
            'active_projects_count' => $statistic['active_projects_count'],
            'active_projects_profit' => $statistic['active_projects_profit'],
            'finished_projects_profit' => $statistic['finished_projects_profit'],
            'finished_projects_count' => $statistic['finished_projects_count'],
            'all_finished_projects_profit' => $statistic['all_finished_projects_profit'],
            'all_finished_projects_count' => $statistic['all_finished_projects_count'],
            'tasks_count' => $statistic['tasks_count'],
        ]);

    }

    public function allUsersStatistic(){

        $users_with_projects = User::whereHas('projects', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->where('active', '1')->where('status', '<>', 'Черновик');
        })->get();

        foreach ($users_with_projects as $user => $key) {
            $key->stat = $this->getManagerThisMonthStatistic($key['id']);
            if ($key->stat['finished_projects_profit'] < 0 || $key->stat['active_projects_profit'] < 0) {
                $key->user_class = 'danger';
            } else {
                if ($key->stat['finished_projects_profit'] == 0) {
                    $key->user_class = 'primary';
                } else {
                    $key->user_class = 'success';
                }
            }
        }

        return view('user.all_users_statistic', [
            'users_with_projects' => $users_with_projects,
        ]);

    }

}
