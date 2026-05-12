<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdVagoneToDotes extends Migration
{
    public function up()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->unsignedInteger('id_vagone')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn('id_vagone');
        });
    }
}
