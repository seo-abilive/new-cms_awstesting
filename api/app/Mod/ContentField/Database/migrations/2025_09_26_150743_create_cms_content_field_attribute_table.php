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
        Schema::create('cms_content_field_attribute', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->longText('field_id')->nullable();
            $table->longText('field_type')->nullable();
            $table->boolean('is_required')->nullable()->default(0);
            $table->boolean('is_list_heading')->nullable()->default(0);
            $table->json('choices')->nullable()->comment('選択肢');
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->timestamps();
            $table->searchable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_content_field_attribute');
    }
};
