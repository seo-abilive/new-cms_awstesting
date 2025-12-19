<?php

use App\Http\Middleware\ActionLogMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('/{company_alias}/content_model')->name('content_model.')->group(function () {

            Route::prefix('markup')->name('markup.')->group(function () {
                Route::get('/', [App\Mod\ContentModel\Actions\Admin\Markup\ListAction::class, '__invoke'])->name('list');
                Route::get('/resource', [App\Mod\ContentModel\Actions\Admin\Markup\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/find', [App\Mod\ContentModel\Actions\Admin\Markup\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                Route::post('/store', [App\Mod\ContentModel\Actions\Admin\Markup\StoreAction::class, '__invoke'])->name('store');
                Route::post('/sort', [App\Mod\ContentModel\Actions\Admin\Markup\SortAction::class, '__invoke'])->name('sort');
                Route::get('/{id}', [App\Mod\ContentModel\Actions\Admin\Markup\DetailAction::class, '__invoke'])->name('detail');
                Route::put('/{id}', [App\Mod\ContentModel\Actions\Admin\Markup\UpdateAction::class, '__invoke'])->name('update');
                Route::delete('/{id}', [App\Mod\ContentModel\Actions\Admin\Markup\DeleteAction::class, '__invoke'])->name('delete');
            });

            Route::get('/', [App\Mod\ContentModel\Actions\Admin\ListAction::class, '__invoke'])->name('list');
            Route::get('/resource', [App\Mod\ContentModel\Actions\Admin\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
            Route::get('/find', [App\Mod\ContentModel\Actions\Admin\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
            Route::post('/store', [App\Mod\ContentModel\Actions\Admin\StoreAction::class, '__invoke'])->name('store');
            Route::post('/sort', [App\Mod\ContentModel\Actions\Admin\SortAction::class, '__invoke'])->name('sort');
            Route::get('/{id}', [App\Mod\ContentModel\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
            Route::put('/{id}', [App\Mod\ContentModel\Actions\Admin\UpdateAction::class, '__invoke'])->name('update');
            Route::delete('/{id}', [App\Mod\ContentModel\Actions\Admin\DeleteAction::class, '__invoke'])->name('delete');
        });
    });
});
