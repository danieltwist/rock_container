<?php

namespace App\Console\Commands;

use App\Http\Traits\ContainerTrait;
use App\Http\Traits\FinanceTrait;
use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Console\Command;

class UpdateActiveProjectsFinance extends Command
{
    use ContainerTrait;
    use FinanceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:projects_finance';

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
        foreach (Project::all() as $project){
            $this->updateProjectFinance($project->id);
        }
    }
}
