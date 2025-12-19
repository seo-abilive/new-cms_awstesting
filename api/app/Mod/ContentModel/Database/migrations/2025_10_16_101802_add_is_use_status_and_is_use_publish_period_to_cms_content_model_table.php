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
            $table->boolean('is_use_status')->nullable()->default(0)->after('is_use_category');
            $table->boolean('is_use_publish_period')->nullable()->default(0)->after('is_use_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->dropColumn('is_use_status');
            $table->dropColumn('is_use_publish_period');
        });
    }
};
