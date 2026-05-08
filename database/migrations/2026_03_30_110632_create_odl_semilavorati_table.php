<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdlSemilavoratiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odl_semilavorati', function (Blueprint $table) {
            $table->id();
            $table->integer('id_odl');
            $table->integer('id_azienda');
            $table->integer('id_articolo'); // il semilavorato
            $table->decimal('qta', 10, 2)->default(1);
            $table->tinyInteger('stato')->default(0); // 0=da fare, 1=completato
            $table->text('note')->nullable();
            $table->timestamp('completato_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('odl_semilavorati');
    }
}
