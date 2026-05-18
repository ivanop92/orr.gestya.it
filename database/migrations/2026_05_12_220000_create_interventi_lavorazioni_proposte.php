<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterventiLavorazioniProposte extends Migration
{
    public function up()
    {
        Schema::create('interventi_lavorazioni_proposte', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_intervento')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_utente')->nullable();
            $table->unsignedInteger('ordinamento')->default(0);

            $table->string('servizio', 10)->nullable();
            $table->string('codice', 100)->nullable();
            $table->text('descrizione')->nullable();
            $table->boolean('setup_tank')->default(false);
            $table->decimal('attivita', 10, 2)->default(1);
            $table->decimal('qta', 10, 3)->default(0);
            $table->decimal('minuti', 10, 2)->default(0);
            $table->decimal('pu', 10, 2)->default(0);
            $table->unsignedSmallInteger('aliquota')->default(22);
            $table->decimal('imposta', 12, 2)->default(0);
            $table->decimal('imponibile', 12, 2)->default(0);
            $table->decimal('pt', 12, 2)->default(0);
            $table->decimal('materiale', 12, 2)->default(0);
            $table->string('descrizione_materiale', 255)->nullable();

            // Tracciabilita: se la riga proviene dal catalogo
            $table->unsignedInteger('id_lavorazione_origine')->nullable();
            $table->unsignedInteger('id_lavorazione_riga_origine')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interventi_lavorazioni_proposte');
    }
}
