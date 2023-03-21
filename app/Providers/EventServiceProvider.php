<?php

namespace App\Providers;

use App\Events\AgreeTelegram;
use App\Events\AnswerTelegram;
use App\Events\TelegramNotify;
use App\Listeners\TelegramAgreeMessage;
use App\Listeners\TelegramAnswerMessage;
use App\Listeners\TelegramSendNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AnswerTelegram::class => [
            TelegramAnswerMessage::class
        ],
        AgreeTelegram::class => [
            TelegramAgreeMessage::class
        ],
        TelegramNotify::class => [
            TelegramSendNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
