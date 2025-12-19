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
            $table->text('webhook_url')->nullable()->after('max_content_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->dropColumn('webhook_url');
        });
    }
};
