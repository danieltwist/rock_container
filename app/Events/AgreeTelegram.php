<?php

namespace App\Events;

use App\Models\TelegramUpdate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgreeTelegram
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $update;

    public function __construct(TelegramUpdate $update)
    {
        $this->update = $update;
    }

}
