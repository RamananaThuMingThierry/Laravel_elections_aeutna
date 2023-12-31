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
        Schema::table('electeurs', function (Blueprint $table) {
            $table->integer('adhesion')->default(0)->comment('0: membre , 1: adhesion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electeurs', function (Blueprint $table) {
            $table->dropColumn('adhesion');
        });
    }
};
