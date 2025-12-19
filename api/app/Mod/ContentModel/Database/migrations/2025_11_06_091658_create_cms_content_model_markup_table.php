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
        Schema::create('cms_content_model_markup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->nullable();
            $table->string('markup_type')->nullable();
            $table->longText('template_json')->nullable();
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->timestamps();
            $table->authoredBy();
            $table->searchable();

            $table->foreign('model_id')
                ->references('id')
                ->on('cms_content_model')
                ->cascadeOnDelete()
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_content_model_markup');
    }
};
