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
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->authoredBy();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->dropColumn('publish_at');
            $table->dropColumn('expires_at');
            $table->dropColumn('sort_num');
            $table->dropColumn('status');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
