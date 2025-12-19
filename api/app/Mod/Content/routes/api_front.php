<?php
use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('api.')->group(function () {
    Route::prefix('v1')->name('v1.')->group(function () {
        Route::prefix('{model_name}')->name('model.')->where(['model_name' => '[a-zA-Z0-9_-]+'])->group(function () {
            Route::prefix('markup')->name('markup.')->group(function () {
                Route::get('/', [App\Mod\Content\Actions\Front\V1\Markup\ListAction::class, '__invoke'])->name('list');
                Route::get('/{id}', [App\Mod\Content\Actions\Front\V1\Markup\DetailAction::class, '__invoke'])->name('detail');
            });
            Route::get('', [App\Mod\Content\Actions\Front\V1\ListAction::class, '__invoke'])->name('list');
            Route::get('/categories', [App\Mod\Content\Actions\Front\V1\Categories\ListAction::class, '__invoke'])->name('categories.list');
            Route::get('/{id}', [App\Mod\Content\Actions\Front\V1\DetailAction::class, '__invoke'])->name('detail');
        });
    });
});
