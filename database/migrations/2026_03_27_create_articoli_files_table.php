<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articoli_files', function (Blueprint $table) {
            $table->id();
            $table->integer('id_articolo');
            $table->integer('id_azienda');
            $table->string('nome_file', 500);
            $table->string('path_file', 500);
            $table->string('tipo_file', 100)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articoli_files');
    }
};
