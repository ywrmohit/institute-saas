<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('name');
            $table->string('website')->nullable()->after('phone');
            $table->string('stamp')->nullable()->after('logo');
            $table->string('signature')->nullable()->after('stamp');
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'website', 'stamp', 'signature']);
        });
    }
};
