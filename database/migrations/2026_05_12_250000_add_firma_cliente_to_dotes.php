<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaClienteToDotes extends Migration
{
    public function up()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->string('firma_token', 64)->nullable()->index();          // url-safe pubblico
            $table->string('firma_otp_hash', 100)->nullable();                 // hash dell'OTP corrente
            $table->timestamp('firma_otp_inviato_il')->nullable();
            $table->unsignedTinyInteger('firma_otp_tentativi')->default(0);    // contatore tentativi sbagliati
            $table->string('firma_telefono', 30)->nullable();                  // numero del firmatario
            $table->string('firma_ip', 64)->nullable();                        // IP del firmatario
            $table->string('firma_user_agent', 500)->nullable();
            $table->timestamp('firmato_il')->nullable();
            $table->string('firmato_da_nome', 200)->nullable();                // chi ha firmato (digitato)
        });
    }

    public function down()
    {
        Schema::table('dotes', function (Blueprint $table) {
            $table->dropColumn([
                'firma_token', 'firma_otp_hash', 'firma_otp_inviato_il', 'firma_otp_tentativi',
                'firma_telefono', 'firma_ip', 'firma_user_agent', 'firmato_il', 'firmato_da_nome',
            ]);
        });
    }
}
