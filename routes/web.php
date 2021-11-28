<?php

use Illuminate\Http\Request;

/** @var \Laravel\Lumen\Routing\Router $router */


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->post('/register',      'AuthController@register');
$router->post('/login',         'AuthController@login');
$router->post('/logout',        ['middleware' => 'auth', 'uses' => 'AuthController@logout']);
$router->get('/me',             ['middleware' => ['auth', 'verified'], 'uses' => 'AuthController@me']);
$router->get('/email/verify',   ['uses' => 'AuthController@emailVerify', 'as' => 'verification.verify']);
$router->get('/email/request-verification', ['middleware' => ['auth'], 'as' => 'verification.notice', 'uses' => 'AuthController@emailRequestVerification']);

$router->group(['prefix' => 'workspace', 'middleware' => ['auth', 'verified']], function () use ($router) {
    $router->get('/',               'WorkspaceController@index');
    $router->post('/',              'WorkspaceController@store');
    $router->get('/{slug}',         'WorkspaceController@show');
    $router->put('/{slug}',         'WorkspaceController@update');
    $router->delete('/{slug}',      'WorkspaceController@destroy');
    $router->post('/change/{slug}', 'WorkspaceController@switch');
});

$router->get('/user/{username}/avatar',           'UserController@avatar');
$router->group(['prefix' => 'user', 'middleware' => ['auth', 'verified']], function () use ($router) {
    $router->get('/',           'UserController@index');
    $router->post('/',          'UserController@invite');
    $router->delete('/{id}',    'UserController@destroy');
});

$router->group(['prefix' => 'project', 'middleware' => ['auth', 'verified']], function () use ($router) {
    $router->get('/',                           'ProjectController@index');
    $router->post('/',                          'ProjectController@store');
    $router->get('/{slug}',                     'ProjectController@show');
    $router->put('/{slug}',                     'ProjectController@update');
    $router->post('/{slug}/assign',             'ProjectController@assign');
    $router->post('/{slug}/massassign',         'ProjectController@massAssign');
    $router->delete('/{slug}/unassign',         'ProjectController@unAssign');
    $router->delete('/{slug}',                  'ProjectController@destroy');
    $router->post('/{slug}/milestone',          'MilestoneController@store');
    $router->put('/{slug}/milestone/{id}',      'MilestoneController@update');
    $router->delete('/{slug}/milestone/{id}',   'MilestoneController@destroy');
    $router->get('/{slug}/task',                'TaskController@index');
});
