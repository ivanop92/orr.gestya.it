<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Aggiunge id_operatore_assegnato alla tabella odl_righe (se non esiste)
        if (!Schema::hasColumn('odl_righe', 'id_operatore_assegnato')) {
            Schema::table('odl_righe', function (Blueprint $table) {
                $table->integer('id_operatore_assegnato')->nullable()->after('completato');
            });
        }

        // Crea la tabella per gli allegati delle fasi ODL
        Schema::create('odl_righe_allegati', function (Blueprint $table) {
            $table->id();
            $table->integer('id_odl_riga');
            $table->integer('id_odl');
            $table->integer('id_azienda');
            $table->integer('id_utente')->nullable();
            $table->string('nome_originale', 500);
            $table->string('nome_file', 500);
            $table->string('path_file', 500);
            $table->string('tipo_file', 100)->nullable();
            $table->integer('dimensione')->nullable();
            $table->string('descrizione', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('odl_righe_allegati');

        if (Schema::hasColumn('odl_righe', 'id_operatore_assegnato')) {
            Schema::table('odl_righe', function (Blueprint $table) {
                $table->dropColumn('id_operatore_assegnato');
            });
        }
    }
};
