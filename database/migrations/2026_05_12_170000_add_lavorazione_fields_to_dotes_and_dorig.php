<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLavorazioneFieldsToDotesAndDorig extends Migration
{
    public function up()
    {
        // Testata: campi specifici flusso manutenzione (allineati a preventivi_testata vecchio sw)
        Schema::table('dotes', function (Blueprint $table) {
            $table->string('localita', 100)->nullable();
            $table->string('reason_intake', 255)->nullable();
            $table->text('note_operatore')->nullable();
            $table->string('automezzo', 100)->nullable(); // fallback testuale se non si usa l'anagrafica vagoni
        });

        // Righe: campi specifici riga di lavorazione (allineati a preventivi_righe / guasti_righe vecchio sw)
        Schema::table('dorig', function (Blueprint $table) {
            $table->string('servizio', 10)->nullable();
            $table->boolean('setup_tank')->default(false);
            $table->decimal('attivita', 10, 2)->nullable();
            $table->decimal('minuti', 10, 2)->nullable();
            $table->decimal('materiale', 12, 2)->nullable();
            $table->string('descrizione_materiale', 255)->nullable();
        });

        // qta su dorig: il vecchio sw usa 3 decimali (es. 0.500), nel nuovo era 3 decimali ma DECIMAL(10,3) e' gia' ok
        // Lo standardizzo per sicurezza (idempotente)
        DB::statement('ALTER TABLE dorig MODIFY COLUMN qta DECIMAL(10,3) NULL DEFAULT NULL');
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn(['localita', 'reason_intake', 'note_operatore', 'automezzo']);
        });

        Schema::table('dorig', function (Blueprint $table) {
            $table->dropColumn(['servizio', 'setup_tank', 'attivita', 'minuti', 'materiale', 'descrizione_materiale']);
        });
    }
}
