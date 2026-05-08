<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Aggiunge il costo orario ai dipendenti
        Schema::table('utenti', function (Blueprint $table) {
            $table->decimal('costo_orario', 10, 2)->nullable()->default(0)->after('immagine');
        });

        // Aggiunge id_commessa alla tabella odl per collegare la produzione alla commessa
        Schema::table('odl', function (Blueprint $table) {
            $table->integer('id_commessa')->nullable()->after('id_azienda');
        });
    }

    public function down()
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->dropColumn('costo_orario');
        });

        Schema::table('odl', function (Blueprint $table) {
            $table->dropColumn('id_commessa');
        });
    }
};
