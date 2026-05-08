<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Webklex\IMAP\Facades\Client;


/**
 * Controller principale del webticket
 * Class HomeController
 * @package App\Http\Controllers
 */
class SdiController extends Controller
{

    public static function scarica_esiti($ore){

        $oClient = Client::account('default');
        $oClient->connect();

        $oFolder = $oClient->getFolder('INBOX');
        $aMessage = $oFolder->query()->since(now()->subHours($ore))->get();

        foreach ($aMessage as $chiave => $msg) {
            SdiController::scarica_messaggio($msg, 'orr@pec.officinariparazionirotabili.com');
        }

    }


    public static function scarica_messaggio($msg,$casella){

        $oggetto = $msg->getSubject();
        
        echo $oggetto;

        if(strpos($oggetto,'Ricevuta di consegna') !== false) {

            echo 'ciao';
            echo $oggetto.'<br>';
            echo $msg->getTextBody() . '<br>';

        }
    }

}


?>