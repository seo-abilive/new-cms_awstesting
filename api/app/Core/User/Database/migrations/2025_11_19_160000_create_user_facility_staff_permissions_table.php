<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_facility_staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type', 255)->comment('リソースタイプ: content_model, contact_setting');
            $table->unsignedBigInteger('resource_id')->nullable()->comment('リソースID（ContentModelのid。ContactSettingの場合はNULL）');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained('contract_facility')->cascadeOnDelete()->comment('施設ID（施設スタッフの場合のみ。企業ユーザーの場合はNULL）');
            $table->foreignId('company_id')->nullable()->constrained('contract_company')->cascadeOnDelete()->comment('企業ID（企業ユーザーの場合のみ。施設スタッフの場合はNULL）');
            $table->boolean('permission_read')->default(false);
            $table->enum('permission_read_scope', ['own', 'all'])->default('own')->comment('read権限のスコープ: own=自分のみ, all=全て');
            $table->boolean('permission_write')->default(false);
            $table->enum('permission_write_scope', ['own', 'all'])->default('own')->comment('write権限のスコープ: own=自分のみ, all=全て');
            $table->boolean('permission_delete')->default(false);
            $table->enum('permission_delete_scope', ['own', 'all'])->default('own')->comment('delete権限のスコープ: own=自分のみ, all=全て');
            $table->timestamps();

            // ユニーク制約: 同じリソース、ユーザー、施設/企業の組み合わせは1つだけ
            $table->unique(['resource_type', 'resource_id', 'user_id', 'facility_id', 'company_id'], 'unique_permission');

            // インデックス
            $table->index(['resource_type', 'resource_id'], 'idx_resource');
            $table->index(['user_id', 'facility_id'], 'idx_user_facility');
            $table->index(['user_id', 'company_id'], 'idx_user_company');
        });

        // CHECK制約: facility_idとcompany_idのどちらか一方のみが設定される
        DB::statement('ALTER TABLE user_facility_staff_permissions ADD CONSTRAINT check_facility_or_company CHECK ((facility_id IS NOT NULL AND company_id IS NULL) OR (facility_id IS NULL AND company_id IS NOT NULL))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_facility_staff_permissions');
    }
};
