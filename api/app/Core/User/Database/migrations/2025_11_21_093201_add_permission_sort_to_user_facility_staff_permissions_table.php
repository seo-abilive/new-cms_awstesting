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
        Schema::table('user_facility_staff_permissions', function (Blueprint $table) {
            $table->boolean('permission_sort')->default(false)->after('permission_delete_scope')->comment('並び替え権限');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_facility_staff_permissions', function (Blueprint $table) {
            $table->dropColumn('permission_sort');
        });
    }
};
