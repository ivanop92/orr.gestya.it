<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdVagoneToDorigAndOdl extends Migration
{
    public function up()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->unsignedInteger('id_vagone')->nullable()->index();
        });

        Schema::table('odl', function (Blueprint $table) {
            $table->unsignedInteger('id_vagone')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('dorig', function (Blueprint $table) {
            $table->dropColumn('id_vagone');
        });

        Schema::table('odl', function (Blueprint $table) {
            $table->dropColumn('id_vagone');
        });
    }
}
