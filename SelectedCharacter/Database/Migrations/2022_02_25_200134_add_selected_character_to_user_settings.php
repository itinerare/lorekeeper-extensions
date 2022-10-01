<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_settings', function (Blueprint $table) {
            //
            $table->integer('selected_character_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_settings', function (Blueprint $table) {
            //
            $table->dropColumn('selected_character_id');
        });
    }
};
