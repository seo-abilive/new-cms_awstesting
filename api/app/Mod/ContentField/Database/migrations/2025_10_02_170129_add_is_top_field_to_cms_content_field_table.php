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
        Schema::table('cms_content_field', function (Blueprint $table) {
            //
            $table->boolean('is_top_field')->nullable()->default(1)->after('model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_field', function (Blueprint $table) {
            //
            $table->dropColumn('is_top_field');
        });
    }
};
