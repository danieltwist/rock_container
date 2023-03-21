<?php

namespace App\Listeners;

use App\Events\AgreeTelegram;
use App\Models\Invoice;
use App\Models\TelegramUpdate;
use App\Models\User;
use Carbon\Carbon;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramAgreeMessage
{
    public function handle(AgreeTelegram $event)
    {
        $action = explode(':', $event->update->action);
        $invoice_id = $action[2];
        if ($event->update->processed == 'waiting_status'){
            unset($action[0]);
            $action_button = implode(':', $action);

            $inline_keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>'Счет согласован на оплату', 'callback_data'=>'agree_set_status:'.$action_button.':agreed'],
                        ['text'=>'Согласована частичная оплата', 'callback_data'=>'agree_set_status:'.$action_button.':part_agreed'],
                    ],
                    [
                        ['text'=>'Счет на согласовании', 'callback_data'=>'agree_set_status:'.$action_button.':on_approval'],
                        ['text'=>'Отменить', 'callback_data'=>'cancel_agree:'.$action_button]
                    ],
                ],
            ]);

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'parse_mode' => 'html',
                'text' => 'Согласование счета №'. $invoice_id .':'.PHP_EOL.'<b>Выберите статус</b>',
                'reply_markup' => $inline_keyboard,
                'disable_notification' => true
            ]);

            $event->update->update([
               'message_id_remove' => $response->getMessageId()
            ]);

//            $first_message = TelegramUpdate::where('chat_id', $event->update->chat_id)
//                ->where('action', $event->update->action.':start_message')
//                ->orderBy('id', 'DESC')
//                ->first();
//
//            $this->removeMessage($first_message, $response->getMessageId());
        }
        elseif ($event->update->processed == 'set_status'){
            $status = $action[3];
            $action[0] = 'agree';
            unset($action[3]);
            $action_string = implode(':', $action);

            unset($action[0]);
            $action_button = implode(':', $action);

            switch ($status){
                case 'agreed':
                    $invoice_status = 'Счет согласован на оплату';
                    break;
                case 'part_agreed':
                    $invoice_status = 'Согласована частичная оплата';
                    break;
                case 'on_approval':
                    $invoice_status = 'Счет на согласовании';
                    break;
                default: $invoice_status = 'Счет на согласовании';
            }

            $exist_update = TelegramUpdate::where('action', $action_string)
                ->where('processed', 'waiting_status')
                ->where('chat_id', $event->update->chat_id)
                ->orderBy('id', 'DESC')
                ->first();

            if(!is_null($exist_update)){
                if(!is_null($exist_update->info)){
                    $info = $exist_update->info;
                    $info['status'] = $invoice_status;
                }
                else{
                    $info = [
                        'status' => $invoice_status,
                        'sub_status' => '',
                        'comment' => ''
                    ];
                }
                $exist_update->update([
                    'info' => $info,
                    'processed' => 'waiting_sub_status'
                ]);
            }

            $inline_keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>'Без дополнительного статуса', 'callback_data'=>'agree_set_sub_status:'.$action_button.':without'],
                    ],
                    [
                        ['text'=>'Срочно', 'callback_data'=>'agree_set_sub_status:'.$action_button.':urgently'],
                        ['text'=>'Взаимозачет', 'callback_data'=>'agree_set_sub_status:'.$action_button.':compensation'],
                        ['text'=>'Отложен', 'callback_data'=>'agree_set_sub_status:'.$action_button.':postponed'],
                    ],
                    [
                        ['text'=>'Отменить согласование', 'callback_data'=>'cancel_agree:'.$action_button]
                    ],
                ],
            ]);

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'parse_mode' => 'html',
                'text' => 'Согласование счета №'. $invoice_id .':'.PHP_EOL.'<b>Выберите дополнительный статус</b>',
                'reply_markup' => $inline_keyboard,
                'disable_notification' => true
            ]);

            $this->removeMessage($exist_update, $response->getMessageId());

        }
        elseif ($event->update->processed == 'set_sub_status'){
            $sub_status = $action[3];
            $action[0] = 'agree';
            unset($action[3]);
            $action_string = implode(':', $action);

            unset($action[0]);
            $action_button = implode(':', $action);

            switch ($sub_status){
                case 'without':
                    $invoice_sub_status = 'Без дополнительного статуса';
                    break;
                case 'urgently':
                    $invoice_sub_status = 'Срочно';
                    break;
                case 'compensation':
                    $invoice_sub_status = 'Взаимозачет';
                    break;
                case 'postponed':
                    $invoice_sub_status = 'Отложен';
                    break;
                default: $invoice_sub_status = 'Без дополнительного статуса';
            }

            $exist_update = TelegramUpdate::where('action', $action_string)
                ->where('processed', 'waiting_sub_status')
                ->where('chat_id', $event->update->chat_id)
                ->orderBy('id', 'DESC')
                ->first();

            if(!is_null($exist_update)){
                if(!is_null($exist_update->info)){
                    $info = $exist_update->info;
                    $info['sub_status'] = $invoice_sub_status;
                }
                else{
                    $info = [
                        'status' => '',
                        'sub_status' => $invoice_sub_status,
                        'comment' => ''
                    ];
                }
                $exist_update->update([
                    'info' => $info,
                    'processed' => 'waiting_need_comment'
                ]);
            }

            $inline_keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>'Добавить комментарий', 'callback_data'=>'agree_set_comment:'.$action_button.':yes'],
                        ['text'=>'Без комментария', 'callback_data'=>'agree_set_comment:'.$action_button.':no'],
                    ],
                    [
                        ['text'=>'Отменить согласование', 'callback_data'=>'cancel_agree:'.$action_button]
                    ],
                ],
            ]);

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'parse_mode' => 'html',
                'text' => 'Согласование счета №'. $invoice_id .':'.PHP_EOL.'<b>Добавьте комментарий</b>',
                'reply_markup' => $inline_keyboard,
                'disable_notification' => true
            ]);

            $this->removeMessage($exist_update, $response->getMessageId());
        }
        elseif ($event->update->processed == 'set_comment'){
            $need_comment = $action[3];
            $action[0] = 'agree';
            unset($action[3]);
            $action_string = implode(':', $action);

            unset($action[0]);
            $action_button = implode(':', $action);

            $exist_update = TelegramUpdate::where('action', $action_string)
                ->where('processed', 'waiting_need_comment')
                ->where('chat_id', $event->update->chat_id)
                ->orderBy('id', 'DESC')
                ->first();

            if(!is_null($exist_update)){
                if($need_comment == 'yes'){
                    $exist_update->update([
                        'processed' => 'listen'
                    ]);

                    $inline_keyboard = json_encode([
                        'inline_keyboard'=>[
                            [
                                ['text'=>'Отменить согласование', 'callback_data'=>'cancel_agree:'.$action_button]
                            ],
                        ],
                    ]);

                    $response = Telegram::sendMessage([
                        'chat_id' => $event->update->chat_id,
                        'parse_mode' => 'html',
                        'text' => 'Согласование счета №'. $invoice_id .':'.PHP_EOL.'<b>Введите текст комментария</b>',
                        'reply_markup' => $inline_keyboard,
                        'disable_notification' => true
                    ]);

                    $this->removeMessage($exist_update, $response->getMessageId());

                }
                else {
                    if(!is_null($exist_update->info)){
                        $info = $exist_update->info;
                        $info['comment'] = '';
                    }
                    else{
                        $info = [
                            'status' => '',
                            'sub_status' => '',
                            'comment' => ''
                        ];
                    }

                    $exist_update->update([
                        'info' => $info,
                        'processed' => 'done'
                    ]);

                    $this->agreeInvoice($exist_update);

                }
            }
        }
        elseif ($event->update->processed == 'message_received'){

            $message_text = $event->update->answer->object['message']['text'];

            if(!is_null($event->update->info)){
                $info = $event->update->info;
                $info['comment'] = $message_text;
            }
            else{
                $info = [
                    'status' => '',
                    'sub_status' => '',
                    'comment' => $message_text
                ];
            }

            $event->update->update([
                'info' => $info,
                'processed' => 'done'
            ]);

            $this->agreeInvoice($event->update);

        }
        elseif ($event->update->processed == 'cancel_agree'){
            $action[0] = 'agree';
            $action = implode(':', $action);

            $exist_update = TelegramUpdate::where('action', $action)
                ->where('chat_id', $event->update->chat_id)
                ->orderBy('id', 'DESC')
                ->first();

            if(!is_null($exist_update)){
                $exist_update->update([
                    'processed' => 'canceled'
                ]);
            }

            $response = Telegram::sendMessage([
                'chat_id' => $event->update->chat_id,
                'text' => 'Согласование счета №'. $invoice_id .':'.PHP_EOL.'<b>Успешно отменено</b>',
                'disable_notification' => true
            ]);

            $this->removeMessage($exist_update, $response->getMessageId());
        }
    }

    public function agreeInvoice(TelegramUpdate $update){
        $action = explode(':', $update->action);
        $invoice_id = $action[2];

        $user = User::where('telegram_chat_id', $update->chat_id)->first();

        if(in_array($user->id, ['1','21'])){
            $invoice = Invoice::findOrFail($invoice_id);
            $update->info['sub_status'] == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $update->info['sub_status'];

            $status_with_sub_status = $update->info['status'];
            if(!is_null($sub_status)) $status_with_sub_status .= '/'.$sub_status;

            if($user->id == '1'){
                if($update->info['status'] != 'Счет на согласовании'){
                    $invoice->agree_1 = serialize([
                        'status' => $update->info['status'],
                        'date' => Carbon::now()
                    ]);
                }
                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_1 = null;
                }
                $invoice->sub_status = $sub_status;
                if($update->info['comment'] != ''){
                    if($invoice->director_comment != ''){
                        $director_comment = 'Ава - '.date('Y-m-d H:i:s').' '. $update->info['comment'] .PHP_EOL. $invoice->director_comment;
                    }
                    else {
                        $director_comment = 'Ава - '.date('Y-m-d H:i:s').' '. $update->info['comment'];
                    }
                    $invoice->director_comment = $director_comment;
                }
            }

            if($user->id == '21'){
                if($update->info['status'] != 'Счет на согласовании'){
                    $invoice->agree_2 = serialize([
                        'status' => $update->info['status'],
                        'date' => Carbon::now()
                    ]);
                }
                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_2 = null;
                }
                $invoice->sub_status = $sub_status;
                if($update->info['comment'] != ''){
                    if($invoice->director_comment != ''){
                        $director_comment = 'Иннокентий - '.date('Y-m-d H:M').' '. $update->info['comment'] .PHP_EOL. $invoice->director_comment;
                    }
                    else {
                        $director_comment = 'Иннокентий - '.date('Y-m-d H:M').' '. $update->info['comment'];
                    }
                    $invoice->director_comment = $director_comment;
                }
            }

            if ($invoice->agree_1 != '' && $invoice->agree_2 != ''){
                $invoice->status = 'Счет согласован на оплату';
            }

            $invoice->save();

            $inline_keyboard = json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>'Открыть', 'url' => config('app.url').'/task/1702'],
                    ],
                ]
            ]);

            $response = Telegram::sendMessage([
                'chat_id' => $update->chat_id,
                'parse_mode' => 'html',
                'text' => 'Статус счета №'. $action[2] .' был успешно изменен'.PHP_EOL.'<b>'.$status_with_sub_status.'</b>',
                'disable_notification' => true,
                'reply_markup' => $inline_keyboard,
            ]);

            $this->removeMessage($update, $response->getMessageId());

            $first_message = TelegramUpdate::where('action', $update->action.':start_message')
                ->orderBy('id', 'DESC')
                ->first();

            if(!is_null($first_message)){
                $this->removeMessage($first_message, null);
                $first_message->delete();
            }

        }
        else {
            Telegram::sendMessage([
                'chat_id' => $update->chat_id,
                'parse_mode' => 'html',
                'text' => '<b>Вы не находитесь в списке согласовывающих счета</b>',
                'disable_notification' => true
            ]);
        }
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

    public function editMessage(TelegramUpdate $update){
        $action = explode(':', $update->action);

        $inline_keyboard = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Открыть', 'url' => config('app.url').'/'.$action[1].'/'.$action[2]],
                ],
            ]
        ]);

        Telegram::editMessageReplyMarkup([
            'chat_id' => $update->chat_id,
            'message_id' => $update->message_id_remove,
            'reply_markup' => $inline_keyboard
        ]);

    }
}
