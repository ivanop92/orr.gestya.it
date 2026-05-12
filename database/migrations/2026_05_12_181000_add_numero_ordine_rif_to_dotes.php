<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroOrdineRifToDotes extends Migration
{
    public function up()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->string('numero_ordine_rif', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn('numero_ordine_rif');
        });
    }
}
