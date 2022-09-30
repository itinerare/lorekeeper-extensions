<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('trade_listings', function (Blueprint $table) {
            $table->string('title')->after('id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('trade_listings', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
