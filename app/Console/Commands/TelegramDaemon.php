<?php

namespace App\Console\Commands;

use App\Events\AgreeTelegram;
use App\Events\AnswerTelegram;
use App\Models\TelegramUpdate;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get_updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updates = Storage::files('public/templates/telegram_updates');
        if(!is_null($updates)){
            foreach ($updates as $file){
                $update = json_decode(Storage::get($file));
                if (isset($update->callback_query)) {
                    $action = explode(':', $update->callback_query->data);
                    $exist_update = TelegramUpdate::where('action', $update->callback_query->data)
                        ->whereNotIn('processed', ['listen'])
                        ->where('chat_id', $update->callback_query->message->chat->id)
                        ->orderBy('id', 'DESC')
                        ->first();

                    if (is_null($exist_update)) {
                        $new_update = new TelegramUpdate();

                        $new_update->update_id = $update->update_id;
                        $new_update->chat_id = $update->callback_query->message->chat->id;
                        $new_update->object = $update;
                        $new_update->action = $update->callback_query->data;

                        switch ($action[0]) {
                            case 'answer':
                                $processed = 'listen';
                                break;
                            case 'agree':
                                $processed = 'waiting_status';
                                break;
                            case 'cancel_answer':
                                $processed = 'cancel_answer';
                                break;
                            case 'cancel_agree':
                                $processed = 'cancel_agree';
                                break;
                            case 'agree_set_status':
                                $processed = 'set_status';
                                break;
                            case 'agree_set_sub_status':
                                $processed = 'set_sub_status';
                                break;
                            case 'agree_set_comment':
                                $processed = 'set_comment';
                                break;
                            default:
                                $processed = null;
                        }

                        $new_update->processed = $processed;

                        $new_update->save();

                        if (in_array($action[0], ['answer', 'cancel_answer'])) {
                            event(new AnswerTelegram($new_update));
                        }
                        if (in_array($action[0], ['agree', 'cancel_agree', 'agree_set_status', 'agree_set_sub_status', 'agree_set_comment'])) {
                            event(new AgreeTelegram($new_update));
                        }
                    }
                }
                elseif (isset($update->my_chat_member)) {
                    if ($update->my_chat_member->new_chat_member->status == 'kicked') {
                        $user = User::where('telegram_chat_id', $update->my_chat_member->chat->id)->first();
                        if (!is_null($user)) {
                            $user->update([
                                'telegram_chat_id' => null,
                                'notification_channel' => 'Система'
                            ]);
                        }
                    }
                    if ($update->my_chat_member->new_chat_member->status == 'member') {
                        $user = User::where('telegram_login', $update->my_chat_member->chat->username)->first();
                        if (!is_null($user)) {
                            $user->update([
                                'telegram_chat_id' => $update->my_chat_member->chat->id,
                                'notification_channel' => 'Telegram'
                            ]);
                        }
                    }
                }
                else {
                    $listen_events = TelegramUpdate::where('processed', 'listen')->orderBy('id', 'DESC')->get();

                    $new_update = new TelegramUpdate();

                    $new_update->update_id = $update->update_id;
                    $new_update->chat_id = $update->message->chat->id;
                    $new_update->object = $update;
                    $new_update->action = 'message';
                    $new_update->processed = null;

                    $new_update->save();

                    foreach ($listen_events as $event) {
                        if ($event->chat_id == $update->message->chat->id) {
                            $action = explode(':', $event->action)[0];

                            $event->update([
                                'answer_id' => $new_update->id,
                                'processed' => 'message_received'
                            ]);

                            if ($action == 'agree') {
                                event(new AgreeTelegram($event));
                            }
                            if ($action == 'answer') {
                                event(new AnswerTelegram($event));
                            }
                        }
                    }
                }
                Storage::move($file, 'public/templates/telegram_updates/processed/'.basename($file));
            }
        }

    }
}
