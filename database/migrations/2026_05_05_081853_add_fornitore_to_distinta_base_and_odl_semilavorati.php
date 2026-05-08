<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFornitoreToDistintaBaseAndOdlSemilavorati extends Migration
{
    public function up()
    {
        Schema::table('distinta_base', function (Blueprint $table) {
            $table->integer('id_fornitore')->nullable()->after('id_materiale');
        });
    }

    public function down()
    {
        Schema::table('distinta_base', function (Blueprint $table) {
            $table->dropColumn('id_fornitore');
        });
    }
}
