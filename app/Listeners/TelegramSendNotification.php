<?php

namespace App\Listeners;

use App\Events\TelegramNotify;
use App\Models\Notification;
use App\Models\TelegramUpdate;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramSendNotification
{
    public function handle(TelegramNotify $event)
    {
        $inline_keyboard = json_encode(
            $event->telegram_message['inline_keyboard']
        );

        $chat_id = getUserTelegramChatId($event->telegram_message['to']);

        if(!is_null($chat_id)){
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'parse_mode' => 'html',
                'text' => $event->telegram_message['text'],
                'reply_markup' => $inline_keyboard
            ]);

            TelegramUpdate::create([
                'chat_id' => $chat_id,
                'action' => $event->telegram_message['action'],
                'message_id_remove' => $response->getMessageId()
            ]);

            if (array_key_exists('type', $event->telegram_message)) {
                if($event->telegram_message['type'] != 'task'){
                    if(getNotificationChannel($event->telegram_message['to']) != 'Везде'){
                        Notification::create([
                            'from' => 'Система',
                            'to_id' => $event->telegram_message['to'],
                            'text' => $event->telegram_message['text'],
                            'link' => $event->telegram_message['link'],
                            'received' => 1,
                            'archive' => 'yes'
                        ]);
                    }
                }

            }

        }

    }
}
