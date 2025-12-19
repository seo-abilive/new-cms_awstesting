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
        Schema::create('cms_content', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('model_id')->nullable();
            $table->foreignId('company_id')->nullable();
            $table->unsignedBigInteger('seq_id')->nullable();
            $table->longText('title')->nullable();
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->timestamps();
            $table->authoredBy();
            $table->searchable();
            $table->morphs('assignable', 'content_assignable_index');

            $table->foreign('model_id')
                ->references('id')
                ->on('cms_content_model')
                ->cascadeOnDelete()
            ;

            $table->foreign('company_id')
                ->references('id')
                ->on('contract_company')
                ->cascadeOnDelete()
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_content');
    }
};
