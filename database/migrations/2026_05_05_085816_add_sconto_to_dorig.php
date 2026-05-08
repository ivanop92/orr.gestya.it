<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScontoToDorig extends Migration
{
    public function up()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->decimal('sconto_perc', 5, 2)->nullable()->default(0)->after('prezzo_unitario');
        });
    }

    public function down()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->dropColumn('sconto_perc');
        });
    }
}
