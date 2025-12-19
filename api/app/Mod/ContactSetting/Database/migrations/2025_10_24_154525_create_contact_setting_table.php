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
        Schema::create('cms_contact_setting', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('title')->nullable();
            $table->longText('token')->nullable();
            $table->longText('from_address')->nullable();
            $table->longText('from_name')->nullable();
            $table->longText('to_address')->nullable();
            $table->longText('subject')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('is_return')->nullable()->default(0);
            $table->string('return_field')->nullable();
            $table->string('return_subject')->nullable();
            $table->string('return_body')->nullable();
            $table->json('fields')->nullable();
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->timestamps();
            $table->authoredBy();
            $table->searchable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_contact_setting');
    }
};
