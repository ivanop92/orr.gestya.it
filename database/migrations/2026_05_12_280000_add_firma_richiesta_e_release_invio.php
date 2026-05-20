<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaRichiestaEReleaseInvio extends Migration
{
    public function up()
    {
        // dotes: flag se la firma OTP e' richiesta sulla pagina pubblica (off = solo lettura)
        Schema::create('dotes_invii_email', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_dotes')->index();
            $table->unsignedInteger('id_azienda')->nullable()->index();
            $table->string('tipo', 30); // 'preventivo' | 'release_to_service' | 'fattura'
            $table->string('destinatari', 1000);
            $table->string('cc', 1000)->nullable();
            $table->boolean('firma_richiesta')->default(false); // valido per preventivo
            $table->unsignedInteger('inviato_da_id_utente')->nullable();
            $table->timestamp('inviato_il')->useCurrent();
        });

        // interventi: marker rapido data ultimo invio Release to Service
        Schema::table('interventi', function (Blueprint $table) {
            $table->timestamp('release_inviato_il')->nullable();
        });
    }

    public function down()
    {
        Schema::table('interventi', function (Blueprint $table) {
            $table->dropColumn('release_inviato_il');
        });
        Schema::dropIfExists('dotes_invii_email');
    }
}
