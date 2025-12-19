<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ActionLogMiddleware;

Route::middleware(['web', 'auth:sanctum', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('contract')->name('contract.')->group(function () {
            Route::prefix('company')->name('company.')->group(function () {
                Route::get('/', [App\Core\Contract\Actions\Admin\Company\ListAction::class, '__invoke'])->name('list');
                Route::post('/store', [App\Core\Contract\Actions\Admin\Company\StoreAction::class, '__invoke'])->name('store');
                Route::post('/sort', [App\Core\Contract\Actions\Admin\Company\SortAction::class, '__invoke'])->name('sort');
                Route::get('/resource', [App\Core\Contract\Actions\Admin\Company\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/find', [App\Core\Contract\Actions\Admin\Company\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/{id}', [App\Core\Contract\Actions\Admin\Company\DetailAction::class, '__invoke'])->name('detail');
                Route::put('/{id}', [App\Core\Contract\Actions\Admin\Company\UpdateAction::class, '__invoke'])->name('update');
                Route::delete('/{id}', [App\Core\Contract\Actions\Admin\Company\DeleteAction::class, '__invoke'])->name('delete');
            });

            Route::prefix('facility')->name('facility.')->group(function () {
                Route::get('/', [App\Core\Contract\Actions\Admin\Facility\ListAction::class, '__invoke'])->name('list');
                Route::post('/store', [App\Core\Contract\Actions\Admin\Facility\StoreAction::class, '__invoke'])->name('store');
                Route::post('/sort', [App\Core\Contract\Actions\Admin\Facility\SortAction::class, '__invoke'])->name('sort');
                Route::get('/resource', [App\Core\Contract\Actions\Admin\Facility\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/find', [App\Core\Contract\Actions\Admin\Facility\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/{id}', [App\Core\Contract\Actions\Admin\Facility\DetailAction::class, '__invoke'])->name('detail');
                Route::put('/{id}', [App\Core\Contract\Actions\Admin\Facility\UpdateAction::class, '__invoke'])->name('update');
                Route::delete('/{id}', [App\Core\Contract\Actions\Admin\Facility\DeleteAction::class, '__invoke'])->name('delete');
            });
        });
    });
});
