<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Project;
use App\Rules\AuthenticateCsmt;
use Illuminate\Http\Request;

class ProjectController extends Controller
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
     * Shows the dashboard
     * 
     * @return Response
     */
    public function dashboard() {
        $projects = Project::all();

        return view('dashboard', ['projects' => $projects]);
    }

    /**
     * Shows form for adding a new project
     *
     * @return Response
     */
    public function add()
    {
        return view('add_project');
    }

    /**
     * Creates a new project
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
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

        $link = tap(new Project($data))->save();

        return redirect('/dashboard');
    }

    /**
     * Deletes a project
     * 
     * @param Project $project 
     * @return Response
     */
    public function delete(Project $project)
    {
        $project->delete();

        return redirect('/dashboard');
    }
}
