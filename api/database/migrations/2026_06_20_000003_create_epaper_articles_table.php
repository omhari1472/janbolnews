<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('epaper_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epaper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->unique(['epaper_id', 'article_id']);
        });

        if (!Schema::hasColumn('epapers', 'status')) {
            Schema::table('epapers', function (Blueprint $table) {
                $table->string('status')->default('draft')->after('edition');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('epaper_articles');
        if (Schema::hasColumn('epapers', 'status')) {
            Schema::table('epapers', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
