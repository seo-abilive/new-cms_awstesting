<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum'])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('action_log')->name('action_log.')->group(function () {
            Route::get('/', [App\Mod\ActionLog\Actions\Admin\ListAction::class, '__invoke'])->name('list');
            Route::get('/{id}', [App\Mod\ActionLog\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
        });
    });
});
