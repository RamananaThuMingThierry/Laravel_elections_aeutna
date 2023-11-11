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
            $table->time('heure_vote')->nullable()->comment('Heure du vote');
        });
    }

    public function down(): void
    {
        Schema::table('electeurs', function (Blueprint $table) {
            $table->dropColumn('heure_vote');
        });
    }
};
