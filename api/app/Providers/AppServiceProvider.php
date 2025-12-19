<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Coreのロード
        $corePath = app_path('Core');
        foreach (glob("{$corePath}/*", GLOB_ONLYDIR) as $core) {
            // routes
            $routesPath = $core . '/routes/api_admin.php';
            if (file_exists($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }

            // config
            $configPath = $core . '/config';
            if (is_dir($configPath)) {
                foreach (glob("{$configPath}/*.php") as $configFile) {
                    $configName = basename($configFile, '.php');
                    $this->mergeConfigFrom($configFile, $configName);
                }
            }

            // migration
            $migrationPath = $core . '/Database/migrations';
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }

        // Modsのロード
        $modPath = app_path('Mod');
        foreach (glob("{$modPath}/*", GLOB_ONLYDIR) as $mod) {
            // routes
            $routesPath = $mod . '/routes/api_admin.php';
            if (file_exists($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }

            $routesPath = $mod . '/routes/api_front.php';
            if (file_exists($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }

            // config
            $configPath = $mod . '/config';
            if (is_dir($configPath)) {
                foreach (glob("{$configPath}/*.php") as $configFile) {
                    $configName = basename($configFile, '.php');
                    $this->mergeConfigFrom($configFile, $configName);
                }
            }

            // migration
            $migrationPath = $mod . '/Database/migrations';
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }

        // Custom factory resolver for Mods
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            // For models inside a "Mod"
            if (preg_match('/^App\\\\Mod\\\\([^\\\\]+)/', $modelName, $matches)) {
                $module = $matches[1];
                return "App\\Mod\\{$module}\\Database\\Factories\\" . class_basename($modelName) . 'Factory';
            }

            // For models inside a "Core"
            if (preg_match('/^App\\\\Core\\\\([^\\\\]+)/', $modelName, $matches)) {
                $module = $matches[1];
                return "App\\Core\\{$module}\\Database\\Factories\\" . class_basename($modelName) . 'Factory';
            }

            // Default Laravel factory name resolution
            $appNamespace = app()->getNamespace();

            $resolvedModelName = str_starts_with($modelName, $appNamespace . 'Models\\')
                ? substr($modelName, strlen($appNamespace . 'Models\\'))
                : substr($modelName, strlen($appNamespace));

            return 'Database\Factories\\' . $resolvedModelName . 'Factory';
        });

        // カスタムバリデーションルール: 電話番号
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            // 日本の電話番号の形式 (例: 090-1234-5678, 03-1234-5678, 0120-123-456)
            // ハイフンを含む形式を許可
            $pattern = '/^0\d{1,4}-?\d{1,4}-?\d{4}$/';
            return preg_match($pattern, $value);
        }, '正しい電話番号の形式で入力してください。');

        // パスワードリセットURLをフロントの画面へ誘導
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $frontend = env('FRONTEND_URL', 'http://localhost:5173');
            $email = urlencode($notifiable->getEmailForPasswordReset());
            return rtrim($frontend, '/') . "/reset-password/confirm?token={$token}&email={$email}";
        });

        // パスワードリセットメール本文（背景なしシンプルHTML）
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $frontend = env('FRONTEND_URL', 'http://localhost:5173');
            $email = urlencode($notifiable->getEmailForPasswordReset());
            $url = rtrim($frontend, '/') . "/reset-password/confirm?token={$token}&email={$email}";

            return (new MailMessage)
                ->subject('【abi-CMS】パスワード再設定のご案内')
                ->view('emails.reset_password', [
                    'resetUrl' => $url,
                ]);
        });
    }
}
