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

Route::get('/', 'ProjectController@index');

Auth::routes([
    'verify' => true,
]);

Route::get('/projects/{project}', 'ProjectController@show')
    ->name('project.show');

Route::get('/users/{id}', 'UserController@profile')
    ->name('users.profile');

Route::namespace('Project')->prefix('/projects/{project}')->group(function () {
    Route::get('/settings', 'SettingsController@index')
        ->name('projects.settings');

    Route::patch('/settings', 'SettingsController@update')
        ->name('projects.settings.save');

    Route::get('/timeline', 'TimelineController@index')
        ->name('project.timeline');

    Route::get('/roadmap/{filter?}', 'MilestoneController@index')
        ->name('project.roadmap');


    Route::get('/changelog', 'MilestoneController@changelog')
        ->name('project.changelog');

    Route::resource('milestones', 'MilestoneController');
    Route::resource('tickets', 'TicketController');

    // Wiki routes
    Route::get('/wiki/{wiki}/revisions', 'WikiController@revisions')
        ->name('wiki.revisions');
    Route::get('/wiki/-/pages', 'WikiController@pages')
        ->name('wiki.pages');
    Route::resource('wiki', 'WikiController');
});

Route::namespace('Admin')->prefix('/admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', 'SettingsController@index')->name('settings');
    Route::patch('/settings', 'SettingsController@update')
        ->name('settings.save');

    Route::resource('projects', 'ProjectsController');
    Route::resource('users', 'UserController');
    Route::resource('user-groups', 'UserGroupController');

    Route::resource('types', 'TicketTypesController');
    Route::resource('statuses', 'TicketStatusesController');
});

Route::namespace('Installer')->prefix('/install')->middleware('install')->group(function () {
    Route::get('/', 'InstallController@index')
        ->name('installer');

    Route::post('/permissions', 'PermissionController@index')
        ->name('installer_permissions');

    Route::match(['get', 'post'], '/database', 'DatabaseController@index')
        ->name('installer_database');

    Route::post('/database/install', 'DatabaseController@install')
        ->name('installer_database_install');

    Route::get('/user', 'AccountController@index')
        ->name('installer_user');

    Route::post('/user', 'AccountController@create')
        ->name('installer_user_create');

    Route::get('/complete', 'InstallController@complete')
        ->name('installer_complete');
});
