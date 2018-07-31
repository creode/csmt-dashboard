<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Project;
use App\Http\Controllers\ToolController;

class PullSnapshotsToTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:pull:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls live snapshots to all test environments';

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
            echo 'Pulling DB snapshot for ' . $project['project_name'] . PHP_EOL;
            $toolController->dbSnapshotPull($project);
            echo 'Pulling media snapshot for ' . $project['project_name'] . PHP_EOL;
            $toolController->mediaSnapshotPull($project);
        }
    }
}
