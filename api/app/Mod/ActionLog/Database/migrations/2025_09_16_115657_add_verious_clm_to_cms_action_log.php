<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cms_action_log', function (Blueprint $table) {
            //
            $table->text('ip')->nullable()->comment('IP');
            $table->text('user_agent')->nullable()->comment('ユーザーエージェント');
            $table->text('path')->nullable()->comment('パス');
            $table->text('method')->nullable()->comment('メソッド');
            $table->json('params')->nullable()->comment('パラメータ');
            $table->text('http_status')->nullable()->comment('ステータス');
            $table->text('error')->nullable()->comment('エラー');
            $table->text('message')->nullable()->comment('メッセージ');
            $table->double('duration')->nullable()->comment('処理時間');
            $table->searchable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_action_log', function (Blueprint $table) {
            //
            $table->dropColumn('ip');
            $table->dropColumn('user_agent');
            $table->dropColumn('path');
            $table->dropColumn('method');
            $table->dropColumn('params');
            $table->dropColumn('http_status');
            $table->dropColumn('error');
            $table->dropColumn('message');
            $table->dropColumn('duration');
            $table->dropColumn('free_search');
        });
    }
};
