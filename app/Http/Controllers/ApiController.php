<?php

namespace App\Http\Controllers;

use App\Imports\TariffeImport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PHPMailer\PHPMailer\PHPMailer;


class ApiController extends Controller
{
    public function send_explorer_config_filter(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {

            $filtro_area = '';
            $filtro_data = '';
            $filtro_budget = '';
            $filtro_luogo = '';
            $filtro_gruppo = '';
            $filtro_tipo_viaggio = '';
            $filtro_pernottamento = '';
            $filtro_max_partecipanti = '';

            foreach ($dati as $d) {
                if ($d['id'] == 1) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_data == '')
                            $filtro_data .= 'and (data_andata  =  STR_TO_DATE(\'' . $r . '\',\'%d-%m-%Y\') or data_andata = STR_TO_DATE(\'' . $r . '\' + INTERVAL 1 DAY,\'%d-%m-%Y\') or data_andata = STR_TO_DATE(\'' . $r . '\' - INTERVAL 1 DAY,\'%d-%m-%Y\')) and ';
                        else
                            $filtro_data .= '(data_ritorno  =  STR_TO_DATE(\'' . $r . '\',\'%d-%m-%Y\') or data_ritorno = STR_TO_DATE(\'' . $r . '\' + INTERVAL 1 DAY,\'%d-%m-%Y\') or data_ritorno = STR_TO_DATE(\'' . $r . '\' - INTERVAL 1 DAY,\'%d-%m-%Y\'))';
                    }
                }
                if ($d['id'] == 2) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_luogo == '')
                            $filtro_luogo .= 'and (luogo = \'' . $r['description'] . '\' or';
                        else
                            $filtro_luogo .= ' luogo = \'' . $r['description'] . '\' or';
                    }
                }
                if ($d['id'] == 3) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_area == '')
                            $filtro_area .= 'and (area_geografica = \'' . $r['description'] . '\' or';
                        else
                            $filtro_area .= ' area_geografica = \'' . $r['description'] . '\' or';
                    }
                }
                if ($d['id'] == 4) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_budget == '')
                            $filtro_budget .= 'and (budget = \'' . $r['description'] . '\' or';
                        else
                            $filtro_budget .= ' budget = \'' . $r['description'] . '\' or';
                    }
                }

                if ($d['id'] == 5) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_pernottamento == '')
                            $filtro_pernottamento .= 'and (pernottamento = \'' . $r['description'] . '\' or';
                        else
                            $filtro_pernottamento .= ' pernottamento = \'' . $r['description'] . '\' or';
                    }
                }

                if ($d['id'] == 6) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_max_partecipanti == '')
                            $filtro_max_partecipanti .= 'and (max_partecipanti = \'' . $r . '\')';/*
                        else
                            $filtro_max_partecipanti .= ' max_partecipanti = \'' . $r['description'] . '\' or';*/
                    }
                }
                if ($d['id'] == 7) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_gruppo == '')
                            $filtro_gruppo .= 'and (gruppo = \'' . $r['description'] . '\' or';
                        else
                            $filtro_gruppo .= ' gruppo = \'' . $r['description'] . '\' or';
                    }
                }
                if ($d['id'] == 8) {
                    foreach ($d['content'] as $r) {
                        if ($filtro_tipo_viaggio == '')
                            $filtro_tipo_viaggio .= 'and (tipo_viaggio = \'' . $r['description'] . '\' or';
                        else
                            $filtro_tipo_viaggio .= ' tipo_viaggio = \'' . $r['description'] . '\' or';
                    }
                }
            }

            $filtro_area = substr($filtro_area, 0, strlen($filtro_area) - 3);
            $filtro_luogo = substr($filtro_luogo, 0, strlen($filtro_luogo) - 3);
            $filtro_budget = substr($filtro_budget, 0, strlen($filtro_budget) - 3);
            $filtro_tipo_viaggio = substr($filtro_tipo_viaggio, 0, strlen($filtro_tipo_viaggio) - 3);
            $filtro_gruppo = substr($filtro_gruppo, 0, strlen($filtro_gruppo) - 3);
            $filtro_pernottamento = substr($filtro_pernottamento, 0, strlen($filtro_pernottamento) - 3);


            $filtro_area = $filtro_area . ')';
            $filtro_luogo = $filtro_luogo . ')';
            $filtro_budget = $filtro_budget . ')';
            $filtro_tipo_viaggio = $filtro_tipo_viaggio . ')';
            $filtro_gruppo = $filtro_gruppo . ')';
            $filtro_pernottamento = $filtro_pernottamento . ')';

            $filtro = $filtro_data . ' ' . $filtro_area . ' ' . $filtro_luogo . ' ' . $filtro_budget . ' ' . $filtro_tipo_viaggio . ' ' . $filtro_max_partecipanti . ' ' . $filtro_gruppo . ' ' . $filtro_pernottamento;

            $viaggi = DB::SELECT('select * from viaggi left join info_viaggio on viaggi.id = info_viaggio.id_viaggio WHERE 1 = 1 ' . $filtro);

            if (sizeof($viaggi) > 0)
                return $viaggi;
            else
                return response('{"Alert": "Nessun Viaggio Trovato."}', 200);

        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function get_explorer_config_filter_explorer(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $filtri = DB::SELECT('SELECT f.id,f.title,f.id_type,p.type  FROM filtri_viaggio f left join tipo_preferenza p on p.id = f.id_type');
            $oggetti = DB::SELECT('SELECT * FROM oggetti_filtri_viaggio');
            $lingue = DB::SELECT('SELECT * FROM lingua');
            foreach ($filtri as $f) {
                $f->oggetti = array();
                if ($f->id_type == 7) {
                    foreach ($lingue as $l) {
                        array_push($f->oggetti, $l);
                    }
                } else {
                    foreach ($oggetti as $o) {
                        if ($f->id == $o->id_filtro)
                            array_push($f->oggetti, $o);
                    }
                }
            }
            return json_encode($filtri);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function get_explorer(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $viaggio = DB::SELECT('SELECT * FROM viaggi v WHERE (SELECT COUNT(id_utente) FROM partecipanti_viaggio WHERE id_viaggio = v.id) < v.max_partecipanti limit 10');
            if (sizeof($viaggio) > 0) {
                $viaggi = '';
                foreach ($viaggio as $v) {
                    $viaggi .= $v->id . ',';
                }
                $viaggi = substr($viaggi, 0, -1);
                $partecipanti_viaggio = DB::SELECT('SELECT *,if(partecipanti_viaggio.ruolo = \'CREATORE\',true,false) as owner FROM partecipanti_viaggio LEFT JOIN info_utente i ON i.id_utente = partecipanti_viaggio.id_utente  where id_viaggio in (' . $viaggi . ')');
                foreach ($viaggio as $v) {
                    $count_partecipanti = 0;
                    $v->partecipants = array();
                    $v->compatibility = 10;
                    $v->days = floor((strtotime($v->data_ritorno) - strtotime($v->data_andata)) / 86400);
                    foreach ($partecipanti_viaggio as $p) {
                        if ($p->id_viaggio == $v->id) {
                            array_push($v->partecipants, array('id' => $p->id, 'coordinated' => $p->owner, 'img' => $p->img));
                            $count_partecipanti++;
                        }
                        $v->disponibili = $v->max_partecipanti - $count_partecipanti;
                    }
                }
                return response(json_encode($viaggio), 200);
            } else
                return response('{"Errore": "Nessun Viaggio Trovato"}', 404);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function get_explorer_filter_bydesc(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $filter = $dati['filter'];
            $viaggio = DB::SELECT('SELECT * FROM viaggi where descrizione like \'%' . $filter . '%\'');
            if (sizeof($viaggio) > 0) {
                $viaggi = '';
                foreach ($viaggio as $v) {
                    $viaggi .= $v->id . ',';
                }
                $viaggi = substr($viaggi, 0, -1);
                $partecipanti_viaggio = DB::SELECT('SELECT *,if(partecipanti_viaggio.ruolo = \'CREATORE\',true,false) as owner FROM partecipanti_viaggio LEFT JOIN info_utente i ON i.id_utente = partecipanti_viaggio.id_utente  where id_viaggio in (' . $viaggi . ')');
                foreach ($viaggio as $v) {
                    $count_partecipanti = 0;
                    $v->partecipants = array();
                    $v->compatibility = 10;
                    $v->days = floor((strtotime($v->data_ritorno) - strtotime($v->data_andata)) / 86400);
                    foreach ($partecipanti_viaggio as $p) {
                        if ($p->id_viaggio == $v->id) {
                            array_push($v->partecipants, array('id' => $p->id, 'coordinated' => $p->owner, 'img' => $p->img));
                            $count_partecipanti++;
                        }
                        $v->disponibili = $v->max_partecipanti - $count_partecipanti;
                    }
                }
                return response(json_encode($viaggio), 200);
            } else
                return response('{"Errore": "Nessun Viaggio Trovato"}', 404);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function tutorial(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $id_tipologia = DB::SELECT('SELECT id_tipologia as id FROM sessione left join utenti on sessione.id_utente = utenti.id where token = \'' . $request->header('token') . '\'')[0]->id;
            $tutorial = DB::SELECT('SELECT descrizione as description, img FROM tutorial where id_tipologia = ' . $id_tipologia);
            return json_encode($tutorial);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function preferenze(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $preferenze = DB::SELECT('SELECT p.id,p.title,t.type, \'\' as content FROM preferenza p left join tipo_preferenza  t on p.id_type = t.id ');
            $oggetti = DB::SELECT('SELECT id,img,content,id_preferenza from oggetti_preferenza');
            foreach ($preferenze as $p) {
                $p->content = [];
                $i = 0;
                foreach ($oggetti as $o) {
                    if ($p->id == $o->id_preferenza) {
                        if ($p->type == 'range') {
                            if ($i == 0)
                                array_push($p->content, array('id' => $o->id, 'min' => $o->content));
                            if ($i == 1)
                                array_push($p->content, array('id' => $o->id, 'max' => $o->content));
                            $i++;
                        }
                        if ($p->type == 'select')
                            array_push($p->content, array('id' => $o->id, 'content' => $o->content));
                        if ($p->type == 'checkbox')
                            array_push($p->content, array('id' => $o->id, 'content' => $o->content));
                        if ($p->type == 'calendar')
                            array_push($p->content, array('id' => $o->id, 'start' => date('Y-m-d', strtotime('now')), 'end' => date('Y-m-d', strtotime('+ ' . $o->content . ' day'))));
                        if ($p->type == 'iconSelect')
                            array_push($p->content, array('id' => $o->id, 'img' => $o->img));
                    }
                }
            }
            return json_encode($preferenze);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function anteprima_utente(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_utente = $dati["id"];
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $utente = DB::SELECT('SELECT * FROM utenti u left join info_utente i on u.id = i.id_utente  where u.id = ' . $id_utente);
            if (sizeof($utente) > 0) {
                $follow = 0;

                $id_follower = DB::SELECT('SELECT * FROM utenti LEFT JOIN sessione on sessione.id_utente = utenti.id where sessione.token =\'' . $request->header('token') . '\' ');
                $id_follower = $id_follower[0]->id_utente;

                $check = DB::SELECT('SELECT * from follow where id_utente = \'' . $id_utente . '\' and id_follower = \'' . $id_follower . '\' ');

                if (sizeof($check) > 0) {
                    $follow = $check[0]->follow;
                }

                $count_followers = DB::SELECT('SELECT count(*) as count from follow where id_utente = \'' . $id_utente . '\' and follow = 1 ');
                $count_following = DB::SELECT('SELECT count(*) as count from follow where id_follower = \'' . $id_utente . '\' and follow = 1 ');

                $json = array(
                    "id" => $utente[0]->id_utente,
                    "img" => $utente[0]->img,
                    "followers" => $count_followers[0]->count,
                    "following" => $count_following[0]->count,
                    "title" => $utente[0]->nome . ' ' . $utente[0]->cognome,
                    "reputation" => 2,
                    "maxReputation" => 5,
                    "follow" => ($follow == 1) ? true : false,
                );
                return json_encode($json);
            } else {
                return response('{"Errore": "Utente inesistente."}', 400);
            }
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public function dettaglio_utente(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_utente = $dati["id"];
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $utente = DB::SELECT('SELECT * FROM utenti u left join info_utente i on u.id = i.id_utente  where u.id = ' . $id_utente);
            if (sizeof($utente) > 0) {
                $follow = 0;

                $id_follower = DB::SELECT('SELECT * FROM utenti LEFT JOIN sessione on sessione.id_utente = utenti.id where sessione.token =\'' . $request->header('token') . '\' ');
                $id_follower = $id_follower[0]->id_utente;

                $check = DB::SELECT('SELECT * from follow where id_utente = \'' . $id_utente . '\' and id_follower = \'' . $id_follower . '\' ');

                if (sizeof($check) > 0) {
                    $follow = $check[0]->follow;
                }

                $viaggi = DB::SELECT('SELECT * FROM partecipanti_viaggio p left join viaggi v on v.id = p.id_viaggio where p.id_utente =\'' . $utente[0]->id_utente . '\' ');
                if (sizeof($viaggi) > 0) {
                    $viaggi_crono = [];
                    $last_i = 0;
                    foreach ($viaggi as $v) {
                        if ($v->hide_notifica != 1) {
                            $sub_json = array(
                                "id" => $v->id,
                                "open" => $v->stato,
                                "dest" => $v->localita,
                                "go" => $v->data_andata,
                                "away" => $v->data_ritorno,
                                "img" => $v->img,
                            );
                            $viaggi_crono[$last_i] = $sub_json;
                            $last_i++;
                        }

                    }
                }
                $count_followers = DB::SELECT('SELECT count(*) as count from follow where id_utente = \'' . $id_utente . '\' and follow = 1 ');
                $count_following = DB::SELECT('SELECT count(*) as count from follow where id_follower = \'' . $id_utente . '\' and follow = 1 ');

                $viaggi_completati = DB::SELECT('SELECT count(*) as count from partecipanti_viaggio where id_utente = ' . $id_utente . ' and abbandonato = 0')[0]->count;
                $viaggi_abbandonati = DB::SELECT('SELECT count(*) as count from partecipanti_viaggio where id_utente = ' . $id_utente . ' and abbandonato = 1')[0]->count;

                $personalita = DB::SELECT('SELECT o.content from preferenze_utenti p left join oggetti_preferenza o on p.oggetto_preferenza = o.id where p.id_utente = ' . $id_utente . ' and p.id_preferenza = 12');
                $hobby = DB::SELECT('SELECT o.content from preferenze_utenti p left join oggetti_preferenza o on p.oggetto_preferenza = o.id where p.id_utente = ' . $id_utente . ' and p.id_preferenza = 13');

                $json = array(
                    "id" => $utente[0]->id_utente,
                    "img" => $utente[0]->img,
                    "followers" => $count_followers[0]->count,
                    "following" => $count_following[0]->count,
                    "title" => $utente[0]->nome . ' ' . $utente[0]->cognome,
                    "reputation" => 2,
                    "maxReputation" => 5,
                    "follow" => ($follow == 1) ? true : false,
                    "profile" => array(
                        "travelerCompleted" => $viaggi_completati,
                        "travelerFailed" => $viaggi_abbandonati,
                        "biography" => $utente[0]->biografia,
                        "another_info" => array(
                            "birthday" => $utente[0]->data_nascita,
                            "origin" => $utente[0]->citta,
                            "life" => $utente[0]->residenza,
                            "languages" => [
                            ],
                            "personality" => $personalita,
                            "Hobbies" => $hobby,
                            "travelers" => $viaggi_crono,
                            "love" => ($utente[0]->stato_sentimentale != '') ? $utente[0]->stato_sentimentale : 'undefined',
                        )
                    )
                );
                return json_encode($json);
            } else {
                return response('{"Errore": "Utente inesistente."}', 400);
            }
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public
    function follower_following(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);

        $id_utente = $dati["id"];

        $is_online = $this->is_online($request->header('token'));

        if ($is_online == 1) {

            $utente = DB::SELECT('SELECT * FROM utenti u left join info_utente i on u.id = i.id_utente  where u.id = ' . $id_utente);

            if (sizeof($utente) > 0) {

                $followers = DB::SELECT('SELECT f.id_utente as id,if(f.follow = 1,true,false) as follow,i.img,concat(i.nome,\' \',i.cognome) as title from follow f left join info_utente i on i.id_utente = \'' . $id_utente . '\' where f.id_utente = \'' . $id_utente . '\' and f.follow = 1 ');
                $following = DB::SELECT('SELECT f.id_utente as id,if(f.follow = 1,true,false) as follow,i.img,concat(i.nome,\' \',i.cognome) as title from follow f left join info_utente i on i.id_utente = \'' . $id_utente . '\' where f.id_follower = \'' . $id_utente . '\' and f.follow = 1 ');

                foreach ($followers as $f) {
                    $f->follow = (bool)$f->follow;
                }

                foreach ($following as $f1) {
                    $f1->follow = (bool)$f1->follow;
                }

                $json = array(
                    "followers" => $followers,
                    "following" => $following,
                );

                return json_encode($json);

            } else
                return response('{"Errore": "Utente inesistente."}', 400);

        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public
    function recensioni(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);

        $id_utente = $dati["id"];

        $is_online = $this->is_online($request->header('token'));

        if ($is_online == 1) {

            $utente = DB::SELECT('SELECT * FROM utenti u left join info_utente i on u.id = i.id_utente  where u.id = ' . $id_utente);

            if (sizeof($utente) > 0) {

                $recensioni = DB::SELECT('SELECT r.*,concat(i.nome,\' \',i.cognome) as nome_utente,i.img as img_utente,concat(i2.nome,\' \',i2.cognome) as nome_utente_recensito,i2.img as img_utente_recensito  FROM recensioni r LEFT JOIN info_utente i ON r.id_utente = i.id_utente LEFT JOIN info_utente i2 ON r.id_utente_recensito = i2.id_utente where r.id_utente_recensito = \''.$id_utente.'\'');

                return json_encode($recensioni);

            } else
                return response('{"Errore": "Utente inesistente."}', 400);

        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public
    function follow(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_utente = $dati["id"];
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            if ($dati["follow"] == true) {
                $dati["follow"] = 1;
            }
            if ($dati["follow"] == false) {
                $dati["follow"] = 0;
            }
            $id_follower = DB::SELECT('SELECT * FROM utenti LEFT JOIN sessione on sessione.id_utente = utenti.id where sessione.token =\'' . $request->header('token') . '\' ');
            $id_follower = $id_follower[0]->id_utente;
            $check = DB::SELECT('SELECT * from follow where id_utente = \'' . $id_utente . '\' and id_follower = \'' . $id_follower . '\' ');
            if (sizeof($check) > 0) {
                DB::SELECT('UPDATE follow set follow = \'' . $dati["follow"] . '\' where id_utente = \'' . $id_utente . '\' and id_follower = \'' . $id_follower . '\' ');
                if ($dati["follow"] == 1) {
                    return response('{"success": "Utente seguito."}', 200);
                }
                if ($dati["follow"] == 0) {
                    return response('{"success": "Utente unfollowato."}', 200);
                }
            } else {
                DB::SELECT('INSERT INTO follow (id_utente,follow,id_follower) VALUES (\'' . $id_utente . '\',1,\'' . $id_follower . '\')');
                return response('{"success": "Utente seguito."}', 200);
            }
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public
    function hide_notifica(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_viaggio = $dati["id_viaggio"];
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $utente = DB::SELECT('SELECT * FROM utenti LEFT JOIN sessione on sessione.id_utente = utenti.id where sessione.token =\'' . $request->header('token') . '\' ');
            $id_utente = $utente[0]->id_utente;
            $check = DB::SELECT('SELECT * FROM partecipanti_viaggio where id_viaggio = \'' . $id_viaggio . '\' and id_utente = \'' . $id_utente . '\' ');
            if (sizeof($check) > 0) {
                DB::SELECT('UPDATE partecipanti_viaggio set hide_notifica = 1 where id_viaggio = \'' . $id_viaggio . '\' and id_utente = \'' . $id_utente . '\' ');
                return response('{"success": "Notifica eliminata correttamente."}', 200);
            } else
                return response('{"Errore": "Nessuna Notifica Trovata"}', 400);
        } else
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
    }

    public
    function viaggio(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_viaggio = $dati["id_viaggio"];

        $viaggio = DB::SELECT('SELECT * FROM viaggi v left join info_viaggio i on i.id_viaggio = v.id  where v.id = ' . $id_viaggio);
        if (sizeof($viaggio) > 0) {
            $id_utente = DB::SELECT('SELECT id_utente from sessione where token = \'' . $request->header('token') . '\'');
            if (sizeof($id_utente) > 0) {
                $id_utente = $id_utente[0]->id_utente;
                $v = $viaggio[0];
                $cond = ($v->lingue) ? '\'' . str_replace(' ', '', str_replace(',', '\',\'', $v->lingue)) . '\'' : '""';
                $lingue = DB::SELECT('SELECT codice FROM lingua where lingua in (' . $cond . ')');
                $l = array();
                foreach ($lingue as $r)
                    array_push($l, $r->codice);
                $lingue = $l;
                $partecipanti = DB::SELECT('SELECT COALESCE((SELECT follow from follow where id_utente = \'' . $id_utente . '\' and id_follower = u.id),0) as follow,i.nome as name,i.img,i.cognome as surname,i.id_utente as id FROM partecipanti_viaggio left join utenti u on u.id = partecipanti_viaggio.id_utente left join info_utente i on u.id = i.id_utente  where id_viaggio = ' . $id_viaggio);
                foreach ($partecipanti as $p) {
                    $p->follow = (bool)$p->follow;
                }

                $immagini_viaggio = DB::SELECT('SELECT link from immagini_viaggio where id_viaggio = ' . $id_viaggio);
                $a = [];
                foreach ($immagini_viaggio as $i)
                    array_push($a, $i->link);
                $json = array(
                    "id" => $id_viaggio,
                    "owner" => false,
                    "listOfImg" => $a,
                    "title" => $v->localita,
                    "to" => $v->arrivo,
                    "from" => $v->partenza,
                    "days" => floor((strtotime($v->data_ritorno) - strtotime($v->data_andata)) / 86400),
                    "date" => $v->data_ritorno,
                    "subtitle" => $v->descrizione,
                    "compatibility" => 10,
                    "available" => intval(intval($v->max_partecipanti) - sizeof($partecipanti)),
                    "status" => ($v->stato == 1) ? true : false,
                    "detailSheet" => array(
                        "description" => $v->descrizione,
                        "other" => array(
                            "type" => $v->tipo_viaggio,
                            "maxBudget" => $v->budget,
                            "overnight" => $v->pernottamento,
                            "languages" => $lingue,
                            "minRangeYear" => $v->eta_min,
                            "maxRangeYear" => $v->eta_max,
                            "typeOfGroup" => $v->gruppo,
                        ),
                    ),
                    "partecipanti" => $partecipanti,
                );
                return json_encode($json, JSON_NUMERIC_CHECK);
            } else
                return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
        }
    }

    public
    function viaggio_roadmap(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $id_viaggio = $dati["id_viaggio"];
        $viaggio = DB::SELECT('SELECT * FROM viaggi v left join info_viaggio i on i.id_viaggio = v.id  where v.id = ' . $id_viaggio);
        if (sizeof($viaggio) > 0) {
            $id_utente = DB::SELECT('SELECT id_utente from sessione where token = \'' . $request->header('token') . '\'');
            if (sizeof($id_utente) > 0) {
                $id_utente = $id_utente[0]->id_utente;
                $v = $viaggio[0];
                $cond = ($v->lingue) ? '\'' . str_replace(' ', '', str_replace(',', '\',\'', $v->lingue)) . '\'' : '""';
                $lingue = DB::SELECT('SELECT codice FROM lingua where lingua in (' . $cond . ')');
                $l = array();
                foreach ($lingue as $r)
                    array_push($l, $r->codice);
                $lingue = $l;
                $partecipanti = DB::SELECT('SELECT COALESCE((SELECT if(follow = 1,true,false) from follow where id_utente = \'' . $id_utente . '\' and id_follower = u.id),\'false\')  as follow,i.nome as name,i.cognome as surname,i.img,i.id_utente as id FROM partecipanti_viaggio left join utenti u on u.id = partecipanti_viaggio.id_utente left join info_utente i on u.id = i.id_utente  where id_viaggio = ' . $id_viaggio);
                foreach ($partecipanti as $p) {
                    $p->follow = (bool)$p->follow;
                }
                $roadmap = [];
                $content_roadmap = DB::SELECT('SELECT *,(select title from tipo_roadmap where tipo_roadmap.id = roadmap.id_tipo_roadmap) as tipo_roadmap FROM roadmap where id_viaggio = ' . $id_viaggio . ' Order by data_inizio asc');
                $date_roadmap = DB::SELECT('SELECT DISTINCT CONVERT(data_inizio,DATE) AS data_inizio FROM roadmap where id_viaggio = ' . $id_viaggio . ' order by data_inizio asc');
                foreach ($date_roadmap as $d) {
                    $sub_json = array("date" => [], 'movement' => []);
                    array_push($sub_json['date'], $d->data_inizio);
                    foreach ($content_roadmap as $r) {
                        if ($d->data_inizio == date('Y-m-d', strtotime($r->data_inizio))) {
                            array_push($sub_json['movement'], $r);
                        }
                    }
                    array_push($roadmap, $sub_json);
                }

                $info = array();
                $group_consigli = DB::SELECT('select title from consigli group by title');
                $consigli = DB::SELECT('select * from consigli');

                foreach ($group_consigli as $d) {
                    $sub_json1 = array("title" => [], 'link' => []);
                    array_push($sub_json1['title'], $d->title);
                    foreach ($consigli as $r) {
                        if ($d->title == $r->title) {
                            array_push($sub_json1['link'], $r->content);
                        }
                    }
                    array_push($info, $sub_json1);
                }

                $json = array(
                    "id" => $id_viaggio,
                    "owner" => false,
                    "listOfImg" => array(),
                    "title" => $v->localita,
                    "to" => $v->arrivo,
                    "from" => $v->partenza,
                    "days" => floor((strtotime($v->data_ritorno) - strtotime($v->data_andata)) / 86400),
                    "date" => $v->data_ritorno,
                    "subtitle" => $v->descrizione,
                    "compatibility" => 10,
                    "available" => intval(intval($v->max_partecipanti) - sizeof($partecipanti)),
                    "status" => ($v->stato == 1) ? true : false,
                    "detailSheet" => array(
                        "description" => $v->descrizione,
                        "other" => array(
                            "type" => $v->tipo_viaggio,
                            "maxBudget" => $v->budget,
                            "overnight" => $v->pernottamento,
                            "languages" => $lingue,
                            "minRangeYear" => $v->eta_min,
                            "maxRangeYear" => $v->eta_max,
                            "typeOfGroup" => $v->gruppo,
                        ),
                    ),
                    "partecipanti" => $partecipanti,
                    "roadmap" => array("info" => $info, "map" => $roadmap,),
                );
                return json_encode($json);
            } else
                return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
        }
    }

    public
    function cronologia_viaggi(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $utente = DB::SELECT('SELECT * FROM utenti LEFT JOIN sessione on sessione.id_utente = utenti.id where sessione.token =\'' . $request->header('token') . '\' ');
            $viaggi = DB::SELECT('SELECT * FROM partecipanti_viaggio p left join viaggi v on v.id = p.id_viaggio where p.id_utente =\'' . $utente[0]->id_utente . '\' ');
            if (sizeof($viaggi) > 0) {
                $last = [];
                $last_i = 0;
                $next = [];
                $next_i = 0;
                foreach ($viaggi as $v) {
                    if (strtotime(date('Y-m-d', strtotime($v->data_ritorno))) <= strtotime(date('Y-m-d', strtotime('now')))) {
                        if ($v->hide_notifica != 1) {
                            $sub_json = array(
                                "id" => $v->id,
                                "open" => $v->stato,
                                "dest" => $v->localita,
                                "go" => $v->data_andata,
                                "away" => $v->data_ritorno,
                                "img" => $v->img,
                            );
                            $last[$last_i] = $sub_json;
                            $last_i++;
                        }
                    } else {
                        $sub_json = array(
                            "id" => $v->id,
                            "open" => $v->stato,
                            "dest" => $v->localita,
                            "go" => $v->data_andata,
                            "away" => $v->data_ritorno,
                            "img" => $v->img,
                        );
                        $next[$next_i] = $sub_json;
                        $next_i++;

                    }
                }
                $json = array(
                    "next" => $next,
                    "last" => $last
                );
                return json_encode($json);
            } else {
                return array(
                    "next" => [],
                    "last" => []
                );
            }
            //   $partecipanti = DB::SELECT('SELECT if(partecipanti_viaggio.ruolo = \'CREATORE\',true,false) as owner,i.id,i.id_utente as id,id_viaggio,img FROM partecipanti_viaggio left join utenti u on u.id = partecipanti_viaggio.id_utente left join info_utente i on u.id = i.id_utente');
        } else {
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
        }
    }

    public
    function viaggi_disponibili(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        $is_online = $this->is_online($request->header('token'));
        if ($is_online == 1) {
            $viaggio = DB::SELECT('SELECT * FROM viaggi where stato = 1 ');
            $partecipanti = DB::SELECT('SELECT if(partecipanti_viaggio.ruolo = \'CREATORE\',true,false) as owner,i.id,i.id_utente as id,id_viaggio,img FROM partecipanti_viaggio left join utenti u on u.id = partecipanti_viaggio.id_utente left join info_utente i on u.id = i.id_utente ORDER BY owner desc');
            if (sizeof($viaggio) > 0) {
                $json = [];
                $i = 0;
                foreach ($viaggio as $v) {
                    $partecipanti_viaggio = [];
                    $count = 0;
                    foreach ($partecipanti as $p) {
                        if ($p->id_viaggio == $v->id) {
                            $partecipanti_viaggio[$count] = $p;
                            $count++;
                        }
                    }


                    $sub_json = array(
                        "id" => $v->id,
                        "owner" => false,
                        "img" => $v->img,
                        "title" => $v->localita,
                        "to" => $v->arrivo,
                        "from" => $v->partenza,
                        "days" => floor((strtotime($v->data_ritorno) - strtotime($v->data_andata)) / 86400),
                        "date" => $v->data_ritorno,
                        "subtitle" => $v->descrizione,
                        "compatibility" => 10,
                        "available" => intval(intval($v->max_partecipanti) - intval($count)),
                        "partecipanti" => $partecipanti_viaggio
                    );
                    $json[$i] = $sub_json;
                    $i++;
                }
                return json_encode($json);
            }
        } else {
            return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
        }
    }

    public
    function registra(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);

        $check_email = DB::SELECT('SELECT * FROM utenti where email = \'' . $dati['email'] . '\' ');

        if (sizeof($check_email) > 0) return response('{"error": "Email già utilizzata."}', 409); else {

            $id_tipo = DB::SELECT('SELECT * FROM tipologia where descrizione like  \'%' . $dati['tipologia'] . '%\'')[0]->id;

            $id = DB::table('utenti')->insertGetId([
                'nominativo' => $dati['nome'] . ' ' . $dati['cognome'],
                'email' => $dati['email'],
                'password' => $dati['password'],
                'abilitato' => 1,
                'id_tipologia' => $id_tipo,
            ]);

            $link = '';

            if (isset($dati['img'])) {
                if ($dati['img'] != '') {

                    $img = $dati['img'];

                    $file = 'immagini/dhonko_profilo_' . rand(0, 9999999999) . '.jpg';

                    $link = URL::asset($file);

                    $img = str_replace('data:image/png;base64,', '', $img);

                    $img = str_replace(' ', '+', $img);

                    $data = base64_decode($img);

                    $fp = fopen($file, "w+");

                    fwrite($fp, $data);

                    /*
                     Hexadecimal to image

                    $dati['img'] = $dati['img'];

                    $binary = pack("H*", str_replace('0x', '', $dati['img']));
                    file_put_contents($file, $binary);
                    $link = URL::asset($file);

                    */

                }
            }

            if ($id_tipo == 4)
                DB::table('info_utente')->insertGetId([
                    'id_utente' => $id,
                    'nome' => $dati['nome'],
                    'cognome' => $dati['cognome'],
                    'citta' => $dati['citta'],
                    'residenza' => $dati['residenza'],
                    'numero' => intval($dati['numero']),
                    'data_nascita' => date('Y-m-d', strtotime(str_replace('/', '-', $dati['data_nascita']))),
                    'img' => $link,
                ]);

            else

                DB::table('info_utente')->insertGetId([
                    'id_utente' => $id,
                    'nome' => (isset($dati['nome'])) ? $dati['nome'] : null,
                    'cognome' => (isset($dati['cognome'])) ? $dati['cognome'] : null,
                    'indirizzo' => (isset($dati['indirizzo'])) ? $dati['indirizzo'] : null,
                    'comune' => (isset($dati['comune'])) ? $dati['comune'] : null,
                    'provincia' => (isset($dati['provincia'])) ? $dati['provincia'] : null,
                    'nazione' => (isset($dati['nazione'])) ? $dati['nazione'] : null,
                    'codice_fiscale' => (isset($dati['codice_fiscale'])) ? $dati['codice_fiscale'] : null,
                    'partitaiva' => (isset($dati['partitaiva'])) ? $dati['partitaiva'] : null,
                    'numero' => (isset($dati['numero'])) ? intval($dati['numero']) : null,
                    'img' => $link,
                    'tipo_utente' => (isset($dati['tipo_utente'])) ? $dati['tipo_utente'] : null,
                ]);

            return response('{"success": "Cliente correttamente creato"}', 200);
        }
    }

    public
    function login(Request $request)
    {
        $dati = json_decode(file_get_contents('php://input'), true);
        //$this->is_online($token);
        if (isset($dati['email'])) {

            $utenti = DB::select('SELECT * from utenti where email = "' . htmlentities($dati['email'], 3, 'UTF-8' . '') . '" and password = "' . htmlentities($dati['password'], 3, 'UTF-8' . '') . '"');

            if (sizeof($utenti) > 0) {

                $utente = $utenti[0];

                $desc = DB::SELECT('SELECT * FROM tipologia where id  = \'' . $utente->id_tipologia . '\'')[0]->descrizione;

                if ($utente->abilitato == 1) {

                    $session = DB::SELECT('SELECT * FROM sessione where id_utente = ' . $utente->id);

                    if (sizeof($session) > 0) {

                        $token = $session[0]->token;
                        DB::SELECT('DELETE FROM sessione where token = \'' . $token . '\'');
                        $token = Str::random(50);
                        DB::SELECT('INSERT INTO sessione (id_utente,token, timeins) VALUES (' . $utente->id . ',\'' . $token . '\',\'' . date('Y-m-d H:i:s', strtotime('now')) . '\') ');

                        return response('{"token": "' . $token . '","tipologia":"' . $desc . '"}', 200);

                    } else {
                        $token = Str::random(50);
                        DB::SELECT('INSERT INTO sessione (id_utente,token, timeins) VALUES (' . $utente->id . ',\'' . $token . '\',\'' . date('Y-m-d H:i:s', strtotime('now')) . '\') ');
                        return response('{"token": "' . $token . '","tipologia":"' . $desc . '"}', 200);
                    }

                    // $this->is_online($token);

                } else {
                    return response('{"error": "Utente non abilitato."}', 401);
                }
            } else
                return response('{"error": "Username o Password sbagliata."}', 400);
        } else
            return response('{"error": "Dati non formattati correttamente."}', 404);
    }

    public
    function is_online($token)
    {
        $session = DB::SELECT('SELECT * FROM sessione where token = \'' . $token . '\'');
        if (sizeof($session) > 0) {
            if ($session[0]->timeins < date('Y-m-d H:i:s', strtotime('-8 hours'))) {
                return 1;
                //DB::SELECT('DELETE FROM sessione where token = \'' . $session[0]->token . '\'');
                //return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
            } else {
                $utente = DB::SELECT('SELECT utenti.*,tipologia.descrizione as descrizione_tip from utenti left join tipologia on tipologia.id = utenti.id_tipologia where utenti.id = \'' . $session[0]->id_utente . '\'')[0];
                return 1;
                //return response('{"token": "' . $token . '","tipologia":"' . $utente->descrizione_tip . '"}', 200);
            }
        } else {
            return 0;
            //  return response('{"Errore": "Il token non esiste. Rifare il login"}', 401);
        }
    }

    public
    function token(Request $request)
    {
        $token = $request->header('token');
        $session = DB::SELECT('SELECT * FROM sessione where token = \'' . $token . '\'');
        if (sizeof($session) > 0) {
            $utente = DB::SELECT('SELECT utenti.*,tipologia.descrizione as descrizione_tip from utenti left join tipologia on tipologia.id = utenti.id_tipologia where utenti.id = \'' . $session[0]->id_utente . '\'')[0];
            if ($session[0]->timeins < date('Y-m-d H:i:s', strtotime('-8 hours'))) {
                DB::SELECT('DELETE FROM sessione where token = \'' . $session[0]->token . '\'');
                return response('{"token": "' . $token . '","tipologia":"' . $utente->descrizione_tip . '"}', 200);
                //return response('{"Errore": "Il token non esiste più. Rifare il login"}', 401);
            } else {
                return response('{"token": "' . $token . '","tipologia":"' . $utente->descrizione_tip . '"}', 200);
            }
        } else {
            return response('{"Errore": "Il token non esiste. Rifare il login"}', 401);
        }
    }


    /*
        public function index(Request $request)
        {

            $this->is_loggato();
            $utente = session('utente');
            $page = 'index';

            return View::make('admin.index', compact('page', 'utente'));

        }
        public function preferenze(Request $request)
        {

            $this->is_loggato();
            $utente = session('utente');
            $page = 'preferenze';
            $preferenze = DB::SELECT('select *,(SELECT COUNT(*) FROM oggetti_preferenza where preferenza.id = oggetti_preferenza.id_preferenza) as count from preferenza');
            $oggetti = DB::SELECT('select oggetti_preferenza.* from preferenza LEFT JOIN oggetti_preferenza ON preferenza.id = oggetti_preferenza.id_preferenza');

            return View::make('admin.index', compact('page', 'utente','preferenze','oggetti'));

        }*/

    public
    function logout()
    {
        session()->flush();
        return Redirect::to('admin/login');
    }

    public
    function is_loggato()
    {
        if (!session()->has('utente')) return Redirect::to('admin/login')->send();
    }



    public static function chiudi_odl($id_odl, $id_dorig, $utente, $lotto_produzione = '') {
        $chiudi_odl = 1;
        $righe = DB::select('SELECT * from odl_righe where id_odl = '.$id_odl);
        foreach($righe as $r){
            if($r->completato == 0) {
                $chiudi_odl = 0;
                break;
            }
        }

        if($chiudi_odl == 1) {
            // Aggiorna l'ODL con il lotto di produzione e lo stato chiuso
            DB::update('update odl set lotto_produzione = "'.htmlentities($lotto_produzione, 3, 'UTF-8' . '').'", 
                    stato = 2, data_chiusura = NOW() 
                    where stato = 1 and id ='.$id_odl);

            $odl = DB::select('SELECT * from odl where id = '.$id_odl);
            if(sizeof($odl) > 0){
                $odl = $odl[0];

                // Genera il barcode per il movimento di magazzino
                $anno = date('Y');
                $numero_odl = str_pad($odl->numero, 6, '0', STR_PAD_LEFT);
                $data_produzione = date('ymd'); // formato AAMMGG
                $lotto = !empty($lotto_produzione) ? $lotto_produzione : str_pad($id_odl, 5, '0', STR_PAD_LEFT);
                $barcode_value = 'ODL' . $anno . $numero_odl . $data_produzione . $lotto;

                $insert['id_azienda'] = $odl->id_azienda;
                $insert['id_odl'] = $odl->id;
                $insert['car'] = 1;
                $insert['id_articolo'] = $odl->id_articolo;
                $insert['qta'] = $odl->qta;
                $insert['id_dorig'] = $odl->id_dorig;
                $insert['lotto'] = $lotto_produzione;
                $insert['scadenza_lotto'] = date('Y-m-d', strtotime('+2 years'));
                $insert['causale'] = 'Carico Chiusura ODL '.$odl->numero;
                $insert['barcode'] = $barcode_value; // Salviamo il barcode nel movimento di magazzino

                // Propaga la commessa dell'ODL ai movimenti di magazzino
                if(!empty($odl->id_commessa)) {
                    $insert['id_commessa'] = $odl->id_commessa;
                }

                // Carico ODL: 1) magazzino prodotti_finiti default, 2) fallback produzione=1
                $mg_prod = DB::select(
                    "SELECT * FROM mg
                     WHERE id_azienda = ?
                       AND ((tipologia = 'prodotti_finiti' AND is_default = 1) OR produzione = 1)
                     ORDER BY (tipologia = 'prodotti_finiti' AND is_default = 1) DESC, produzione DESC
                     LIMIT 1",
                    [$odl->id_azienda]
                );
                if(sizeof($mg_prod) > 0){
                    $insert['id_mg'] = $mg_prod[0]->id;
                }

                DB::table('mgmov')->insertGetId($insert);

                $m = new MagazzinoController();
                $m->ricalcolaGiacenze($odl->id_articolo);

                // Aggiorna stato_prod e qta_evadibile_prod nella riga dell'ordine
                if($id_dorig > 0) {
                    DB::table('dorig')
                        ->where('id', $id_dorig)
                        ->where('id_azienda', $utente->id_azienda)
                        ->update([
                            'stato_prod' => 2,
                            'qta_evadibile_prod' => 0
                        ]);
                }
            }
        }
    }

    public static function chudi_fase($id_riga,$quantita = 0,$note ='', $id_fase, $lotto, $scadenza_lotto, $utente, $id_odl, $id_dorig,$lotto_produzione = '', $qta_materiale_custom = null, $id_materiale_custom = null){


        if($quantita == 0) {
            DB::update('update odl_righe set qta_fatta = qta,qta_finale = qta_iniziale + qta, fine = NOW(),completato = 1 where id=' . $id_riga);
        } else  {

            $dati['qta_fatta'] = $quantita;
            $dati['qta_iniziale'] = 0;
            $dati['note'] = $note;
            $dati['completato'] = 1;
            DB::table('odl_righe')->where('id',$id_riga)->update($dati);
            DB::update('update odl_righe set fine = NOW() where id=' . $id_riga);
        }



        $righe = DB::table('odl_righe')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->get();

        $righeAll = DB::table('odl_righe')->where('id_odl', $id_odl)->where('id_azienda', $utente->id_azienda)->get();

        if (sizeof($righe) > 0) {
            $riga = $righe[0];

            $odl = DB::select('SELECT * from odl where id ='.$riga->id_odl);
            if (sizeof($odl) > 0) {
                $odl = $odl[0];

                // Recupera il materiale della distinta base SOLO per la fase attuale
                $materiale_fase = DB::table('distinta_base as db')
                    ->where('db.id_articolo', $odl->id_articolo)
                    ->where('db.id_fase_articolo', $riga->id_fase)  // Filtra per la fase corrente
                    ->select('db.*')
                    ->get();

                // Verifica se ci sono materiali per la fase corrente
                if ($materiale_fase->isNotEmpty()) {
                    foreach ($materiale_fase as $index => $materiale) {
                        $insert = [];
                        $insert['id_azienda'] = $odl->id_azienda;
                        $insert['id_odl'] = $odl->id;
                        $insert['id_riga_odl'] = $id_riga;
                        $insert['id_dorig'] = $odl->id_dorig;
                        $insert['sca'] = 1;
                        $insert['lotto'] = !empty($lotto[$index]) ? $lotto[$index] : null;
                        $insert['scadenza_lotto'] = !empty($scadenza_lotto[$index]) ? date('Y-m-d', strtotime($scadenza_lotto[$index])) : null;
                        $insert['id_articolo'] = $materiale->id_materiale;

                        // Se l'admin ha modificato manualmente la qta, usa quella; altrimenti calcola standard
                        $qta_da_scaricare = $materiale->qta * $odl->qta;
                        if (is_array($qta_materiale_custom) && is_array($id_materiale_custom)) {
                            foreach ($id_materiale_custom as $idx => $id_mat) {
                                if ($id_mat == $materiale->id_materiale && isset($qta_materiale_custom[$idx]) && $qta_materiale_custom[$idx] !== '' && $qta_materiale_custom[$idx] !== null) {
                                    $qta_da_scaricare = floatval($qta_materiale_custom[$idx]);
                                    break;
                                }
                            }
                        }
                        $insert['qta'] = $qta_da_scaricare * -1;

                        // Propaga la commessa dell'ODL ai movimenti di scarico
                        if(!empty($odl->id_commessa)) {
                            $insert['id_commessa'] = $odl->id_commessa;
                        }

                        // Cerca il magazzino dove il materiale ha giacenza con quel lotto
                        $mg_materiale = null;
                        if(!empty($lotto[$index])) {
                            $mg_materiale = DB::select('SELECT g.id_mg FROM giacenze g WHERE g.id_articolo = ? AND g.id_azienda = ? AND g.lotto = ? AND g.qta > 0 LIMIT 1',
                                [$materiale->id_materiale, $odl->id_azienda, $lotto[$index]]);
                        }

                        if($mg_materiale && sizeof($mg_materiale) > 0) {
                            $insert['id_mg'] = $mg_materiale[0]->id_mg;
                        } else {
                            // Fallback: cerca qualsiasi magazzino con giacenza per questo articolo
                            $mg_materiale_any = DB::select('SELECT g.id_mg FROM giacenze g WHERE g.id_articolo = ? AND g.id_azienda = ? AND g.qta > 0 LIMIT 1',
                                [$materiale->id_materiale, $odl->id_azienda]);

                            if(sizeof($mg_materiale_any) > 0) {
                                $insert['id_mg'] = $mg_materiale_any[0]->id_mg;
                            } else {
                                // Ultimo fallback: magazzino materie_prime default, poi produzione=0, poi primo disponibile
                                $mg_mp = DB::select(
                                    "SELECT * FROM mg
                                     WHERE id_azienda = ?
                                       AND ((tipologia = 'materie_prime' AND is_default = 1) OR produzione = 0)
                                     ORDER BY (tipologia = 'materie_prime' AND is_default = 1) DESC, id ASC
                                     LIMIT 1",
                                    [$odl->id_azienda]
                                );
                                if(sizeof($mg_mp) > 0){
                                    $insert['id_mg'] = $mg_mp[0]->id;
                                } else {
                                    $mg_any = DB::select('SELECT * FROM mg WHERE id_azienda = ? LIMIT 1', [$odl->id_azienda]);
                                    if(sizeof($mg_any) > 0){
                                        $insert['id_mg'] = $mg_any[0]->id;
                                    }
                                }
                            }
                        }

                        $insert['causale'] = 'Scarico Chiusura ODL ' . $odl->numero . ' Fase: ' . $riga->id_fase;

                        DB::table('mgmov')->insert($insert);

                        $m = new MagazzinoController();
                        $m->ricalcolaGiacenze($materiale->id_materiale);

                    }

                }
            }
        }



        $righeCompletate = 0;
       foreach($righeAll as $righe) {
           if($righe->completato == 1) {
               $righeCompletate ++;
           }

        }

       if($righeCompletate === count($righeAll)) {
           self::chiudi_odl($id_odl, $id_dorig, $utente,$lotto_produzione);
       }
    }




}
