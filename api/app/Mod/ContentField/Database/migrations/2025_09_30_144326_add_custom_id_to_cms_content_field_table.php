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
            $table->foreignId('custom_field_id')->nullable()->after('model_id');
            $table->foreign('custom_field_id')
                ->references('id')
                ->on('cms_content_custom_field')
                ->cascadeOnDelete()
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_field', function (Blueprint $table) {
            //
            $table->dropForeign('cms_content_field_custom_field_id_foreign');
            $table->dropColumn('custom_field_id');
        });
    }
};
