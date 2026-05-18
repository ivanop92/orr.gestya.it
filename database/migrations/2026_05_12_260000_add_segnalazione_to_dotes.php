<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSegnalazioneToDotes extends Migration
{
    public function up()
    {
        // dotes e' gia' al limite del row-size InnoDB. Tabella separata per segnalazioni.
        Schema::create('dotes_segnalazioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_dotes')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->text('testo');
            $table->string('contatto', 200)->nullable();
            $table->string('ip', 64)->nullable();
            $table->boolean('gestita')->default(false)->index();
            $table->unsignedInteger('gestita_da_id_utente')->nullable();
            $table->timestamp('gestita_il')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dotes_segnalazioni');
    }
}
