<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use PHPMailer\PHPMailer\PHPMailer;
use Socialite;


class HomeController extends Controller
{

    public function index()
    {


        return View::make('homepage');

    }

    public function landing(Request $request)
    {
        $dati = $request->all();


        if (isset($dati['send_email'])) {
            unset($dati['send_email']);
            
            $nome = explode(' ', $dati['nome_cognome'], 2);

            if (sizeof($nome) > 1) {
                $cognome = $nome[1];
                $nome = $nome[0];
            } else {
                $cognome = '';
                $nome = $nome[0];
            }

            $api_key = 'b6489f646bc8723ca35bfd6db10ba44f-us21';
            $list_id = 'dbd4f1a36a';

            $mailchimp = new \MailchimpMarketing\ApiClient();

            $mailchimp->setConfig([
                'apiKey' => $api_key,
                'server' => 'us21'
            ]);

            try {
                $subscriber = $mailchimp->lists->addListMember($list_id, [
                    'email_address' => $dati['email'],
                    'status' => 'subscribed',
                    'merge_fields' => [
                        'FNAME' => $nome,
                        'LNAME' => $cognome,
                        'PHONE' => (isset($dati['numero']))? $dati['numero']:'',
                    ]
                ]);
            } catch (\Exception $exception) {
                unset($dati["send_mail"]);
                unset($dati["nome_cognome"]);
                unset($dati["email"]);
                unset($dati["number"]);
                unset($dati["message"]);
                $nome = '';     
                $cognome ='';
                echo '<script>alert("Registrazione non avvenuta. Ci scusiamo per il disagio.")</script>';
                return View::make('landing');
            }
            unset($dati["send_mail"]);
            unset($dati["nome_cognome"]);
            unset($dati["email"]);
            unset($dati["number"]);
            unset($dati["message"]);
            $nome = '';
            $cognome ='';
            echo '<script>alert("Registrazione avvenuta con successo.")</script>';
        }
        return View::make('landing');

    }

    public function privacy(Request $request)
    {
        return View::make('privacy');

    }

    public function landing_color(Request $request)
    {
        return View::make('landing_color');

    }

    public function landing_mobile(Request $request)
    {
        return View::make('landing_mobile');

    }

}
