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
        Schema::create('page_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->index();
            $table->string('url', 2048);
            $table->string('path', 255);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->boolean('has_ga4')->default(false);
            $table->boolean('has_gtm')->default(false);
            $table->boolean('has_meta_pixel')->default(false);
            $table->boolean('has_search_console')->default(false);
            $table->boolean('has_schema_org')->default(false);
            $table->text('schema_org_types')->nullable();
            $table->json('detection_details')->nullable();
            $table->string('fetch_error', 500)->nullable();
            $table->timestamp('analyzed_at')->useCurrent()->index();
            $table->timestamps();

            $table->unique(['site_name', 'path'], 'page_analyses_site_path_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_analyses');
    }
};
