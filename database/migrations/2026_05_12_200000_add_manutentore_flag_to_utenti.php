<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManutentoreFlagToUtenti extends Migration
{
    public function up()
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->boolean('manutentore')->default(false)->index();
        });
    }

    public function down()
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->dropColumn('manutentore');
        });
    }
}
