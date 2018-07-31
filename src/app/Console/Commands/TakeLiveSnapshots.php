<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Project;
use App\Http\Controllers\ToolController;

class TakeLiveSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:take:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers live snapshots for all sites';

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
     * @return mixed
     */
    public function handle()
    {
        $projects = Project::all();
        $toolController = new ToolController();

        foreach($projects as $project) {
            echo 'Taking DB snapshot for ' . $project['project_name'] . PHP_EOL;
            $toolController->dbSnapshot($project, 'live');
            echo 'Taking media snapshot for ' . $project['project_name'] . PHP_EOL;
            $toolController->mediaSnapshot($project, 'live');
        }
    }
}
