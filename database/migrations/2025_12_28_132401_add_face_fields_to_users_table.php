<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code')->unique()->after('email');
            $table->json('face_encoding')->nullable()->after('employee_code');
            $table->boolean('is_active')->default(true)->after('face_encoding');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code',
                'face_encoding',
                'is_active'
            ]);
        });
    }
};
