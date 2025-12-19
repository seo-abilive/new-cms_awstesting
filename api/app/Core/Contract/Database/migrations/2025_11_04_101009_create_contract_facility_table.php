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
        Schema::create('contract_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->text('facility_name')->nullable();
            $table->string('alias')->nullable();
            $table->string('zip_code')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('website')->nullable();
            $table->schedule();
            $table->sortable();
            $table->statusable();
            $table->timestamps();
            $table->authoredBy();
            $table->searchable();

            $table->foreign('company_id')
                ->references('id')
                ->on('contract_company')
                ->onDelete('set null')
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_facility');
    }
};
