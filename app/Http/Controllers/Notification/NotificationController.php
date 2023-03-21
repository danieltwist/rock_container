<?php

namespace App\Http\Controllers\Notification;

use App\Events\NotificationReceived;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $from_notifications = Notification::where('from', auth()->user()->name)->whereNull('archive')->orderBy('created_at','desc')->paginate(20, ['*'], 'from_notifications');
        $to_notifications = Notification::where('to_id', auth()->user()->id)->whereNull('archive')->orderBy('created_at','desc')->paginate(20, ['*'], 'to_notifications');
        return view('notification.index', [
            'from_notifications' => $from_notifications,
            'to_notifications' => $to_notifications
        ]);
    }

    public function showArchive(){
        $from_notifications = Notification::where('from', auth()->user()->name)->whereNotNull('archive')->orderBy('created_at','desc')->paginate(20, ['*'], 'from_notifications');
        $to_notifications = Notification::where('to_id', auth()->user()->id)->whereNotNull('archive')->orderBy('created_at','desc')->paginate(20, ['*'], 'to_notifications');
        return view('notification.archive', [
            'from_notifications' => $from_notifications,
            'to_notifications' => $to_notifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('notification.create',[
            'users' => User::whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['super-admin']);
            })->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->from == 'Система' ? $from = 'Система' : $from = auth()->user()->name;

        $notification = [
            'from' => $from,
            'to' => $request->to_id,
            'text' => $request->text,
            'class' => 'bg-info',
            'link' => ''
        ];

        event(new NotificationReceived($notification));

        return redirect()->back()->withSuccess(__('notification.send_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function makeRead(Request $request){

        $notification = Notification::find($request->id);

        $notification->update([
            'received' => 1
        ]);

    }

    public function getNotifications(Request $request){

        $current_user_notifications = Notification::where('to_id', $request->user_id)->get();

        return view('layouts.ajax.notifications_dropdown',[
            'current_user_notifications' => $current_user_notifications
        ])->render();

    }

    public function makeAllRead(){

        $notifications = Notification::where('to_id', auth()->user()->id)->get();

        foreach ($notifications as $notification){
            $notification->update([
                'received' => 1
            ]);
        }

    }

    public function addAllReadToArchive(Request $request)
    {
        if($request->type == 'in'){
            $notifications = Notification::where('to_id', auth()->user()->id)
                ->whereNotNull('received')
                ->whereNull('archive')
                ->get();
        }
        else
            $notifications = Notification::where('from', auth()->user()->name)
                ->whereNotNull('received')
                ->whereNull('archive')
                ->get();


        foreach ($notifications as $notification) {
            $notification->update([
                'archive' => 'yes'
            ]);
        }
        return redirect()->back()->withSuccess(__('notification.read_notification_archived_successfully'));
    }

}
