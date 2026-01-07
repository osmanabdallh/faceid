<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // في migration إضافة الحقول لجدول users
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('employee_code')->unique()->nullable()->after('email');
        $table->text('face_encoding')->nullable()->after('employee_code'); // غيرت من json إلى text
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
