<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFornitoreToOdlSemilavorati extends Migration
{
    public function up()
    {
        Schema::table('odl_semilavorati', function (Blueprint $table) {
            $table->integer('id_fornitore')->nullable()->after('id_operatore');
        });
    }

    public function down()
    {
        Schema::table('odl_semilavorati', function (Blueprint $table) {
            $table->dropColumn('id_fornitore');
        });
    }
}
