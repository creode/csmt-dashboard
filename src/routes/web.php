<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;
use App\Rules\AuthenticateCsmt;


Auth::routes();


Route::get('/', function () {
    return view('home');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dashboard', function () {
    $projects = \App\Project::all();

    return view('dashboard', ['projects' => $projects]);
});





Route::get('/add-project', function () {
    return view('add_project');
});

Route::post('/add-project', function (Request $request) {
    $credentials = [];

    $data = $request->validate([
        'project_name' => 'required|max:255',
        'live_url' => [
            'required',
            'url',
            'max:255',
            new AuthenticateCsmt(
                $credentials,
                'live_credentials_user',
                'live_credentials_pass'
            )
        ],
        'test_url' => [
            'sometimes',
            'nullable',
            'url',
            'max:255',
            new AuthenticateCsmt(
                $credentials,
                'test_credentials_user',
                'test_credentials_pass'
            )
        ],
    ]);

    $data = array_merge($data, $credentials);

    $link = tap(new App\Project($data))->save();

    return redirect('/dashboard');
});


Route::get('/project/version/{project}/{environment}', function (App\Project $project, $environment) {

    $envUrl = $environment . '_url';
    $envUser = $environment . '_credentials_user';
    $envPass = $environment . '_credentials_pass';

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
            $project->$envUrl,
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
})->where('project', '[0-9]+');


Route::get('/project/database/snapshot/{project}/{environment}', function (App\Project $project, $environment) {

    $envUrl = $environment . '_url';
    $envUser = $environment . '_credentials_user';
    $envPass = $environment . '_credentials_pass';

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
            $project->$envUrl . '?command=snapshot:database',
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
})->where('project', '[0-9]+');



Route::get('/project/database/info/{project}/{environment}', function (App\Project $project, $environment) {

    $envUrl = $environment . '_url';
    $envUser = $environment . '_credentials_user';
    $envPass = $environment . '_credentials_pass';

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
            $project->$envUrl . '?command=snapshot:database:info',
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
})->where('project', '[0-9]+');
