<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterventiTables extends Migration
{
    public function up()
    {
        Schema::create('interventi', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_utente')->nullable();

            // Step 1 - Ordinativo Ufficio
            $table->unsignedInteger('id_cliente')->nullable()->index();
            $table->unsignedInteger('id_vagone')->nullable()->index();
            $table->string('automezzo', 100)->nullable();
            $table->date('data_apertura')->nullable();
            $table->string('reason_intake', 255)->nullable();
            $table->string('localita', 100)->nullable();
            $table->string('priorita', 10)->default('media'); // bassa, media, alta
            $table->text('note')->nullable();

            // Step 2 - Assegnazione manutentore
            $table->unsignedInteger('id_operatore_assegnato')->nullable()->index();

            // Step 3 - Report manutentore
            $table->text('report_danni')->nullable();

            // Step 4 - Documenti emessi
            $table->unsignedInteger('id_dotes_preventivo')->nullable()->index();
            $table->unsignedInteger('id_dotes_certificato')->nullable()->index();

            // Step 5 - Accettazione
            $table->timestamp('accettato_il')->nullable();
            $table->unsignedInteger('accettato_da_id_utente')->nullable();
            $table->timestamp('rifiutato_il')->nullable();
            $table->text('motivo_rifiuto')->nullable();

            // Step 6 - Fattura
            $table->unsignedInteger('id_dotes_fattura')->nullable()->index();
            $table->timestamp('fattura_inviata_il')->nullable();

            // Workflow state
            $table->unsignedTinyInteger('step_corrente')->default(1); // 1..6
            $table->string('stato', 30)->default('in_corso')->index(); // in_corso, completato, annullato

            $table->timestamp('step_1_completato_il')->nullable();
            $table->timestamp('step_2_completato_il')->nullable();
            $table->timestamp('step_3_completato_il')->nullable();
            $table->timestamp('step_4_completato_il')->nullable();
            $table->timestamp('step_5_completato_il')->nullable();
            $table->timestamp('step_6_completato_il')->nullable();

            $table->timestamps();
        });

        Schema::create('interventi_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_intervento')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_utente')->nullable();
            $table->unsignedTinyInteger('step')->nullable();
            $table->string('azione', 50); // aperto, completato, rifiutato, annullato, riassegnato, ecc
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interventi_log');
        Schema::dropIfExists('interventi');
    }
}
