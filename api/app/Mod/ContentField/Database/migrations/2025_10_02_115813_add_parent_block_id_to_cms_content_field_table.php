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
            $table->foreignId('parent_block_id')->nullable()->after('model_id');
            $table->foreign('parent_block_id')
                ->references('id')
                ->on('cms_content_field')
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
            $table->dropForeign('cms_content_field_parent_block_id_foreign');
            $table->dropColumn('parent_block_id');
        });
    }
};
