<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('promos', PromoController::class);
    $router->resource('transactions', TransactionController::class);
    $router->resource('master-services', MasterServicesController::class);
    $router->resource('maid-schedules', MaidScheduleController::class);
});
