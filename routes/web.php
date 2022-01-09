<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

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

/*
 * @hideFromAPIDocumentation
 */
$router->get('/', function () use ($router) {
    abort(404);
});

$router->post('/auth', 'AuthController@store');
$router->group(['middleware' => 'auth:api', 'prefix' => 'auth'], function ($router) {
    $router->get('/', 'AuthController@show');
    $router->put('/', 'AuthController@update');
    $router->delete('/', 'AuthController@destroy');
});

$router->group(['middleware' => 'auth:api', 'prefix' => 'users'], function ($router) {
    $router->get('/', 'UserController@index');
    $router->post('/', 'UserController@store');
    $router->get('/{id:[0-9]+}', 'UserController@show');
    $router->put('/{id:[0-9]+}', 'UserController@update');
    $router->patch('/{id:[0-9]+}', 'UserController@update');
    $router->delete('/{id:[0-9]+}', 'UserController@destroy');
});

$router->group(['middleware' => 'auth:api', 'prefix' => 'domoscio'], function ($router) {
    $router->get('/', 'EventController@index');
    $router->post('/event', 'EventController@store');
});

$router->group(['middleware' => 'auth:api', 'prefix' => 'dreamcask'], function ($router) {
    $router->get('/', 'StatementController@index');
    $router->post('/emit', 'StatementController@create');
    $router->post('/state', 'StatementController@update');
    $router->get('/state', 'StatementController@show');
});

$router->group(['middleware' => 'auth:api', 'prefix' => 'msdynamics'], function ($router) {
    $router->get('/', 'MicrosoftDynamicsController@index');
    $router->post('/enroll', 'MicrosoftDynamicsController@update');
    $router->delete('/unenroll', 'MicrosoftDynamicsController@destroy');
    $router->get('/list', 'MicrosoftDynamicsController@show');
});
