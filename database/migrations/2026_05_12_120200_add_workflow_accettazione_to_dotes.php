<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowAccettazioneToDotes extends Migration
{
    public function up()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->string('stato_accettazione', 30)->nullable()->index();
            $table->text('motivo_rifiuto')->nullable();
            $table->unsignedSmallInteger('tentativi')->default(0);
            $table->unsignedInteger('accettato_da_id_utente')->nullable();
            $table->timestamp('inviato_revisione_il')->nullable();
            $table->timestamp('accettato_il')->nullable();
            $table->timestamp('rifiutato_il')->nullable();
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn([
                'stato_accettazione',
                'motivo_rifiuto',
                'tentativi',
                'accettato_da_id_utente',
                'inviato_revisione_il',
                'accettato_il',
                'rifiutato_il',
            ]);
        });
    }
}
