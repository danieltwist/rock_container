<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelegramNotify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $telegram_message;

    public function __construct($telegram_message)
    {
        $this->telegram_message = $telegram_message;
    }

}
