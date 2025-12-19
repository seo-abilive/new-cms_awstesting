<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ActionLogMiddleware;

// SPAクッキー方式でセッションを使うために 'web' ミドルウェアを付与
Route::middleware(['web', ActionLogMiddleware::class])->prefix('api')->name('api.')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // 認証ユーザ取得（企業・施設の最小情報を付与）
        Route::middleware(['auth:sanctum'])->get('/me', function (\Illuminate\Http\Request $request) {
            /** @var \Illuminate\Contracts\Auth\Authenticatable|mixed $authUser */
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json(null);
            }

            // 企業・施設の関連が利用できるユーザモデルの場合のみ読み込み
            if (method_exists($authUser, 'companies') && method_exists($authUser, 'facilities')) {
                $authUser->load([
                    'companies:id,company_name,alias',
                    'facilities:id,facility_name,alias,company_id',
                    'facilities.company:id,alias',
                ]);

                $companies = $authUser->companies->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'company_name' => $c->company_name,
                        'alias' => $c->alias,
                    ];
                })->values();

                $facilities = $authUser->facilities->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'facility_name' => $f->facility_name,
                        'alias' => $f->alias,
                        'company' => [
                            'id' => optional($f->company)->id,
                            'alias' => optional($f->company)->alias,
                        ],
                    ];
                })->values();

                $base = [
                    'id' => $authUser->id,
                    'name' => $authUser->name ?? null,
                    'email' => $authUser->email ?? null,
                    'user_type' => $authUser->user_type ?? null,
                ];

                return response()->json($base + [
                    'companies' => $companies,
                    'facilities' => $facilities,
                ]);
            }

            // フォールバック: そのまま返却
            return response()->json($authUser);
        })->name('me')->withoutMiddleware(ActionLogMiddleware::class);
        // 認証ユーザ更新
        Route::middleware(['auth:sanctum'])->put('/user/me', [App\Core\User\Actions\Admin\UpdateMeAction::class, '__invoke'])->name('user.me.update');

        Route::prefix('user')->name('user.')->group(function () {

            // ログイン（SPAクッキー方式）
            Route::post('/login', [App\Core\User\Actions\Admin\LoginAction::class, '__invoke'])->name('login');

            // 2段階認証コード検証（未認証で利用可）
            Route::post('/login/verify-code', [App\Core\User\Actions\Admin\VerifyTwoFactorCodeAction::class, '__invoke'])->name('login.verify-code');

            // 2段階認証コード再送信（未認証で利用可）
            Route::post('/login/resend-code', [App\Core\User\Actions\Admin\ResendVerificationCodeAction::class, '__invoke'])->name('login.resend-code');

            // パスワードリセット（未認証で利用可）
            Route::post('/password/forgot', [App\Core\User\Actions\Admin\ForgotPasswordAction::class, '__invoke'])->name('password.forgot');
            Route::post('/password/reset', [App\Core\User\Actions\Admin\ResetPasswordAction::class, '__invoke'])->name('password.reset');

            Route::middleware(['auth:sanctum'])->group(function () {

                Route::prefix('permissions')->name('permissions.')->group(function () {
                    Route::get('/function/resource', [App\Core\User\Actions\Admin\Permissions\Function\ResourceAction::class, '__invoke'])->name('function.resource')->withoutMiddleware(ActionLogMiddleware::class);
                    Route::get('/check', [App\Core\User\Actions\Admin\Permissions\CheckAction::class, '__invoke'])->name('check')->withoutMiddleware(ActionLogMiddleware::class);
                });

                Route::post('/logout', [App\Core\User\Actions\Admin\LogoutAction::class, '__invoke'])->name('logout');
                Route::get('/', [App\Core\User\Actions\Admin\ListAction::class, '__invoke'])->name('list');
                Route::post('/store', [App\Core\User\Actions\Admin\StoreAction::class, '__invoke'])->name('store');
                Route::post('/sort', [App\Core\User\Actions\Admin\SortAction::class, '__invoke'])->name('sort');
                Route::get('/resource', [App\Core\User\Actions\Admin\ResourceAction::class, '__invoke'])->name('resource')->withoutMiddleware(ActionLogMiddleware::class);
                Route::post('/logout', [App\Core\User\Actions\Admin\LogoutAction::class, '__invoke'])->name('logout');
                Route::get('/find', [App\Core\User\Actions\Admin\FindAction::class, '__invoke'])->name('find')->withoutMiddleware(ActionLogMiddleware::class);
                Route::get('/{id}', [App\Core\User\Actions\Admin\DetailAction::class, '__invoke'])->name('detail');
                Route::put('/{id}', [App\Core\User\Actions\Admin\UpdateAction::class, '__invoke'])->name('update');
                Route::delete('/{id}', [App\Core\User\Actions\Admin\DeleteAction::class, '__invoke'])->name('delete');
                Route::put('/{id}/two-factor/enabled', [App\Core\User\Actions\Admin\SetTwoFactorEnabledAction::class, '__invoke'])->name('two-factor.enabled');
                Route::post('/{id}/two-factor/reset', [App\Core\User\Actions\Admin\ResetTwoFactorAction::class, '__invoke'])->name('two-factor.reset');
            });
        });

        Route::middleware(['auth:sanctum'])->prefix('{company_alias}/{facility_alias}/user')->name('company_user.')->group(function () {
            Route::get('/', [App\Core\User\Actions\Admin\Company\ListAction::class, '__invoke'])->name('list');
            Route::post('/store', [App\Core\User\Actions\Admin\Company\StoreAction::class, '__invoke'])->name('store');
            Route::get('/{id}', [App\Core\User\Actions\Admin\Company\DetailAction::class, '__invoke'])->name('detail');
            Route::put('/{id}', [App\Core\User\Actions\Admin\Company\UpdateAction::class, '__invoke'])->name('update');
            Route::delete('/{id}', [App\Core\User\Actions\Admin\Company\DeleteAction::class, '__invoke'])->name('delete');
        });
    });
});
