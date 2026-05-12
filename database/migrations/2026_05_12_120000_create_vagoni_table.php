<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVagoniTable extends Migration
{
    public function up()
    {
        Schema::create('vagoni', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_cliente')->nullable()->index();
            $table->unsignedInteger('id_sede')->nullable()->index();

            $table->string('codice', 100)->nullable()->index();
            $table->string('tipo', 100)->nullable();

            $table->string('numero_uic', 20)->nullable()->index();
            $table->date('data_immatricolazione')->nullable();
            $table->date('data_ultima_revisione_generale')->nullable();
            $table->unsignedSmallInteger('intervallo_revisione_mesi')->nullable();
            $table->decimal('peso_a_vuoto_kg', 10, 2)->nullable();
            $table->decimal('portata_massima_kg', 10, 2)->nullable();
            $table->decimal('lunghezza_metri', 8, 2)->nullable();

            $table->text('note')->nullable();
            $table->boolean('attivo')->default(true);

            $table->unsignedInteger('id_utente')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vagoni');
    }
}
