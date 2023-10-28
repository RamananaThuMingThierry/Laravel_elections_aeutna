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
        Schema::create('electeurs', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->integer('numero_carte')->nullable()->unique(); 
            $table->string('nom', 255)->nullable();
            $table->string('prenom', 255)->nullable();
            $table->string('sexe', 10)->nullable()->comment('Genre (Male ou Femelle)');
            $table->string('cin', 12)->nullable()->comment('Care d\'Identité Nationale');
            $table->string('axes', 100)->nullable();
            $table->string('sympathisant')->nullable();
            $table->date('date_inscription');
            $table->string('secteurs')->nullable();
            $table->integer('status')->default(0);
            $table->unique(['nom', 'prenom', 'cin']);
            $table->string('votes', 50)->nullable()->comment('Piège jointe utilisé pour les élections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electeurs');
    }
};
