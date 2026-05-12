<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLavorazioneOrigineToDorig extends Migration
{
    public function up()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->unsignedInteger('id_lavorazione_origine')->nullable()->index();
            $table->unsignedInteger('id_lavorazione_riga_origine')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->dropColumn(['id_lavorazione_origine', 'id_lavorazione_riga_origine']);
        });
    }
}
