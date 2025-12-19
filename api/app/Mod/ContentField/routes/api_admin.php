<?php

use App\Http\Middleware\ActionLogMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('{company_alias}/content_field')->name('content_field.')->group(function () {

            // custom
            Route::prefix('custom')->name('custom.')->group(function () {
                Route::get('/', [App\Mod\ContentField\Actions\Admin\Custom\ListAction::class, '__invoke'])->name('list');
                Route::get('/resource', [App\Mod\ContentField\Actions\Admin\Custom\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                Route::post('/store', [App\Mod\ContentField\Actions\Admin\Custom\StoreAction::class, '__invoke'])->name('store');
                Route::post('/sort', [App\Mod\ContentField\Actions\Admin\Custom\SortAction::class, '__invoke'])->name('sort');
                Route::get('/{id}', [App\Mod\ContentField\Actions\Admin\Custom\DetailAction::class, '__invoke'])->name('detail');
                Route::put('/{id}', [App\Mod\ContentField\Actions\Admin\Custom\UpdateAction::class, '__invoke'])->name('update');
                Route::delete('/{id}', [App\Mod\ContentField\Actions\Admin\Custom\DeleteAction::class, '__invoke'])->name('delete');
            });

            // block
            Route::prefix('block')->name('block.')->group(function () {
                Route::get('/resource', [App\Mod\ContentField\Actions\Admin\Block\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
            });

            // field
            Route::get('/', [App\Mod\ContentField\Actions\Admin\ListAction::class, '__invoke'])->name('list');
            Route::get('/resource', [App\Mod\ContentField\Actions\Admin\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
            Route::post('/store', [App\Mod\ContentField\Actions\Admin\StoreAction::class, '__invoke'])->name('store');
            Route::post('/sort', [App\Mod\ContentField\Actions\Admin\SortAction::class, '__invoke'])->name('sort');
            Route::get('/{id}', [App\Mod\ContentField\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
            Route::put('/{id}', [App\Mod\ContentField\Actions\Admin\UpdateAction::class, '__invoke'])->name('update');
            Route::delete('/{id}', [App\Mod\ContentField\Actions\Admin\DeleteAction::class, '__invoke'])->name('delete');
        });
    });
});
