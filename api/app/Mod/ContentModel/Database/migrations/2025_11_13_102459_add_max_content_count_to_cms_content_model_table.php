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
        Schema::table('cms_content_model', function (Blueprint $table) {
            $table->unsignedInteger('max_content_count')->nullable()->after('is_use_publish_period')->comment('記事登録数の上限（nullの場合は無制限）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            $table->dropColumn('max_content_count');
        });
    }
};
