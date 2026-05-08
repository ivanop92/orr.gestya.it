<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Espone $azienda_settings in TUTTE le view (con $usa_lotti, $usa_barcode)
        // cosi' i template possono fare @if($usa_lotti) senza che ogni controller lo passi.
        // Ricalcolato a ogni richiesta basandosi sulla session utente.
        View::composer('*', function ($view) {
            $utente = session('utente') ?? session('utente_produzione');
            if ($utente && !empty($utente->id_azienda)) {
                $azienda_settings = DB::table('aziende')->where('id', $utente->id_azienda)->first();
            } else {
                $azienda_settings = null;
            }
            $view->with('azienda_settings', $azienda_settings);
            $view->with('usa_lotti',   !empty($azienda_settings->usa_lotti));
            $view->with('usa_barcode', !empty($azienda_settings->usa_barcode));
        });
    }
}
