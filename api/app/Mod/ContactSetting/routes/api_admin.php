<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ActionLogMiddleware;
use App\Http\Middleware\PermissionMiddleware;

Route::middleware(['web', 'auth:sanctum', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('{company_alias}/{facility_alias}/contact_setting')->name('contact_setting.')->middleware([PermissionMiddleware::class . ':contact_setting'])->group(function () {
            Route::get('/', [App\Mod\ContactSetting\Actions\Admin\ListAction::class, '__invoke'])->name('list');
            Route::post('/store', [App\Mod\ContactSetting\Actions\Admin\StoreAction::class, '__invoke'])->name('store');
            Route::post('/sort', [App\Mod\ContactSetting\Actions\Admin\SortAction::class, '__invoke'])->name('sort');
            Route::get('/resource', [App\Mod\ContactSetting\Actions\Admin\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware([ActionLogMiddleware::class, PermissionMiddleware::class]);
            Route::get('/find', [App\Mod\ContactSetting\Actions\Admin\FindAction::class, '__invoke'])->name('find')->withoutMiddleware([ActionLogMiddleware::class, PermissionMiddleware::class]);
            Route::get('/{id}', [App\Mod\ContactSetting\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
            Route::put('/{id}', [App\Mod\ContactSetting\Actions\Admin\UpdateAction::class, '__invoke'])->name('update');
            Route::delete('/{id}', [App\Mod\ContactSetting\Actions\Admin\DeleteAction::class, '__invoke'])->name('delete');
        });
    });
});
