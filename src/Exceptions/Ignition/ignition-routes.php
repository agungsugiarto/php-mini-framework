<?php

use Illuminate\Support\Facades\Route;
use Mini\Framework\Exceptions\Ignition\Http\Controllers\ExecuteSolutionController;
use Mini\Framework\Exceptions\Ignition\Http\Controllers\HealthCheckController;
use Mini\Framework\Exceptions\Ignition\Http\Controllers\UpdateConfigController;
use Mini\Framework\Exceptions\Ignition\Http\Middleware\RunnableSolutionsEnabled;

Route::group([
    'as' => 'ignition.',
    'prefix' => config('ignition.housekeeping_endpoint_prefix'),
    'middleware' => [RunnableSolutionsEnabled::class],
], function () {
    Route::get('health-check', HealthCheckController::class)->name('healthCheck');

    Route::post('execute-solution', ExecuteSolutionController::class)
        ->name('executeSolution');

    Route::post('update-config', UpdateConfigController::class)->name('updateConfig');
});
