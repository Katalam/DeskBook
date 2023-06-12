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
        Schema::table('tables', static function (Blueprint $table) {
            $table->foreignId('time_off_type_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', static function (Blueprint $table) {
            $table->dropForeign(['time_off_type_id']);
            $table->dropColumn('time_off_type_id');
        });
    }
};
