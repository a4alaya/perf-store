<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone')->nullable()->after('email');
            $table->string('preferred_locale', 2)->default('en')->after('password');
            $table->boolean('is_admin')->default(false)->after('preferred_locale');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['phone']);
            $table->dropColumn(['phone', 'preferred_locale', 'is_admin']);
        });
    }
};
