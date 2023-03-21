<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $from;
    public $to;
    public $text;
    public $link;
    public $class;
    public $id;

    public function __construct($notification)
    {
        $this->from = $notification['from'];
        $this->to = $notification['to'];
        $this->text = $notification['text'];
        $this->link = $notification['link'];
        $this->class = $notification['class'];

        $new_notification = new Notification();

        $new_notification->from = 'Система';
        $new_notification->to_id = $this->to;
        $new_notification->text = $this->text;
        $new_notification->link = $this->link;

        $new_notification->save();

        $this->id = $new_notification->id;
    }

    public function broadcastOn()
    {
        return new Channel('notifications-channel');
    }

    public function broadcastAs()
    {
        return 'notify-user-'.$this->to;
    }
}
