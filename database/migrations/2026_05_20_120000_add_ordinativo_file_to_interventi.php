<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdinativoFileToInterventi extends Migration
{
    public function up()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->string('ordinativo_file', 255)->nullable();
            $table->string('ordinativo_filename_originale', 255)->nullable();
            $table->timestamp('ordinativo_caricato_il')->nullable();
        });
    }

    public function down()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->dropColumn(['ordinativo_file', 'ordinativo_filename_originale', 'ordinativo_caricato_il']);
        });
    }
}
