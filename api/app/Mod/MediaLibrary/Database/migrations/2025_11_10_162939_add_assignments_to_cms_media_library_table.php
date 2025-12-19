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
        Schema::table('cms_media_library', function (Blueprint $table) {
            //
            $table->foreignId('company_id')->nullable()->after('id');
            $table->foreign('company_id')
                ->references('id')
                ->on('contract_company')
                ->cascadeOnDelete()
            ;
            $table->morphs('assignable', 'media_library_assignable_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_media_library', function (Blueprint $table) {
            //
            $table->dropForeign('cms_media_library_company_id_foreign');
            $table->dropColumn('company_id');
            $table->dropMorphs('assignable', 'media_library_assignable_index');
        });
    }
};
