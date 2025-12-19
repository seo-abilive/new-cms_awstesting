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
            //
            $table->boolean('is_use_preview')->nullable()->default(0)->after('webhook_url');
            $table->longText('preview_url')->nullable()->after('is_use_preview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->dropColumn('is_use_preview');
            $table->dropColumn('preview_url');
        });
    }
};
