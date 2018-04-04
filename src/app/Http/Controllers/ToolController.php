<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Project;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Returns tool version details
     * 
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function version(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment);
    }

    /**
     * Takes a db snapshot
     * 
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function dbSnapshot(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:database');
    }

    /**
     * Returns info on the DB snapshot(s)
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function dbSnapshotInfo(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:database:info');
    }

    /**
     * Pulls the latest DB snapshot(s) to the test environment
     * @param Project $project 
     * @return string
     */
    public function dbSnapshotPull(Project $project)
    {
        return $this->makeToolReqest($project, 'test', 'snapshot:database:pull');
    }

    /**
     * Downloads the latest DB snapshot(s)
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function dbSnapshotDownload(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:database:download');
    }

    /**
     * Restores the DB snapshot(s) to the test environment
     * @param Project $project 
     * @return string
     */
    public function dbSnapshotRestore(Project $project)
    {
        return $this->makeToolReqest($project, 'test', 'snapshot:database:restore');
    }

    /**
     * Takes a media snapshot
     * 
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function mediaSnapshot(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:filesystem');
    }

    /**
     * Returns info on the media snapshot(s)
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function mediaSnapshotInfo(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:filesystem:info');
    }

    /**
     * Pulls the latest media snapshot(s) to the test environment
     * @param Project $project 
     * @return string
     */
    public function mediaSnapshotPull(Project $project)
    {
        return $this->makeToolReqest($project, 'test', 'snapshot:filesystem:pull');
    }

    /**
     * Downloads the latest media snapshot(s)
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function mediaSnapshotDownload(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'snapshot:filesystem:download');
    }

    /**
     * Restores the media snapshot(s) on the test environment
     * @param Project $project 
     * @return string
     */
    public function mediaSnapshotRestore(Project $project)
    {
        return $this->makeToolReqest($project, 'test', 'snapshot:filesystem:restore');
    }

    /**
     * Updates the tool phar file
     * @param Project $project 
     * @param string $environment 
     * @return string
     */
    public function update(Project $project, $environment)
    {
        return $this->makeToolReqest($project, $environment, 'self-update');
    }


    /**
     * Makes a request to the tool associated with this project/environment
     * @param Project $project 
     * @param string $environment 
     * @param string|null $command 
     * @return string
     */
    private function makeToolReqest(Project $project, $environment, $command = null)
    {
        $envUrl = $environment . '_url';
        $envUser = $environment . '_credentials_user';
        $envPass = $environment . '_credentials_pass';

        $params = isset($command) ? [
            'command' => $command
        ] : [

        ];

        if (
            !isset($project->$envUrl) ||
            !isset($project->$envUser) ||
            !isset($project->$envPass)
        ) {
            return 'Could not find url/user/pass details for ' . $environment . ' environment';
        }

        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->get(
                $project->$envUrl . "?" . http_build_query($params),
                array(
                    'stream' => false,
                    'auth' => [
                        $project->$envUser, 
                        $project->$envPass
                    ]
                )
            );
            
            return strip_tags($res->getBody());
        } catch (\Exception $e) {
            switch($e->getCode()) {
                case 401:
                    return 'Authentication Failed';
                    break;
                case 404:
                    return 'Not found';
                    break;
                default:
                    return 'Error: '. $e->getMessage();
                    break;
            }
        }
    }
}
