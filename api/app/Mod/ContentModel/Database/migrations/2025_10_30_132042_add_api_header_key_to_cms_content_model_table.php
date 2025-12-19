<?php

use App\Mod\ContentModel\Domain\Models\ContentModel;
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
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->text('api_header_key')->nullable()->after('alias');
        });

        ContentModel::all()->each(function (ContentModel $model) {
            $model->api_header_key = bin2hex(random_bytes(32));
            $model->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_content_model', function (Blueprint $table) {
            //
            $table->dropColumn('api_header_key');
        });
    }
};
