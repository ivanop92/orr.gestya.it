<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSediTable extends Migration
{
    public function up()
    {
        Schema::create('sedi', function (Blueprint $table) {
            $table->id();
            $table->integer('id_azienda');
            $table->string('tipo', 20); // 'cliente' o 'fornitore'
            $table->integer('id_riferimento'); // id del cliente o fornitore
            $table->string('nome', 200); // es. "Sede Legale", "Magazzino Nord"
            $table->string('indirizzo', 200)->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('comune', 200)->nullable();
            $table->string('provincia', 10)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sedi');
    }
}
