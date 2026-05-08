<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->string('cig', 50)->nullable()->after('data_consegna');
            $table->string('cup', 50)->nullable()->after('cig');
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn(['cig', 'cup']);
        });
    }
};
