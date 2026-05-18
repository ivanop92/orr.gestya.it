<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterventiAllegatiEMateriali extends Migration
{
    public function up()
    {
        Schema::create('interventi_allegati', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_intervento')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_utente')->nullable();
            $table->string('filename', 500);       // path relativo
            $table->string('original_name', 255)->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('interventi_materiali', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_intervento')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->unsignedInteger('id_utente')->nullable();
            $table->string('codice', 100)->nullable();
            $table->string('descrizione', 500);
            $table->decimal('qta', 10, 3)->default(0);
            $table->string('um', 20)->default('PZ');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interventi_materiali');
        Schema::dropIfExists('interventi_allegati');
    }
}
