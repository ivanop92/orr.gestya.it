<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampiFerroviariToInterventi extends Migration
{
    public function up()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->string('codice_cuu', 50)->nullable()->index();           // es. "1.3.1.2"
            $table->string('numero_ordine_cliente', 100)->nullable();        // es. "1861750"
            $table->string('impianto', 100)->nullable();                     // es. "NOLA", "BARI", "SAN VITALIANO"
            $table->unsignedInteger('id_capo_squadra')->nullable();          // FK utenti (CS)
            $table->unsignedInteger('id_responsabile_manutenzione')->nullable(); // FK utenti (RM/ECM)
            $table->string('pdm_riferimento', 50)->nullable();               // es. "VPI"
            $table->string('matricola_carro_old', 100)->nullable();          // vecchia matricola (utile per Release to service)
            $table->string('odl_numero', 50)->nullable();                    // numero OdL ORR es. "1015"
        });
    }

    public function down()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->dropColumn([
                'codice_cuu', 'numero_ordine_cliente', 'impianto',
                'id_capo_squadra', 'id_responsabile_manutenzione',
                'pdm_riferimento', 'matricola_carro_old', 'odl_numero',
            ]);
        });
    }
}
