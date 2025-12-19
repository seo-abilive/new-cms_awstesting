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
            $table->dropForeign('cms_content_field_attribute_id_foreign');
            $table->dropColumn('attribute_id');
        });

        Schema::dropIfExists('cms_content_field_attribute');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('cms_content_field_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id');
            $table->longText('name')->nullable();
            $table->longText('field_id')->nullable();
            $table->longText('field_type')->nullable();
            $table->boolean('is_required')->nullable()->default(0);
            $table->boolean('is_list_heading')->nullable()->default(0);
            $table->json('choices')->nullable()->comment('選択肢');
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->timestamps();
            $table->authoredBy();

            $table->foreign('custom_field_id')
                ->references('id')
                ->on('cms_content_custom_field')
                ->cascadeOnDelete()
            ;
        });

        Schema::table('cms_content_field', function (Blueprint $table) {
            //
            $table->foreignId('attribute_id')->nullable()->after('custom_field_id');
            $table->foreign('attribute_id')
                ->references('id')
                ->on('cms_content_field_attribute')
                ->cascadeOnDelete()
            ;
        });
    }
};
