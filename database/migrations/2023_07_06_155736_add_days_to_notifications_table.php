<?php

use App\Services\NotificationService;
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
        Schema::table('notifications', static function (Blueprint $table) {
            $table->string('days')->default('['.implode(',', array_keys(NotificationService::getAllWeekdays())).']');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', static function (Blueprint $table) {
            $table->dropColumn('days');
        });
    }
};
