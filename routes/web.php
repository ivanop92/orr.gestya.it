<?php
Route::get('/', function () {
    return redirect('/admin/login');
});
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanoniController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::any('',function(){ return redirect('/admin/login'); });
Route::any('admin/login',array('uses'=>'AdminController@login'));
Route::any('admin/index',array('uses'=>'AdminController@index'));
Route::any('admin/aziende',array('uses'=>'AdminController@aziende'));
Route::any('admin/moduli',array('uses'=>'AdminController@moduli'));
Route::any('admin/utenti',array('uses'=>'AdminController@utenti'));
Route::any('admin/logout',array('uses'=>'AdminController@logout'));


Route::any('utente/index',array('uses'=>'UtenteController@index'));
Route::any('utente/cambia_anno/{anno}',array('uses'=>'UtenteController@cambia_anno'));
Route::any('utente/dipendenti',array('uses'=>'UtenteController@dipendenti'));
Route::any('utente/clienti',array('uses'=>'UtenteController@clienti'));
Route::any('utente/fornitori',array('uses'=>'UtenteController@fornitori'));
Route::any('utente/dettaglio_fornitore/{id}',array('uses'=>'UtenteController@dettaglio_fornitore'));
Route::any('utente/vagoni',array('uses'=>'UtenteController@vagoni'));
Route::any('utente/dettaglio_vagone/{id}',array('uses'=>'UtenteController@dettaglio_vagone'));
Route::post('utente/documento_workflow/{id_dotes}/{azione}',array('uses'=>'UtenteController@documento_workflow'));
Route::any('utente/lavorazioni',array('uses'=>'UtenteController@lavorazioni'));
Route::post('utente/lavorazioni/importa_csv',array('uses'=>'UtenteController@importa_lavorazioni_csv'));
Route::any('utente/dettaglio_lavorazione/{id}',array('uses'=>'UtenteController@dettaglio_lavorazione'));
Route::post('utente/ajax/ordina_righe_lavorazione/{id_lavorazione}',array('uses'=>'UtenteController@ordina_righe_lavorazione'));
Route::get('utente/ajax/catalogo_lavorazioni_righe',array('uses'=>'UtenteController@ajax_catalogo_lavorazioni_righe'));

// Interventi Manutenzione (workflow 6-step)
Route::any('utente/interventi',array('uses'=>'UtenteController@interventi'));
Route::any('utente/interventi/nuovo',array('uses'=>'UtenteController@interventi_nuovo'));
Route::any('utente/interventi/{id}',array('uses'=>'UtenteController@interventi_dettaglio'));
Route::post('utente/interventi/{id}/completa_step',array('uses'=>'UtenteController@interventi_completa_step'));
Route::post('utente/interventi/{id}/step2_assegna',array('uses'=>'UtenteController@interventi_step_2_assegna'));
Route::post('utente/interventi/{id}/step3_report',array('uses'=>'UtenteController@interventi_step_3_report'));
Route::post('utente/interventi/{id}/step4_emetti_preventivo',array('uses'=>'UtenteController@interventi_step_4_emetti_preventivo'));
Route::post('utente/interventi/{id}/step5_decisione',array('uses'=>'UtenteController@interventi_step_5_decisione'));
Route::post('utente/interventi/{id}/invia_preventivo_email',array('uses'=>'UtenteController@interventi_invia_preventivo_email'));

// Pagina pubblica firma cliente (no login) — link via mail
Route::get('firma/{token}',array('uses'=>'FirmaController@preventivo'));
Route::post('firma/{token}/invia_otp',array('uses'=>'FirmaController@invia_otp'));
Route::post('firma/{token}/verifica_otp',array('uses'=>'FirmaController@verifica_otp'));
Route::post('utente/interventi/{id}/step6_fattura',array('uses'=>'UtenteController@interventi_step_6_fattura'));

// Dashboard Manutentore (responsive smartphone/tablet)
Route::any('manutentore/dashboard',array('uses'=>'UtenteController@manutentore_dashboard'));
Route::any('manutentore/intervento/{id}',array('uses'=>'UtenteController@manutentore_intervento'));
Route::post('manutentore/intervento/{id}/invia_report',array('uses'=>'UtenteController@manutentore_invia_report'));
Route::any('manutentore/storico',array('uses'=>'UtenteController@manutentore_storico'));
Route::post('manutentore/allegato/{id_allegato}/elimina',array('uses'=>'UtenteController@manutentore_elimina_allegato'));
Route::get('manutentore/ajax/cerca_righe_catalogo',array('uses'=>'UtenteController@manutentore_search_righe_catalogo'));
Route::get('manutentore/ajax/cerca_articoli',array('uses'=>'UtenteController@manutentore_search_articoli'));
Route::any('manutentore/logout',array('uses'=>'UtenteController@logout'));
Route::post('utente/applica_lavorazioni_a_documento/{id_dotes}',array('uses'=>'UtenteController@applica_lavorazioni_a_documento'));
Route::post('utente/ajax/ordina_righe_documento/{id_dotes}',array('uses'=>'UtenteController@ordina_righe_documento'));
Route::any('utente/articoli',array('uses'=>'UtenteController@articoli'));
Route::any('utente/fasi_di_lavorazione',array('uses'=>'UtenteController@fasi_di_lavorazione'));
Route::any('utente/articoli',array('uses'=>'UtenteController@articoli'));
Route::any('utente/odl',array('uses'=>'UtenteController@odl'));
Route::any('utente/dettaglio_odl/{id}',array('uses'=>'UtenteController@dettaglio_odl'));
Route::any('utente/programmazione', array('uses'=>'UtenteController@programmazione'));
Route::any('utente/distinta_base', 'UtenteController@distinta_base');
Route::any('utente/distinta_base/dettaglio/{id}', 'UtenteController@distinta_base_dettaglio');
Route::any('utente/gestione_documenti', 'UtenteController@gestione_documenti');
Route::any('utente/riepilogo_documenti/{cd_do}', 'UtenteController@riepilogo_documenti');
Route::any('utente/riepilogo_documenti/{cd_do}/{mese}', 'UtenteController@riepilogo_documenti');
Route::any('utente/crea_documento/{cd_do}', 'UtenteController@crea_documento');
Route::any('utente/modifica_documento/{id}', 'UtenteController@modifica_documento');
Route::any('utente/duplica_documento/{id}', 'UtenteController@duplica_documento');
Route::any('utente/dettaglio_documento/{id}', 'UtenteController@dettaglio_documento');
Route::any('utente/documenti_da_registrare', array('uses'=>'UtenteController@documenti_da_registrare'));
Route::any('utente/visualizza_xml_da_file/{id}', array('uses'=>'UtenteController@visualizza_xml_da_file'));
Route::any('utente/scarica_xml/{id}', 'UtenteController@scarica_xml');
Route::any('utente/visualizza_xml/{id}', 'UtenteController@visualizza_xml');
Route::any('utente/salva_scadenze/{id_dotes}', 'UtenteController@salva_scadenze');
Route::any('utente/get_scadenza/{id}', 'UtenteController@get_scadenza');
Route::any('utente/registra_pagamento', 'UtenteController@registra_pagamento');
Route::any('utente/scadenziario', array('uses'=>'UtenteController@scadenziario'));
Route::any('/track-email/{tracking_id}', 'UtenteController@trackEmail');
Route::any('/utente/profilo', array('uses'=>'UtenteController@profilo'));
Route::any('utente/gestione_magazzini', 'UtenteController@gestione_magazzini');
Route::get('/utente/magazzino/giacenze/{id_mg}','UtenteController@giacenze_magazzino');
Route::get('/utente/magazzino/movimenti/{id_articolo}/{id_mg}','UtenteController@movimenti_magazzino');
Route::get('/ajax/get_distinta_base_tree/{id}','UtenteController@getDistintaBaseTree');

Route::any('/utente/mg/{id}/{codice_magazzino}/',array('uses'=>'UtenteController@magazzini'));
Route::any('/utente/mg/carico', 'UtenteController@carico');
Route::any('/utente/mg/scarico', 'UtenteController@scarico');
Route::any('utente/magazzino/trasferisci', 'UtenteController@trasferisci_magazzino');
Route::any('/utente/mg/trasferimento_mg', 'UtenteController@trasferimento_mg');
Route::any('/utente/mg/inventario', 'UtenteController@inventario');

Route::any('/utente/commesse', 'UtenteController@commesse');
Route::any('utente/commesse/{id_commessa}/attivita', array('uses'=>'UtenteController@commesse_attivita'));
Route::any('utente/commesse/{id_commessa}/dashboard', array('uses'=>'UtenteController@commesse_dashboard'));

// API per gestione attività commesse
Route::get('utente/commesse/attivita/{id}', array('uses'=>'UtenteController@get_attivita'));
Route::post('utente/commesse/attivita', array('uses'=>'UtenteController@crea_attivita'));
Route::put('utente/commesse/attivita/{id}', array('uses'=>'UtenteController@aggiorna_attivita'));
Route::delete('utente/commesse/attivita/{id}', array('uses'=>'UtenteController@elimina_attivita'));

// Rotte per le voci extra delle commesse
Route::get('utente/commesse/voci-extra/{id}', array('uses'=>'CommesseExtraController@get_voce_extra'));
Route::post('utente/commesse/voci-extra', array('uses'=>'CommesseExtraController@crea_voce_extra'));
Route::put('utente/commesse/voci-extra/{id}', array('uses'=>'CommesseExtraController@aggiorna_voce_extra'));
Route::delete('utente/commesse/voci-extra/{id}', array('uses'=>'CommesseExtraController@elimina_voce_extra'));

Route::get('utente/commesse/{id_commessa}/gantt/load', array('uses'=>'GanttController@load'));
Route::post('utente/commesse/{id_commessa}/gantt/task', array('uses'=>'GanttController@task'));
Route::post('utente/commesse/{id_commessa}/gantt/export-pdf', array('uses'=>'GanttController@exportPDF'));

Route::post('utente/canoni/{id_commessa}/gantt/export-pdf', array('uses'=>'GanttController@exportPDF'));


Route::any('produzione/login/{token_azienda}', ['uses' => 'ProduzioneController@login']);
Route::any('produzione/dashboard', ['uses' => 'ProduzioneController@dashboard']);
Route::any('produzione/dettaglio_odl/{id}', ['uses' => 'ProduzioneController@dettaglio_odl']);
Route::post('produzione/start_fase', ['uses' => 'ProduzioneController@start_fase']);
Route::post('produzione/fine_fase', ['uses' => 'ProduzioneController@fine_fase']);
Route::post('produzione/cambia_fase', ['uses' => 'ProduzioneController@cambia_fase']);
Route::any('produzione/logout', ['uses' => 'ProduzioneController@logout']);
Route::post('produzione/salva_nota_fase', ['uses' => 'ProduzioneController@salva_nota_fase']);

// Allegati ODL fasi (admin)
Route::post('utente/odl_carica_allegato/{id_riga}', ['uses' => 'UtenteController@odl_carica_allegato']);
Route::get('utente/odl_elimina_allegato/{id}', ['uses' => 'UtenteController@odl_elimina_allegato']);



Route::any('utente/logout',array('uses'=>'UtenteController@logout'));
Route::any('utente/impostazioni',array('uses'=>'UtenteController@impostazioni'));


Route::any('utente/ajax/modifica_cliente/{id}',array('uses'=>'UtenteAjaxController@modifica_cliente'));
Route::any('utente/ajax/riepilogo_documenti_mese', 'UtenteAjaxController@riepilogo_documenti_mese');

Route::any('stampa/documento/{id}', 'StampaController@documento');

Route::any('stampa/tracciabilita/{id_odl}',array('uses'=>'StampaController@tracciabilita'));

Route::any('tecnopack/magazzino/{token}',array('uses'=>'TecnopackController@magazzino'));
Route::any('tecnopack/gridhandler',array('uses'=>'TecnopackController@gridhandler'));


/*

Route::any('',array('uses'=>'HomeController@index'));
Route::any('admin/login',array('uses'=>'AdminController@login'));
Route::any('admin/index',array('uses'=>'AdminController@index'));
Route::any('admin/clienti',array('uses'=>'AdminController@clienti'));
Route::any('admin/articoli',array('uses'=>'AdminController@articoli'));
Route::any('admin/odl',array('uses'=>'AdminController@odl'));
Route::any('admin/dettaglio_odl/{id_odl}',array('uses'=>'AdminController@dettaglio_odl'));
Route::any('admin/fornitori',array('uses'=>'AdminController@fornitori'));
Route::any('admin/dettaglio_utente/{id}',array('uses'=>'AdminController@dettaglio_utente'));
Route::any('admin/fatture/{anno}',array('uses'=>'AdminController@fatture'));

*/


Route::any('cliente/index',array('uses'=>'ClienteController@index'));

//DIPENDENTI - REPARTI
Route::any('admin/dipendenti',array('uses'=>'AdminController@dipendenti'));
Route::any('admin/modifica_dipendente/{id}',array('uses'=>'AjaxController@modifica_dipendente'));
Route::any('admin/modifica_reparto/{id}',array('uses'=>'AjaxController@modifica_reparto'));

//TASK
Route::any('admin/commesse_in_lavorazione',array('uses'=>'AdminController@commesse_in_lavorazione'));
Route::any('admin/commesse_chiuse',array('uses'=>'AdminController@commesse_chiuse'));
Route::any('admin/commesse_archiviate',array('uses'=>'AdminController@commesse_archiviate'));
Route::any('admin/modifica_task/{id}',array('uses'=>'AjaxController@modifica_task'));
Route::any('admin/task/assegna/{id}',array('uses'=>'AjaxController@assegna_task'));
Route::any('admin/lavoro/aggiungi_task/{id}',array('uses'=>'AjaxController@aggiungi_task'));
Route::any('admin/task_assegnati',array('uses'=>'AdminController@task_assegnati'));
Route::any('admin/task_chiusi',array('uses'=>'AdminController@task_chiusi'));
Route::any('admin/task_sospesi',array('uses'=>'AdminController@task_sospesi'));
Route::any('admin/chiudi_task/{id}',array('uses'=>'AjaxController@chiudi_task'));
Route::any('admin/sospendi_task/{id}',array('uses'=>'AjaxController@sospendi_task'));
Route::any('admin/info_task_chiuso/{id}',array('uses'=>'AjaxController@info_task_chiuso'));
Route::any('admin/info_task_sospeso/{id}',array('uses'=>'AjaxController@info_task_sospeso'));


//TASK GIORNALIERI



//BANDI
Route::any('admin/bandi',array('uses'=>'AdminController@bandi'));
Route::any('admin/progetti/{id_reparto}',array('uses'=>'AdminController@progetti'));
Route::any('admin/modifica_bando/{id}',array('uses'=>'AjaxController@modifica_bando'));
Route::any('admin/bandi_archiviati',array('uses'=>'AdminController@bandi_archiviati'));
Route::any('admin/bandi_allegati',array('uses'=>'AdminController@bandi_allegati'));
Route::any('admin/modifica_bandi_allegati/{id}',array('uses'=>'AjaxController@modifica_bandi_allegati'));
Route::any('admin/aggiungi_allegati/{id}',array('uses'=>'AjaxController@aggiungi_allegati'));
Route::any('admin/aggiungi_utenti/{id}',array('uses'=>'AjaxController@aggiungi_utenti'));
/*PULSANTI GESTIONE CLIENTI E MAIL*/
Route::any('admin/visualizza_stato_clienti/{id}',array('uses'=>'AjaxController@visualizza_stato_clienti'));
Route::any('admin/invia_mail_bando_clienti/{id}',array('uses'=>'AjaxController@invia_mail_bando_clienti'));
/*ROUTE TOKEN+TOKEN*/
Route::any('bandi/{token_utente_per_bando}/{token_bando}',array('uses'=>'AdminController@invio_dati'));

Route::any('bandi/frc',array('uses'=>'AdminController@form_frc'));



Route::any('admin/leads/{status}',array('uses'=>'AdminController@leads'));
Route::any('admin/preventivi/{status}',array('uses'=>'AdminController@preventivi'));
Route::any('admin/cashflow/{status}',array('uses'=>'AdminController@cashflow'));

Route::any('admin/logout',array('uses'=>'AdminController@logout'));


Route::any('agente/index',array('uses'=>'AgenteController@index'));
Route::any('agente/logout',array('uses'=>'AgenteController@logout'));


Route::any('cliente/index',array('uses'=>'ClienteController@index'));
Route::any('cliente/formazione_40',array('uses'=>'ClienteController@formazione_40'));

Route::any('cliente/formazione_40/moduli/calendario/{id_modulo}',array('uses'=>'ClienteController@calendario'));
Route::any('cliente/formazione_40/moduli/{id_corso}',array('uses'=>'ClienteController@moduli'));
Route::any('cliente/formazione_40/corsisti/{id_corso}',array('uses'=>'ClienteController@corsisti'));
Route::any('cliente/torna_admin',array('uses'=>'ClienteController@torna_admin'));
Route::any('cliente/logout',array('uses'=>'ClienteController@logout'));

Route::any('stampa/fascicolo_corso/{id_corso}',array('uses'=>'StampaController@fascicolo_corso'));

Route::any('ajax/salva_token_onesignal/{tipologia}/{token}', 'AjaxController@salva_token_onesignal');
/*nuova route articoli*/
Route::any('ajax/modifica_articolo_carico/{id_articolo}',array('uses'=>'AjaxController@modifica_articolo_carico'));
Route::any('ajax/modifica_articolo_scarico/{id_articolo}',array('uses'=>'AjaxController@modifica_articolo_scarico'));
Route::any('ajax/modifica_articolo_rettifica/{id_articolo}',array('uses'=>'AjaxController@modifica_articolo_rettifica'));
Route::any('ajax/modifica_articolo_movimenti/{id_articolo}',array('uses'=>'AjaxController@modifica_articolo_movimenti'));


/*ciclo attivo GESTYA*/

/*documenti*/
Route::any('documenti/{ciclo}/{cd_do}',array('uses'=>'AdminController@documenti'));
Route::any('/crea_documento/{cd_do}',array('uses'=>'AdminController@crea_documento'));
Route::any('ajax/getClienteForOrdine',array('uses'=>'AjaxController@getClienteForOrdine'));
Route::any('dettaglio_documento/{id}',array('uses'=>'AdminController@dettaglio_documento'));
Route::any('modifica_documento/{id}',array('uses'=>'AdminController@modifica_documento'));
Route::any('evadi_documento/{id}',array('uses'=>'AjaxController@evadi_documento'));
Route::post('/elimina_ordine', array('uses' => 'AdminController@eliminaOrdine' ) );
Route::post('evadi-quantita', 'AdminController@evadiQuantita')->name('evadi.quantita');
Route::post('/admin/fatt/creafatturaevasa', 'AdminController@creaFatturaEvasa')->name('creafatturaevasa');
Route::any('/admin/fatt/importa_fatture_xml', 'AdminController@importa_fatture_xml')->name('importa.fattura.xml');


/*magazzini*/
//Route::any('/utente/mg/{id}/{codice_magazzino}/',array('uses'=>'UtenteController@magazzini'));
Route::any('/gestione_magazzini', 'AdminController@gestione_magazzini');
Route::post('/admin/salva_punto_riordino', 'AdminController@salvaPuntoRiordino');
Route::get('/controllo-riordino', 'AdminController@controlloPuntoDiRiordino')->name('controllo.riordino');
Route::any('/inventario', 'AdminController@inventario');

Route::any('/check', 'UtenteController@controlloArticolo');
Route::any('/update-giacenza/{id}','AdminController@updateGiacenza');
Route::any('utente/carico-magazzino/{id}', 'UtenteController@caricoMagazzino');
Route::any('utente/scarico-magazzino/{id}', 'UtenteController@scaricoMagazzino');
Route::any('utente/trasferimento_mg', 'UtenteController@trasferimento_mg');

Route::any('/trasferimento-magazzino/{id}', 'AdminController@trasferimentoMagazzino');
Route::any('/decode-barcode', 'AdminController@decodeBarcode');


Route::any('cron/importa_fatture_xml',array('uses'=>'CronController@importa_fatture_xml'));
Route::any('cron/get_risposte_sogei',array('uses'=>'CronController@get_risposte_sogei'));

Route::any('stampa/etichetta_odl/{id_odl}',array('uses'=>'StampaController@stampa_etichetta_odl'));
Route::any('stampa/dettaglio_odl/{id_odl}',array('uses'=>'StampaController@dettaglio_odl'));

Route::get('/utente/commesse/resource-gantt-data', 'UtenteController@getResourceGanttData');





/*PRODUZIONE*/
Route::any('/fasi_di_lavorazione', 'AdminController@fasiLavorazione');
Route::any('/update-fase', 'AdminController@updateFase');
Route::any('distinta-base/{id}', 'AdminController@distintaBase')->name('distinta_base');
// Rotta per ottenere gli ordini con tipo_documento 'ord'
Route::get('/get-ordini', 'AjaxController@getOrdini')->name('get-ordini');

// Rotta per ottenere gli articoli collegati a un ordine specifico
Route::get('/get-articoli', 'AjaxController@getArticoli')->name('get-articoli');


/*documenti di trasporto*/
Route::any('documenti_di_trasporto',array('uses'=>'AdminController@documenti_di_trasporto'));
Route::get('/ajax/getProductByBarcode/{barcode}', 'AdminController@getProductByBarcode');

/*Aziende e Utenti*/
Route::any('aziende/', ('AdminController@aziende'))->name('aziende');
Route::any('utenti/', ('AdminController@utentiSuperAdmin'))->name('utenti');
Route::any('utentiAdmin/', ('AdminController@utentiAdmin'))->name('utentiAdmin');
Route::any('admin/effettua_login', ('AdminController@effettua_login'))->name('effettua_login');
Route::any('moduli/', ('AdminController@moduli'))->name('moduli');
/*MODULI*/
Route::any('/api/moduli/aggiungi-aziende', 'AdminController@aggiungiAziende')->name('moduli.aggiungiAziende');

/*MODULO CONTRATTO*/
Route::any('contratti/', ('AdminController@contratti'))->name('contratti');
Route::get('contratti/dettagli/{id}', 'AdminController@dettagliContratto')->name('contratti.dettagli');
Route::post('/contratti/evadi-tutto-fattura', 'AdminController@evadiTuttoInFattura')
    ->name('contratti.evadiTuttoFattura');
Route::any('/contratti/fatturazione-periodica', 'AdminController@aggiornaFatturazionePeriodica')->name('contratti.fatturazionePeriodica');
Route::get('/fatturazione-automatica', 'AdminController@fatturazioneAutomatica');
Route::post('contratti/aggiornaOre', 'AdminController@aggiornaOreContratto');
Route::post('contratti/fattura', 'AdminController@fatturaContratto')->name('contratti.fattura');


//JSON Datatable

Route::any('json/lista_fatture/{anno}', 'JsonController@lista_fatture');
Route::any('admin/ajax/modifica_testata_fattura/{id}', 'AjaxController@modifica_testata_fattura');

// Contabilizza una fattura
/*Route::post('/fattura/contabilizza/{id}', 'AdminController@contabilizza')->name('fattura.contabilizza');*/
Route::any('/riepilogo/{anno}', 'AdminController@riepilogo')->name('riepilogo');
// Mostra i dettagli della fattura
Route::any('/dettaglio_fattura/{id}', 'AdminController@dettaglio_fattura')->name('fattura.dettaglio');
// Aggiungi un pagamento alla fattura
Route::post('/pagamento/inserisci/{id}', 'AdminController@inserisci_pagamento')->name('pagamento.inserisci');
Route::any('/pagamento/elimina/{id}', 'AdminController@elimina_pagamento')->name('pagamento.elimina');

Route::post('/fattura/configura_pagamento_rate/{id}', 'AdminController@configura_pagamento_rate')
    ->name('fattura.configura_pagamento_rate');

Route::any('/fattura/{id}/elimina_rata', 'AdminController@eliminaRata')->name('fattura.elimina_rata');

Route::post('/fattura/{id}/aggiorna_rate', 'AdminController@aggiorna_rate')->name('fattura.aggiorna_rate');
Route::post('/fattura/marca-saldata/{id}', 'AdminController@marcaSaldata')->name('fattura.marca_saldata');
Route::any('/utente/esporta_scadenze_pdf', 'UtenteController@esportaScadenzePdf')->name('fattura.elimina_rata');
Route::any('/utente/esporta_scadenze_recupero_crediti', 'UtenteController@esportaScadenzeRecuperoCrediti')->name('scadenze.recupero_crediti');

// Rotte per la gestione dei canoni
Route::get('/canoni', [CanoniController::class, 'index'])->name('canoni.index');
Route::post('/canoni', [CanoniController::class, 'store'])->name('canoni.store');
Route::put('/canoni/{id}', [CanoniController::class, 'update'])->name('canoni.update');
Route::delete('/canoni/{id}', [CanoniController::class, 'destroy'])->name('canoni.destroy');
Route::post('/canoni/genera-fatture', [CanoniController::class, 'generaFatture'])->name('canoni.genera-fatture');

// Route per il dettaglio articolo
Route::any('utente/dettaglio_articolo/{id}', array('uses' => 'ArticoloController@dettaglio_articolo'));
Route::post('utente/elimina_file_articolo', array('uses' => 'UtenteController@elimina_file_articolo'));

// Route per aggiungere famiglie e categorie via AJAX
Route::post('utente/aggiungi_famiglia', array('uses' => 'ArticoloController@aggiungi_famiglia'));
Route::post('utente/aggiungi_categoria', array('uses' => 'ArticoloController@aggiungi_categoria'));

// Route per la gestione dei materiali nella distinta base
Route::get('ajax/get_materiali', array('uses' => 'AjaxController@get_materiali'));
Route::post('ajax/aggiungi_materiale_distinta_base', array('uses' => 'AjaxController@aggiungi_materiale_distinta_base'));

Route::get('utente/ajax/stampa_barcode', array('uses'=>'ArticoloController@stampaBarcode'));
Route::any('utente/ajax/carico_scarico_magazzino', array('uses'=>'ArticoloController@carico_scarico_magazzino'));

// Rotte per la gestione degli articoli alternativi
Route::get('utente/ajax/cerca_articoli', array('uses'=>'ArticoloController@cercaArticoli'));
Route::post('utente/ajax/aggiungi_alternativa', array('uses'=>'ArticoloController@aggiungiAlternativa'));
Route::post('utente/ajax/rimuovi_alternativa/{id}', array('uses'=>'ArticoloController@rimuoviAlternativa'));
Route::any('utente/dettaglio_cliente/{id}', array('uses'=>'UtenteController@dettaglio_cliente'));
Route::any('utente/get-disponibilita/{id}', 'UtenteController@getDisponibilita');

Route::any('utente/preventivi/gantt/{id_preventivo}', array('uses'=>'UtenteController@show_gantt'));
Route::any('utente/agenti', array('uses'=>'UtenteController@agenti'));
Route::any('utente/provvigioni_agente/{id_agente}', array('uses'=>'UtenteController@provvigioni_agente'));
Route::get('utente/get_prodotto_da_barcode', 'UtenteController@get_prodotto_da_barcode');
Route::any('utente/visualizza_provvigioni', array('uses'=>'UtenteController@visualizza_provvigioni'));
// Aggiungi questa route al file routes/web.php
// Rotte per l'importazione delle anagrafiche
Route::get('utente/import_anagrafiche', ['uses' => 'ImportAnagraficheFattureInCloudController@index']);
Route::post('utente/import_clienti_excel', ['uses' => 'ImportAnagraficheFattureInCloudController@import_clienti_excel']);
Route::post('utente/import_fornitori_excel', ['uses' => 'ImportAnagraficheFattureInCloudController@import_fornitori_excel']);
Route::post('utente/import_articoli_excel', ['uses' => 'ImportAnagraficheFattureInCloudController@import_articoli_excel']);

// Dettaglio giacenza
Route::get('/utente/dettaglio-giacenza', 'UtenteController@dettaglioGiacenza')->name('magazzino.dettaglio-giacenza');

// Rotte per i listini prezzi
// Rotte per la gestione listini
Route::any('utente/listini', 'ListiniController@index');
Route::any('utente/listini/create', 'ListiniController@create');
Route::any('utente/listini/dettaglio/{id}', 'ListiniController@dettaglio');
Route::any('utente/listini/aggiungi_articolo/{id_listino}', 'ListiniController@aggiungi_articolo');
Route::any('utente/listini/elimina_articolo/{id_listino}/{id_articolo_listino}', 'ListiniController@elimina_articolo');
Route::any('utente/listini/aggiungi_cliente/{id_listino}', 'ListiniController@aggiungi_cliente');
Route::any('utente/listini/elimina_cliente/{id_listino}/{id_cliente_listino}', 'ListiniController@elimina_cliente');
Route::any('utente/listini/importa_csv/{id_listino}', 'ListiniController@importa_csv');
Route::any('utente/listini/esempio_csv', 'ListiniController@scarica_esempio_csv');
// Aggiungi questa route al file routes/web.php
Route::get('/utente/ajax/get_prezzo_articolo_simple', 'UtenteAjaxController@get_prezzo_articolo_simple');
// Rotte per la produzione (aggiungi queste nuove rotte)
Route::post('produzione/cambia_tipo_lavoro', array('uses'=>'ProduzioneController@cambia_tipo_lavoro'));
Route::get('produzione/dettaglio_attivita/{id}', array('uses'=>'ProduzioneController@dettaglio_attivita'));
// Rotte per la gestione delle attività delle commesse
Route::any('produzione/start_attivita/{id}', array('uses'=>'ProduzioneController@start_attivita'));
Route::any('produzione/fine_attivita/{id}', array('uses'=>'ProduzioneController@fine_attivita'));
Route::any('produzione/aggiorna_attivita/{id}', array('uses'=>'ProduzioneController@aggiorna_attivita'));
Route::post('produzione/carica_allegato/{id}', array('uses'=>'ProduzioneController@carica_allegato'));
Route::get('produzione/elimina_allegato/{id_allegato}', array('uses'=>'ProduzioneController@elimina_allegato'));
Route::any('utente/commesse/{id_commessa}/attivita/{id_attivita}', array('uses'=>'UtenteController@commesse_attivita_dettaglio'));
Route::get('/utente/controllo-articolo-scarico', array('uses'=>'UtenteController@controlloArticoloScarico'));
Route::post('/utente/download-etichetta', 'UtenteController@downloadEtichetta');

// Routes principali per MappingBarcodeController
Route::get('utente/ricezione_barcode', 'MappingBarcodeController@index')->name('utente.ricezione_barcode');
Route::post('utente/ricezione_barcode', 'MappingBarcodeController@index');
Route::get('utente/decode_barcode', 'MappingBarcodeController@decode_barcode');
Route::post('utente/carico_barcode', 'MappingBarcodeController@carico_barcode');
Route::post('utente/stampa_etichetta_barcode', 'MappingBarcodeController@stampa_etichetta_barcode');

// Route per gli esempi di barcode (utile per testing)
Route::get('/utente/esempi_barcode', 'MappingBarcodeController@getEsempiBarcode');
// In routes/web.php, aggiungi questa route

Route::post('utente/check_partita_iva', array('uses'=>'UtenteController@check_partita_iva'));
Route::any('/winfatt/import_clienti', 'WinfattController@import_clienti');
Route::post('/winfatt/import_fornitori', [App\Http\Controllers\WinfattController::class, 'import_fornitori']);

Route::post('produzione/carica_allegato/{id}', 'ProduzioneController@carica_allegato');
Route::get('produzione/elimina_allegato/{id}', 'ProduzioneController@elimina_allegato');
Route::post('utente/commesse/{id_commessa}/attivita/{id_attivita}/carica_allegato', 'UtenteController@carica_allegato_attivita');
Route::get('utente/commesse/{id_commessa}/attivita/{id_attivita}/elimina_allegato/{id}', 'UtenteController@elimina_allegato_attivita');