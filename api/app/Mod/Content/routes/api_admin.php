<?php

use App\Http\Middleware\ActionLogMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('{company_alias}')->name('content.')->group(function () {

            Route::prefix('{facility_alias}/content')->middleware(PermissionMiddleware::class . ':content')->group(function () {
                Route::prefix('model')->name('model.')->group(function () {
                    Route::get('/resource', [App\Mod\Content\Actions\Admin\Model\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                    Route::get('/find', [App\Mod\Content\Actions\Admin\Model\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                });

                Route::prefix('{model_name}')->name('model_name.')->where(['model_name' => '[a-zA-Z0-9_-]+'])->group(function () {

                    Route::prefix('category')->name('category.')->group(function () {
                        Route::get('/', [App\Mod\Content\Actions\Admin\Category\ListAction::class, '__invoke'])->name('list');
                        Route::post('/store', [App\Mod\Content\Actions\Admin\Category\StoreAction::class, '__invoke'])->name('store');
                        Route::post('/sort', [App\Mod\Content\Actions\Admin\Category\SortAction::class, '__invoke'])->name('sort');
                        Route::get('/resource', [App\Mod\Content\Actions\Admin\Category\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                        Route::get('/find', [App\Mod\Content\Actions\Admin\Category\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                        Route::get('/{id}', [App\Mod\Content\Actions\Admin\Category\DetailAction::class, '__invoke'])->name('detail');
                        Route::put('/{id}', [App\Mod\Content\Actions\Admin\Category\UpdateAction::class, '__invoke'])->name('update');
                        Route::delete('/{id}', [App\Mod\Content\Actions\Admin\Category\DeleteAction::class, '__invoke'])->name('delete');
                    });

                    Route::get('/', [App\Mod\Content\Actions\Admin\ListAction::class, '__invoke'])->name('list');
                    Route::post('/store', [App\Mod\Content\Actions\Admin\StoreAction::class, '__invoke'])->name('store');
                    Route::post('/sort', [App\Mod\Content\Actions\Admin\SortAction::class, '__invoke'])->name('sort');
                    Route::get('/resource', [App\Mod\Content\Actions\Admin\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                    Route::get('/find', [App\Mod\Content\Actions\Admin\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                    Route::post('/preview', [App\Mod\Content\Actions\Admin\PreviewProxyAction::class, '__invoke'])->name('preview');
                    Route::get('/{id}', [App\Mod\Content\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
                    Route::put('/{id}', [App\Mod\Content\Actions\Admin\UpdateAction::class, '__invoke'])->name('update');
                    Route::delete('/{id}', [App\Mod\Content\Actions\Admin\DeleteAction::class, '__invoke'])->name('delete');
                });
            });

            Route::prefix('content')->group(function () {
                Route::post('/ai-proofread', [App\Mod\Content\Actions\Admin\AiProofreadAction::class, '__invoke'])->name('ai-proofread');
            });
        });
    });
});
