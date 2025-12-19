<?php
use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('api.')->group(function () {
    Route::prefix('v1')->name('front.')->group(function () {
        Route::prefix('contact')->name('contact_setting.')->group(function () {
            Route::get('/{token}', [App\Mod\ContactSetting\Actions\Front\DetailAction::class, '__invoke'])->name('detail');
            Route::post('/{token}', [App\Mod\ContactSetting\Actions\Front\StoreAction::class, '__invoke'])->name('store');
        });
    });
});
