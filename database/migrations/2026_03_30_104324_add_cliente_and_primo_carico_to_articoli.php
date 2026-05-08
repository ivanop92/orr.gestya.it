<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClienteAndPrimoCaricoToArticoli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articoli', function (Blueprint $table) {
            $table->date('data_primo_carico')->nullable()->after('data_creazione');
        });
    }

    public function down()
    {
        Schema::table('articoli', function (Blueprint $table) {
            $table->dropColumn('data_primo_carico');
        });
    }
}
