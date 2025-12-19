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
            $table->longText('name')->nullable()->after('custom_field_id');
            $table->longText('field_id')->nullable()->after('name');
            $table->longText('field_type')->nullable()->after('field_id');
            $table->boolean('is_required')->nullable()->default(0)->after('field_type');
            $table->boolean('is_list_heading')->nullable()->default(0)->after('is_required');
            $table->json('choices')->nullable()->comment('選択肢')->after('is_list_heading');
            $table->text('placeholder')->nullable()->after('choices');
            $table->text('help_text')->nullable()->after('placeholder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_field', function (Blueprint $table) {
            //
            $table->dropColumn('name');
            $table->dropColumn('field_id');
            $table->dropColumn('field_type');
            $table->dropColumn('is_required');
            $table->dropColumn('is_list_heading');
            $table->dropColumn('choices');
            $table->dropColumn('placeholder');
            $table->dropColumn('help_text');
        });
    }
};
