<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLavorazioniTables extends Migration
{
    public function up()
    {
        Schema::create('lavorazioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->string('codice', 50)->nullable()->index();
            $table->string('descrizione', 255);
            $table->decimal('totale', 12, 2)->default(0);
            $table->boolean('attivo')->default(true);
            $table->unsignedInteger('id_utente')->nullable();
            $table->timestamps();
        });

        Schema::create('lavorazioni_righe', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_lavorazione')->index();
            $table->unsignedInteger('ordinamento')->default(0)->index();

            $table->string('servizio', 10)->nullable()->index();
            $table->string('codice', 100)->nullable();
            $table->boolean('setup_tank')->default(false);
            $table->text('descrizione')->nullable();
            $table->string('attivita', 50)->nullable();
            $table->decimal('qta', 10, 2)->default(0);
            $table->decimal('minuti', 10, 2)->default(0);
            $table->decimal('pu', 10, 2)->default(0);
            $table->unsignedSmallInteger('aliquota')->default(22);
            $table->decimal('imposta', 12, 2)->default(0);
            $table->decimal('imponibile', 12, 2)->default(0);
            $table->decimal('pt', 12, 2)->default(0);

            $table->decimal('materiale', 12, 2)->default(0);
            $table->string('descrizione_materiale', 255)->nullable();

            $table->unsignedInteger('id_utente')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lavorazioni_righe');
        Schema::dropIfExists('lavorazioni');
    }
}
