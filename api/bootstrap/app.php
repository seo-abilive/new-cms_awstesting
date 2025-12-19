<?php

use App\Http\Middleware\ActionLogMiddleware;
use App\Http\Middleware\ContentSecurityPolicyMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\App;
use PhpParser\Node\Expr\AssignOp\Mod;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORSを有効化（config/cors.php の設定が反映されます）
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            ContentSecurityPolicyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withCommands([
        App\Console\Commands\MakeModMigration::class,
        App\Console\Commands\ModTest::class,
        App\Console\Commands\MakeMod::class
    ])
    ->create();
