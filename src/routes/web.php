<?php

use Illuminate\Support\Facades\Route;
use Sumandey8976\PluginInstaller\Http\Controllers\PluginUploadController;

Route::get('/plugins', [PluginUploadController::class, 'showForm']);
Route::get('/plugins/list', [PluginUploadController::class, 'listPlugins'])->name('plugins.list');
Route::post('/plugins/upload', [PluginUploadController::class, 'upload'])->name('plugins.upload');
Route::post('/plugins/install-remote', [PluginUploadController::class, 'installFromRemote'])->name('plugins.install.remote');
