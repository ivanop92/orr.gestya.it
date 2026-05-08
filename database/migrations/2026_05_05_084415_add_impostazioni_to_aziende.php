<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpostazioniToAziende extends Migration
{
    public function up()
    {
        Schema::table('aziende', function (Blueprint $table) {
            $table->boolean('usa_lotti')->default(true)->after('iban');
        });
    }

    public function down()
    {
        Schema::table('aziende', function (Blueprint $table) {
            $table->dropColumn('usa_lotti');
        });
    }
}
