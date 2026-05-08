<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaseOperatoreToOdlSemilavorati extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('odl_semilavorati', function (Blueprint $table) {
            $table->integer('id_fase')->nullable()->after('id_articolo');
            $table->integer('id_operatore')->nullable()->after('stato');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('odl_semilavorati', function (Blueprint $table) {
            $table->dropColumn(['id_fase', 'id_operatore']);
        });
    }
}
