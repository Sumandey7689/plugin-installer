<?php

use PluginInstaller\Http\Controllers\PluginUploadController;

Route::get('/plugins', [PluginUploadController::class, 'showForm']);
Route::get('/plugins/list', [PluginUploadController::class, 'listPlugins'])->name('plugins.list');
Route::post('/plugins/upload', [PluginUploadController::class, 'upload'])->name('plugins.upload');
