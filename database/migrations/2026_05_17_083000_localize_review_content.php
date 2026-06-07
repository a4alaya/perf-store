<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->json('title_localized')->nullable()->after('rating');
            $table->json('body_localized')->nullable()->after('title_localized');
            $table->json('admin_response_localized')->nullable()->after('status');
        });

        DB::table('reviews')
            ->select(['id', 'title', 'body', 'admin_response'])
            ->orderBy('id')
            ->chunkById(100, function ($reviews): void {
                foreach ($reviews as $review) {
                    DB::table('reviews')
                        ->where('id', $review->id)
                        ->update([
                            'title_localized' => $review->title ? json_encode(['en' => $review->title, 'ar' => $review->title], JSON_UNESCAPED_UNICODE) : null,
                            'body_localized' => $review->body ? json_encode(['en' => $review->body, 'ar' => $review->body], JSON_UNESCAPED_UNICODE) : null,
                            'admin_response_localized' => $review->admin_response ? json_encode(['en' => $review->admin_response, 'ar' => $review->admin_response], JSON_UNESCAPED_UNICODE) : null,
                        ]);
                }
            });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropColumn(['title', 'body', 'admin_response']);
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->renameColumn('title_localized', 'title');
            $table->renameColumn('body_localized', 'body');
            $table->renameColumn('admin_response_localized', 'admin_response');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->string('legacy_title')->nullable()->after('rating');
            $table->text('legacy_body')->nullable()->after('legacy_title');
            $table->text('legacy_admin_response')->nullable()->after('status');
        });

        DB::table('reviews')
            ->select(['id', 'title', 'body', 'admin_response'])
            ->orderBy('id')
            ->chunkById(100, function ($reviews): void {
                foreach ($reviews as $review) {
                    $title = json_decode($review->title ?? 'null', true);
                    $body = json_decode($review->body ?? 'null', true);
                    $adminResponse = json_decode($review->admin_response ?? 'null', true);

                    DB::table('reviews')
                        ->where('id', $review->id)
                        ->update([
                            'legacy_title' => is_array($title) ? ($title['en'] ?? reset($title) ?: null) : $review->title,
                            'legacy_body' => is_array($body) ? ($body['en'] ?? reset($body) ?: null) : $review->body,
                            'legacy_admin_response' => is_array($adminResponse) ? ($adminResponse['en'] ?? reset($adminResponse) ?: null) : $review->admin_response,
                        ]);
                }
            });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropColumn(['title', 'body', 'admin_response']);
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->renameColumn('legacy_title', 'title');
            $table->renameColumn('legacy_body', 'body');
            $table->renameColumn('legacy_admin_response', 'admin_response');
        });
    }
};
