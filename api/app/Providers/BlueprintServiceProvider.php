<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 作成者・更新者
        Blueprint::macro('authoredBy', function() {
            $this->foreignId('created_by')->nullable()->constrained('users');
            $this->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 公開開始日時・公開終了日時
        Blueprint::macro('schedule', function () {
            $this->dateTime('publish_at')->nullable();
            $this->dateTime('expires_at')->nullable();
        });

        // 並び順
        Blueprint::macro('sortable', function (){
            $this->unsignedInteger('sort_num')->nullable();
        });

        // 表示
        Blueprint::macro('statusable', function () {
            $this->string('status')->nullable();
        });

        // フリー検索用
        Blueprint::macro('searchable', function () {
            $this->longText('free_search')->nullable();
        });
    }
}
