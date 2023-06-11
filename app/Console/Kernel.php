<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\ShortSchedule\ShortSchedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GetRates::class,
        \App\Console\Commands\AfterBorderDocuments::class,
        \App\Console\Commands\FreightLocation::class,
        \App\Console\Commands\GracePediodOver::class,
        \App\Console\Commands\SVVProlong::class,
        \App\Console\Commands\ProlongContact::class,
        \App\Console\Commands\NeedAgreeInvoices::class,
        \App\Console\Commands\NeedDoSomeTasks::class,
        \App\Console\Commands\UpdateActiveProjectsFinance::class,
        \App\Console\Commands\NeedProcessContainerProjects::class,
        \App\Console\Commands\NeedUploadApplicationFromClient::class,
        \App\Console\Commands\NotifyClientAboutPayment::class,
        \App\Console\Commands\HaveOverdueTasks::class,
        \App\Console\Commands\ThereAreUnusedContainers::class,
        \App\Console\Commands\TelegramDaemon::class,
        \App\Console\Commands\UpdateContainersUsageInfo::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('get:rates')->everySixHours();
        $schedule->command('task:documents_after_border')->dailyAt('12:00');
        $schedule->command('task:notify_buyer_about_location')->weeklyOn(5, '9:00');
        $schedule->command('task:grace_period_almost_over')->dailyAt('11:00');
        $schedule->command('notification:there_are_unused_containers')->cron('0 */2 * * *');
        $schedule->command('notification:have_tasks')->dailyAt('10:00');
        $schedule->command('task:prolong_contract')->dailyAt('13:00');
        $schedule->command('task:prolong_svv')->dailyAt('10:00');
        $schedule->command('update:projects_finance')->dailyAt('00:00');
        $schedule->command('task:need_process_container_projects')->dailyAt('9:15');
        $schedule->command('task:notify_client_about_payment')->dailyAt('9:30');
        $schedule->command('task:have_overdue_task')->dailyAt('9:45');
        $schedule->command('update:usage_info')->dailyAt('00:10');
        $schedule->command('bank_accounts:get_balances')->everyFiveMinutes();
        $schedule->command('bank_accounts:get_payments')->everyFiveMinutes();
        $schedule->command('notify:bank_accounts_balances')->dailyAt('18:00');
    }

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        $shortSchedule->command('telegram:get_updates')->everySecond();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
