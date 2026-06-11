<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super','editor'])->default('editor');
            $table->enum('status', ['active','disabled'])->default('active');
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_hi');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('icon')->default('');
            $table->string('color')->default('#C41E3A');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio_hi')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title_hi');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt_hi')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_hi')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->foreignId('category_id')->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft','published','scheduled'])->default('draft');
            $table->enum('language', ['hi','en','both'])->default('hi');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('article_tags', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id','tag_id']);
        });

        Schema::create('breaking_news', function (Blueprint $table) {
            $table->id();
            $table->string('text_hi');
            $table->string('text_en')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('epapers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('edition_date');
            $table->string('edition')->default('main');
            $table->string('pdf_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('article_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('epapers');
        Schema::dropIfExists('breaking_news');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
    }
};
