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
            $table->date('ddn')->nullable()->comment('Date de naissance');
            $table->string('ldn', 255)->nullable()->comment('Lieu de naissance');
            $table->string('sexe', 10)->nullable()->comment('Genre (Male ou Femelle)');
            $table->string('cin', 12)->nullable()->comment('Care d\'Identité Nationale');
            $table->string('delivrance_cin', 255)->nullable()->comment('Lieu de délivrance CIN');
            $table->string('filieres', 100)->nullable();
            $table->string('niveau', 20)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('contact')->nullable();
            $table->string('axes', 100)->nullable();
            $table->string('sympathisant')->nullable();
            $table->string('facebook')->nullable();
            $table->date('date_inscription');
            $table->string('secteurs')->nullable();
            $table->integer('status')->default(0);
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
