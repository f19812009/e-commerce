<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'tracking_code')) {
                $table->string('tracking_code')->nullable()->after('courier_name');
            }
            if (! Schema::hasColumn('orders', 'tracking_note')) {
                $table->text('tracking_note')->nullable()->after('tracking_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_name', 'tracking_code', 'tracking_note']);
        });
    }
};
