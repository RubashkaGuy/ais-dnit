<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->string('scan_path')->nullable()->after('next_date');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->string('scan_path')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropColumn('scan_path');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('scan_path');
        });
    }
};
