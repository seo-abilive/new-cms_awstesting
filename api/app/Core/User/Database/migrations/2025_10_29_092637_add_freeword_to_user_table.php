<?php

use App\Core\User\Domain\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->text('free_search')->nullable()->after('remember_token');
            $table->boolean('is_master')->nullable()->default(0)->after('password');
        });

        User::create([
            'name' => 'マスター管理者',
            'email' => 'sys-second-g@ab-net.co.jp',
            'password' => Hash::make('abilive1999'),
            'is_master' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('free_search');
            $table->dropColumn('is_master');
        });

        User::where('email', 'sys-second-g@ab-net.co.jp')->delete();
    }
};
