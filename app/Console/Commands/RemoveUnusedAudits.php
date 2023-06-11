<?php

namespace App\Console\Commands;

use App\Http\Traits\AuditTrait;
use Illuminate\Console\Command;
use OwenIt\Auditing\Models\Audit;

class RemoveUnusedAudits extends Command
{
    use AuditTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:remove_unused';

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
        $remove = [];

        foreach (Audit::all() as $audit){
            if($this->checkAuditForColumn($audit))
                $remove [] = $audit->id;
        }

        $audits_for_remove = Audit::whereIn('id', $remove)->get();

        foreach ($audits_for_remove as $audit){
            $audit->delete();
        }

    }
}
