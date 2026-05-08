<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommesseMovimentiCassaTable extends Migration
{
    public function up()
    {
        Schema::create('commesse_movimenti_cassa', function (Blueprint $table) {
            $table->id();
            $table->integer('id_azienda');
            $table->integer('id_commessa');
            $table->enum('tipo', ['entrata', 'uscita']);
            $table->decimal('importo', 10, 2);
            $table->date('data_movimento');
            $table->string('descrizione', 300);
            $table->string('modalita_pagamento', 100)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commesse_movimenti_cassa');
    }
}
