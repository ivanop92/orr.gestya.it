<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('commesse_documenti')) {
            Schema::create('commesse_documenti', function (Blueprint $table) {
                $table->id();
                $table->integer('id_commessa');
                $table->integer('id_dotes');
                $table->integer('id_azienda');
                $table->integer('id_utente');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('commesse_documenti');
    }
};
