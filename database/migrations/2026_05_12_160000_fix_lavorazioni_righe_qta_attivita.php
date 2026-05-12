<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixLavorazioniRigheQtaAttivita extends Migration
{
    public function up()
    {
        // Allinea al vecchio sw ORR (guasti_righe):
        //   qta DECIMAL(10,3) per supportare 3 decimali (es. 0.500)
        //   attivita DECIMAL(10,2) come moltiplicatore numerico (default 1.00)
        DB::statement('ALTER TABLE lavorazioni_righe MODIFY COLUMN qta DECIMAL(10,3) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE lavorazioni_righe MODIFY COLUMN attivita DECIMAL(10,2) NOT NULL DEFAULT 1');
    }

    public function down()
    {
        DB::statement('ALTER TABLE lavorazioni_righe MODIFY COLUMN qta DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE lavorazioni_righe MODIFY COLUMN attivita VARCHAR(50) NULL DEFAULT NULL');
    }
}
