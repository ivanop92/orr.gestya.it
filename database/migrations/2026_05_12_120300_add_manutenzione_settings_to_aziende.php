<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManutenzioneSettingsToAziende extends Migration
{
    public function up()
    {
        Schema::table('aziende', function (Blueprint $table) {
            $table->boolean('manut_anagrafica_vagoni_attiva')->default(false);
            $table->boolean('manut_certificato_ecm_separato')->default(false);
            $table->boolean('manut_workflow_accettazione_multistep')->default(false);
            $table->boolean('manut_magazzino_ricetta_default')->default(true);
            $table->boolean('manut_consuntivo_materiali_manutentore')->default(true);
            $table->decimal('manut_tariffa_oraria_default', 10, 2)->default(33.75);
        });
    }

    public function down()
    {
        Schema::table('aziende', function (Blueprint $table) {
            $table->dropColumn([
                'manut_anagrafica_vagoni_attiva',
                'manut_certificato_ecm_separato',
                'manut_workflow_accettazione_multistep',
                'manut_magazzino_ricetta_default',
                'manut_consuntivo_materiali_manutentore',
                'manut_tariffa_oraria_default',
            ]);
        });
    }
}
