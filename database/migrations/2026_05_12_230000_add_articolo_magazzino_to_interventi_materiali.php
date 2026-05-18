<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArticoloMagazzinoToInterventiMateriali extends Migration
{
    public function up()
    {
        Schema::table('interventi_materiali', function (Blueprint $table) {
            $table->unsignedInteger('id_articolo')->nullable()->index();
            $table->unsignedInteger('id_mg')->nullable()->index();
            $table->unsignedInteger('id_mgmov')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('interventi_materiali', function (Blueprint $table) {
            $table->dropColumn(['id_articolo', 'id_mg', 'id_mgmov']);
        });
    }
}
