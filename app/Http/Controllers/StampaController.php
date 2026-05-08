<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;
use App\Exports\PreventivoExport;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;



class StampaController extends Controller{



    public function documento($id_dotes){

        $this->is_loggato();
        $utente = session('utente');

        $dotes = DB::select('SELECT * from dotes where id = '.$id_dotes.' and id_azienda = '.$utente->id_azienda);
        if(sizeof($dotes) > 0){
            $dotes = $dotes[0];
            $do = DB::select('SELECT * from do where cd_do = "'.$dotes->cd_do.'" and id_azienda = '.$utente->id_azienda);
            if(sizeof($do) > 0){
                $do = $do[0];
                $dorig = DB::select('SELECT * from dorig where id_dotes ='.$dotes->id);
                $azienda = DB::select('SELECT * from aziende where id ='.$dotes->id_azienda);

                // Recupera le scadenze
                $scadenze = DB::table('scadenziario')
                    ->where('id_dotes', $id_dotes)
                    ->where('id_azienda', $utente->id_azienda)
                    ->orderBy('data_scadenza', 'asc')
                    ->get();

                if(sizeof($azienda) > 0){
                    $azienda = $azienda[0];
                    $logo_path = self::trovaLogo($azienda);
                    $html = View::make('stampa.documento',compact('dotes','dorig','do','utente','azienda','scadenze','logo_path'));
                    $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'mode' => 'utf-8', 'margin_left' => 5, 'margin_right' => 5, 'margin_top' => 5, 'margin_bottom' => 5, 'margin_header' => 0, 'margin_footer' => 0]);
                    $mpdf->SetTitle($do->descrizione.' N. '.$dotes->numero_doc.' del '.$dotes->data_doc);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output('stampa_documento' . $id_dotes . '.pdf', 'I');
                }


            }
        }



    }


    public static function trovaLogo($azienda)
    {
        if (!empty($azienda->immagine)) {
            // Cerca il file in varie posizioni
            $paths = [
                public_path($azienda->immagine),
                base_path($azienda->immagine),
                base_path('public/' . $azienda->immagine),
            ];
            foreach ($paths as $p) {
                if (file_exists($p)) return $p;
            }

            // Se non trovato localmente, scarica temporaneamente dall'URL
            $url = url($azienda->immagine);
            try {
                $contenuto = @file_get_contents($url);
                if ($contenuto) {
                    $ext = pathinfo($azienda->immagine, PATHINFO_EXTENSION) ?: 'png';
                    $tmp = sys_get_temp_dir() . '/logo_azienda_' . $azienda->id . '.' . $ext;
                    file_put_contents($tmp, $contenuto);
                    return $tmp;
                }
            } catch (\Exception $e) {}
        }
        return null;
    }

    public function is_loggato(){

        if (!session()->has('utente')) return Redirect::to('admin/login')->send();

    }

    public function dettaglio_odl($id_odl)
    {
        $this->is_loggato();
        $utente = session('utente');

        $odl = DB::select('SELECT o.*, a.titolo as articolo, a.codice_articolo, c.ragione_sociale as cliente_ragione_sociale FROM odl o LEFT JOIN articoli a ON a.id = o.id_articolo LEFT JOIN dotes d ON d.id = o.id_dotes LEFT JOIN clienti c ON c.id = d.id_cliente WHERE o.id = ? AND o.id_azienda = ?', [$id_odl, $utente->id_azienda]);
        if (sizeof($odl) == 0) return redirect()->back();
        $odl = $odl[0];

        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        $odl_righe = DB::select('SELECT o.*, f.descrizione as nome_fase FROM odl_righe o LEFT JOIN fasi f ON f.id = o.id_fase WHERE o.id_odl = ? AND o.id_azienda = ?', [$id_odl, $utente->id_azienda]);

        $articolo = DB::table('articoli')->where('id', $odl->id_articolo)->where('id_azienda', $utente->id_azienda)->first();

        $operatori = DB::select('SELECT * FROM utenti WHERE id_tipologia = 3 AND id_azienda = ?', [$utente->id_azienda]);

        // Materiali per ogni fase
        foreach ($odl_righe as &$riga) {
            $riga->materiali = DB::select('
                SELECT a.titolo, a.id, a.tipologia, a.um, db.qta
                FROM articoli a
                LEFT JOIN distinta_base db ON db.id_materiale = a.id
                WHERE db.id_articolo = ? AND db.id_fase_articolo = ? AND a.id_azienda = ?',
                [$articolo->id, $riga->id_fase, $utente->id_azienda]
            );

            $riga->allegati = DB::table('odl_righe_allegati')
                ->where('id_odl_riga', $riga->id)
                ->where('id_azienda', $utente->id_azienda)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        unset($riga);

        // Semilavorati
        $tabella_fornitori_join = ($utente->id_azienda == 14) ? 'clienti' : 'fornitori';
        $odl_semilavorati = DB::select('
            SELECT os.*, a.titolo, a.codice_articolo, f.descrizione as nome_fase,
                   u.nome as operatore_nome, u.cognome as operatore_cognome,
                   fo.ragione_sociale as fornitore_nome
            FROM odl_semilavorati os
            JOIN articoli a ON a.id = os.id_articolo
            LEFT JOIN fasi f ON f.id = os.id_fase
            LEFT JOIN utenti u ON u.id = os.id_operatore
            LEFT JOIN ' . $tabella_fornitori_join . ' fo ON fo.id = os.id_fornitore
            WHERE os.id_odl = ? AND os.id_azienda = ?
            ORDER BY os.id_fase ASC, a.titolo ASC',
            [$id_odl, $utente->id_azienda]
        );

        foreach ($odl_semilavorati as $semi) {
            $semi->fasi = DB::select('
                SELECT f.descrizione FROM fasi_articoli fa JOIN fasi f ON f.id = fa.id_fase
                WHERE fa.id_articolo = ? AND fa.id_azienda = ? ORDER BY fa.id ASC',
                [$semi->id_articolo, $utente->id_azienda]
            );

            $semi->materiali = DB::select('
                SELECT a.titolo, a.codice_articolo, a.tipologia, a.um, db.qta
                FROM distinta_base db
                JOIN articoli a ON a.id = db.id_materiale
                WHERE db.id_articolo = ? AND db.id_azienda = ?
                ORDER BY a.tipologia ASC, a.titolo ASC',
                [$semi->id_articolo, $utente->id_azienda]
            );
        }

        // Fornitori per lavorazioni esterne
        $usaClientTable = ($utente->id_azienda == 14);
        $tabella_fornitori = $usaClientTable ? 'clienti' : 'fornitori';
        $fornitori_odl = DB::table($tabella_fornitori)->where('id_azienda', $utente->id_azienda)->where('id_tipologia', 1)->orderBy('ragione_sociale')->get();

        $logo_path = self::trovaLogo($azienda);
        $html = View::make('stampa.dettaglio_odl', compact('odl', 'odl_righe', 'articolo', 'operatori', 'odl_semilavorati', 'azienda', 'utente', 'logo_path', 'fornitori_odl'));
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'mode' => 'utf-8', 'margin_left' => 10, 'margin_right' => 10, 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->SetTitle('Dettaglio ODL ' . $odl->numero . ' - ' . $odl->codice_articolo);
        $mpdf->WriteHTML($html);
        $mpdf->Output('dettaglio_odl_' . $odl->numero . '.pdf', 'I');
    }

    public function tracciabilita($id_odl)
    {
        $odl = DB::select('SELECT * from odl where id =' . $id_odl);
        if (sizeof($odl) > 0) {

            $odl = $odl[0];
            $mgmov = DB::select('
                SELECT mgmov.*, articoli.titolo 
                FROM mgmov 
                JOIN articoli ON mgmov.id_articolo = articoli.id 
                WHERE mgmov.id_odl = ' . $id_odl
            );

            if (sizeof($mgmov) > 0) {


                $html = View::make('stampa.report_progetti', compact('mgmov','odl'));
                $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L', 'mode' => 'utf-8', 'margin_left' => 5, 'margin_right' => 5, 'margin_top' => 40, 'margin_bottom' => 10, 'margin_header' => 0, 'margin_footer' => 0]);
                $mpdf->SetTitle('Report Tracciabilita ODL ' . $id_odl);

                $mpdf->SetHTMLHeader('
                    <table width="100%" style="font-weight: bold;">
                        <tr>
                            <td width="33%">                
                                <img src="/logo_gestya.jpg" style="height:120px;">
                            </td>
                            <td width="66%" align="right" style="font-size:25px;">
                                Report Tracciabilita ODL ' . $id_odl . '<br>
                            </td>
                        </tr>
                    </table>
                    ');

                $mpdf->WriteHTML($html);
                $mpdf->Output('report_tracciabilita_' . $id_odl . '.pdf', 'I');


            }
        }

    }

    public function stampa_etichetta_odl($id_odl)
    {
        $utente = session('utente_produzione');

        // Recupera i dati dell'ODL
        $odl = DB::select('
        SELECT o.*, a.titolo as articolo, a.codice_articolo, o.lotto_produzione
        FROM odl o
        JOIN articoli a ON a.id = o.id_articolo
        WHERE o.id = ? AND o.stato = 2 and o.id_azienda = ?',
            [$id_odl, $utente->id_azienda]
        );

        if (empty($odl)) {
            return redirect()->back()->with('error', 'ODL non trovato o non completato');
        }

        $odl = $odl[0];

        // Cerca il barcode nel movimento di magazzino
        $movimento = DB::table('mgmov')
            ->where('id_odl', $id_odl)
            ->where('id_azienda', $utente->id_azienda)
            ->where('car', 1) // Movimento di carico
            ->orderBy('id', 'desc') // Prendiamo il più recente
            ->first();

        // Se esiste un movimento con barcode, usa quello
        if ($movimento && !empty($movimento->barcode)) {
            $barcode_value = $movimento->barcode;
        } else {
            // Altrimenti usa il lotto di produzione come barcode
            $barcode_value = $odl->lotto_produzione;
        }

        // Genera il barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($barcode_value, $generator::TYPE_CODE_128));

        // Crea il PDF con mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [104, 159],
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5
        ]);

        $html = View::make('stampa.etichetta', compact('odl', 'barcode'));
        $mpdf->WriteHTML($html);

        // Output del PDF
        return $mpdf->Output('Etichetta_ODL_' . $odl->numero . '.pdf', 'I');
    }

}


