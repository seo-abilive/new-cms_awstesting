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
        Schema::table('cms_contact_setting', function (Blueprint $table) {
            //
            $table->boolean('is_recaptcha')->nullable()->after('fields')->default(0);
            $table->text('recaptcha_site_key')->nullable()->after('is_recaptcha');
            $table->text('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
            $table->text('thanks_page')->nullable()->after('recaptcha_secret_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_contact_setting', function (Blueprint $table) {
            //
            $table->dropColumn('is_recaptcha');
            $table->dropColumn('recaptcha_site_key');
            $table->dropColumn('recaptcha_secret_key');
            $table->dropColumn('thanks_page');
        });
    }
};
