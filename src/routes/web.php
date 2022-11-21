<?php

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

$router->get('/', function () use ($router) {
    return "Hi :)";
});


$router->group(['prefix' => 'api/v1', 'namespace' => 'v1'], function () use ($router) {

    //Load the clinet's csv file
    $router->post('load',  ['uses' => 'APIController@load']);

    //Get seller data
    $router->get('sellers/{id}',  ['uses' => 'APIController@seller']);
    $router->get('sellers/{id}/contacts',  ['uses' => 'APIController@contacts']);
    $router->get('sellers/{id}/sales',  ['uses' => 'APIController@sales']);

    //Get sales
    $router->get('sales/{year}',  ['uses' => 'APIController@summary']);

});