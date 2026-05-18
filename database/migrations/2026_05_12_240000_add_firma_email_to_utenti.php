<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaEmailToUtenti extends Migration
{
    public function up()
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->text('firma_email')->nullable();
        });
    }

    public function down()
    {
        Schema::table('utenti', function (Blueprint $table) {
            $table->dropColumn('firma_email');
        });
    }
}
