<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = config('auth.passwords.users.table', 'password_reset_tokens');
        if (Schema::hasTable($tableName)) {
            return; // 既に存在する場合は何もしない
        }
        Schema::create($tableName, function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('auth.passwords.users.table', 'password_reset_tokens'));
    }
};


