<?php

namespace App\Listeners;

use App\Events\AnswerTelegram;
use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\Task;
use App\Models\TelegramUpdate;
use App\Models\User;
use App\Models\WorkRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramAnswerMessage
{
    public function handle(AnswerTelegram $event)
    {
        if ($event->update->processed == 'listen'){
            $action = explode(':', $event->update->action);

            $inline_keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>'Отменить', 'callback_data'=>'cancel_answer:'.$event->update->action]
                    ],
                ]
            ]);

            if($action[1] == 'task'){
                $task = Task::findOrFail($action[2]);
                $chat = unserialize($task->comment);
                $message_text = $chat[$action[3]]['text'];
                $text = 'в задаче №'.$task->id;
            }
            elseif($action[1] == 'work_request'){
                $work_request = WorkRequest::findOrFail($action[2]);
                $chat = unserialize($work_request->comment);
                $message_text = $chat[$action[3]]['text'];
                $text = 'в запросе №'.$work_request->id;
            }
            elseif($action[1] == 'project'){
                $project_comment = ProjectComment::findOrFail($action[2]);
                $message_text = $project_comment->comment;
                $text = 'в проекте '.$project_comment->project->name;
            }

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'parse_mode' => 'html',
                'text' => 'Ответ на сообщение '.$text.':'.PHP_EOL.'<b>Введите текст сообщения и отправьте</b>',
                'reply_markup' => $inline_keyboard,
                'disable_notification' => true
            ]);

            $event->update->update([
                'message_id_remove' => $response->getMessageId()
            ]);

        }
        elseif ($event->update->processed == 'message_received'){
            $action = explode(':', $event->update->action);
            $message_text = $event->update->answer->object['message']['text'];

            if($action[1] == 'task'){
                $task = Task::findOrFail($action[2]);
                $chat = unserialize($task->comment);
                $user = User::where('telegram_chat_id', $event->update->chat_id)->first();
                $chat [] = [
                    'user' => $user->id,
                    'text' => $message_text,
                    'answer_to' => $action[3],
                    'file' => '',
                    'date' => Carbon::now()
                ];

                $task->update([
                    'comment' => serialize($chat)
                ]);

                $text = $user->name.' отправил сообщение в задаче №'.$task->id.':'.PHP_EOL.'<b>'.$message_text.'</b>';

                $message = [
                    'from' => 'Система',
                    'to' => $chat[$action[3]]['user'],
                    'text' => $text,
                    'link' => 'task/'.$action[2],
                    'class' => 'bg-info'
                ];

                $this->sendAnswerNotification($message,'answer:task:'.$task->id.':'.array_key_last($chat));

            }
            elseif($action[1] == 'work_request'){
                $work_request = WorkRequest::findOrFail($action[2]);
                $chat = unserialize($work_request->comment);
                $user = User::where('telegram_chat_id', $event->update->chat_id)->first();
                $chat [] = [
                    'user' => $user->id,
                    'text' => $message_text,
                    'answer_to' => $action[3],
                    'file' => '',
                    'date' => Carbon::now()
                ];

                $work_request->update([
                    'comment' => serialize($chat)
                ]);

                $text = $user->name.' отправил сообщение в запросе №'.$work_request->id.':'.PHP_EOL.'<b>'.$message_text.'</b>';

                $message = [
                    'from' => 'Система',
                    'to' => $chat[$action[3]]['user'],
                    'text' => $text,
                    'link' => 'task/'.$action[2],
                    'class' => 'bg-info'
                ];

                $this->sendAnswerNotification($message,'answer:task:'.$work_request->id.':'.array_key_last($chat));
            }
            elseif($action[1] == 'project'){
                $project_comment = ProjectComment::findOrFail($action[2]);
                $user = User::where('telegram_chat_id', $event->update->chat_id)->first();

                $new_comment = ProjectComment::create([
                    'answer_to' => $project_comment->id,
                    'user_id' => $user->id,
                    'project_id' => $project_comment->project->id,
                    'comment' => $message_text
                ]);

                $text = $user->name.' отправил сообщение в проекте №'.$project_comment->project->name.':'.PHP_EOL.'<b>'.$message_text.'</b>';

                $message = [
                    'from' => 'Система',
                    'to' => $project_comment->user_id,
                    'text' => $text,
                    'link' => 'project/'.$project_comment->project->id,
                    'class' => 'bg-info'
                ];

                $this->sendAnswerNotification($message,'answer:project:'.$project_comment->project->id.':'.$new_comment->id);
            }

            $event->update->update([
                'processed' => 'done'
            ]);

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'text' => 'Сообщение было успешно отправлено',
                'disable_notification' => true
            ]);

            $first_message = TelegramUpdate::where('chat_id', $event->update->chat_id)
                ->where('action', $event->update->action.':start_message')
                ->orderBy('id', 'DESC')
                ->first();

            $this->removeMessage($event->update, $response->getMessageId());
            //$this->editMessage($first_message);

        }
        elseif ($event->update->processed == 'cancel_answer'){
            $action = explode(':', $event->update->action);
            unset($action[0]);
            $action = implode(':', $action);

            $update = TelegramUpdate::where('action', $action)
                ->where('chat_id', $event->update->chat_id)
                ->where('processed', '<>', 'done')
                ->orderBy('id', 'DESC')->first();

            if(!is_null($update)){
                $update->update([
                    'processed' => 'done'
                ]);
                $message_text = 'Отправка сообщения отменена';
            }
            else
                $message_text = 'Сообщение уже было отправлено';

            Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'text' => $message_text,
                'disable_notification' => true
            ]);
        }
    }

    public function editMessage(TelegramUpdate $update){
        $action = explode(':', $update->action);

        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Открыть', 'url' => config('app.url').$action[1].'/'.$action[2]],
                ],
            ]
        ]);

        Telegram::editMessageReplyMarkup([
            'chat_id' => $update->chat_id,
            'message_id' => $update->message_id_remove,
            'reply_markup' => $inline_keyboard
        ]);

    }

    public function removeMessage(TelegramUpdate $update, $new_message_id){

        Telegram::deleteMessage([
            'chat_id' => $update->chat_id,
            'message_id' => $update->message_id_remove
        ]);

        $update->update([
            'message_id_remove' => $new_message_id
        ]);
    }

    public function sendAnswerNotification($message, $callback_data){

        $message['inline_keyboard'] = [
            'inline_keyboard' => [
                [
                    ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                    ['text' => 'Ответить', 'callback_data' => $callback_data]
                ],
            ]
        ];

        $message['action'] = $callback_data.':start_message';

        $notification_channel = getNotificationChannel($message['to']);

        if($notification_channel == 'Система'){
            event(new NotificationReceived($message));
        }
        elseif($notification_channel == 'Telegram'){
            event(new TelegramNotify($message));
        }
        else {
            event(new NotificationReceived($message));
            event(new TelegramNotify($message));
        }
    }
}
