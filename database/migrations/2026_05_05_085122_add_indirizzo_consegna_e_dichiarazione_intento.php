<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndirizzoConsegnaEDichiarazioneIntento extends Migration
{
    public function up()
    {
        // Sede di consegna sui documenti
        Schema::table('dotes', function (Blueprint $table) {
            $table->integer('id_sede_consegna')->nullable()->after('id_cliente');
        });

        // Dichiarazione d'intento per i clienti
        Schema::table('clienti', function (Blueprint $table) {
            $table->boolean('esportatore_abituale')->default(false)->after('esigibilita_iva');
            $table->string('dich_intento_protocollo', 100)->nullable()->after('esportatore_abituale');
            $table->date('dich_intento_data')->nullable()->after('dich_intento_protocollo');
            $table->date('dich_intento_validita_da')->nullable()->after('dich_intento_data');
            $table->date('dich_intento_validita_a')->nullable()->after('dich_intento_validita_da');
            $table->decimal('dich_intento_importo', 14, 2)->nullable()->after('dich_intento_validita_a');
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn('id_sede_consegna');
        });
        Schema::table('clienti', function (Blueprint $table) {
            $table->dropColumn(['esportatore_abituale', 'dich_intento_protocollo', 'dich_intento_data', 'dich_intento_validita_da', 'dich_intento_validita_a', 'dich_intento_importo']);
        });
    }
}
