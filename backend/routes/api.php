<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Server Management Routes
    Route::apiResource('servers', 'App\Http\Controllers\ServerController');
    
    // Metrics Routes
    Route::get('/servers/{server}/metrics', 'App\Http\Controllers\MetricController@getLatest');
    Route::get('/servers/{server}/metrics/history', 'App\Http\Controllers\MetricController@getHistory');
    
    // Firewall Routes
    Route::apiResource('servers.firewall-rules', 'App\Http\Controllers\FirewallRuleController');
    
    // Alert Routes
    Route::apiResource('alerts', 'App\Http\Controllers\AlertController');
    Route::put('/alerts/{alert}/resolve', 'App\Http\Controllers\AlertController@resolve');
    Route::apiResource('alert-rules', 'App\Http\Controllers\AlertRuleController');
    
    // User Management Routes
    Route::get('/servers/{server}/users', 'App\Http\Controllers\ServerUserController@index');
    Route::post('/servers/{server}/users/{username}/change-password', 'App\Http\Controllers\ServerUserController@changePassword');
    
    // Server Logs Routes
    Route::get('/servers/{server}/logs', 'App\Http\Controllers\ServerLogController@index');
    Route::get('/servers/{server}/logs/{log}', 'App\Http\Controllers\ServerLogController@show');
    Route::post('/servers/{server}/logs', 'App\Http\Controllers\ServerLogController@store');
    Route::get('/servers/{server}/logs/export/csv', 'App\Http\Controllers\ServerLogController@download');
    Route::delete('/servers/{server}/logs/cleanup', 'App\Http\Controllers\ServerLogController@clearOldLogs');

    // Dashboard Routes
    Route::get('/dashboard/stats', 'App\Http\Controllers\DashboardController@stats');
    Route::get('/dashboard/alerts-summary', 'App\Http\Controllers\DashboardController@alertsSummary');
    
    // User Profile
    Route::get('/profile', 'App\Http\Controllers\ProfileController@show');
    Route::put('/profile', 'App\Http\Controllers\ProfileController@update');
    Route::put('/profile/password', 'App\Http\Controllers\ProfileController@changePassword');
    
    // Logout
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
});

// Public Auth Routes
Route::post('/auth/login', 'App\Http\Controllers\AuthController@login');
Route::post('/auth/register', 'App\Http\Controllers\AuthController@register');

// Agent Routes (server-to-manager communication)
Route::prefix('agent')->group(function () {
    Route::post('/heartbeat', 'App\Http\Controllers\AgentController@heartbeat');
    Route::post('/metrics', 'App\Http\Controllers\AgentController@submitMetrics');
    Route::get('/tasks', 'App\Http\Controllers\AgentController@getTasks');
    Route::post('/task-result', 'App\Http\Controllers\AgentController@submitTaskResult');
});

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok']));
