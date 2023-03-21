<?php

namespace App\Console\Commands;

use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use Illuminate\Console\Command;

class UpdateContainersUsageInfo extends Command
{
    use ContainerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:usage_info';

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
        $containers = Container::whereNull('archive')->get();

        foreach ($containers as $container){
            $this->updateUsageInfo($container);
        }
    }
}
