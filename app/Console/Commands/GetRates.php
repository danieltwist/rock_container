<?php

namespace App\Console\Commands;

use App\Models\CurrencyRate;
use App\Models\Setting;
use Illuminate\Console\Command;

class GetRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:rates';

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
        $rates = json_decode(file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js'), true);

        $usd_ratio = Setting::where('name', 'usd_ratio')->first()->toArray()['value'];
        $cny_ratio = Setting::where('name', 'cny_ratio')->first()->toArray()['value'];

        $rates['Valute']['CNY']['Value'] > 70 ? $cny_rate = $rates['Valute']['CNY']['Value']/10 : $cny_rate = $rates['Valute']['CNY']['Value'];

        $today_rates = new CurrencyRate();

        $today_rates->USD = $rates['Valute']['USD']['Value'];
        $today_rates->CNY = $cny_rate;
        $today_rates->usd_ratio = $usd_ratio;
        $today_rates->cny_ratio = $cny_ratio;
        $today_rates->usd_divided = (float)$rates['Valute']['USD']['Value'] - (float)$usd_ratio;
        $today_rates->cny_divided = (float)$cny_rate - (float)$cny_ratio;

        $today_rates->save();

    }
}
