<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_name',
        'live_url',
        'test_url',
        'live_credentials_user',
        'live_credentials_pass',
        'test_credentials_user',
        'test_credentials_pass'
    ];
}
