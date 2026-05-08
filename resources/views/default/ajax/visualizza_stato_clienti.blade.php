<?php foreach ($bandi as $b){ ?>
<?php $array_clienti = explode(',', $b->id_clienti) ?>
<?php $clienti = DB::table('utenti')->whereIn('id', $array_clienti)->get() ?>

<?php $array_allegati = explode(',', $b->id_allegati) ?>
<?php $allegati = DB::table('bandi_allegati')->whereIn('id', $array_allegati)->get() ?>

<?php $array_mail_inviate = explode(',', $b->mail_inviate) ?>



<div class="modal modal-xl fade" id="modal_visualizza_stato_clienti_<?php echo $b->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Stato Invio Documenti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <div class="modal-body">
                <ul style="padding: 0; margin: 0; list-style-type: none">
                    <?php foreach ($clienti as $cl){ ?>
                        <li style="font-size: 18px; margin-bottom: 15px; border-bottom: 1px solid white; padding-bottom: 10px;">
                            <div class="row" style="margin-bottom: 20px; justify-content: space-between">
                                <p class="col-3" style="margin: 0">{{$cl->ragione_sociale}}<?php if(in_array($cl->id, $array_mail_inviate)){ ?> <span style="margin-left: 20px; font-size: 10px">(Mail inviata!)</span> <?php } ?></p>
                                <?php $bandi_allegati_utenti = DB::table('bandi_allegati_utenti')->where('id_bando', $b->id)->where('id_cliente', $cl->id)->where('preventivo', 0)->get() ?>
                                <?php $bandi_preventivi_utenti = DB::table('bandi_allegati_utenti')->where('id_bando', $b->id)->where('id_cliente', $cl->id)->where('preventivo', 1)->get() ?>
                                <?php $numero_allegati_caricati = count($bandi_allegati_utenti) ?>
                                <?php $numero_allegati_totali = count($array_allegati) ?>


                                <div class="col-8" style="margin: 0; display: flex; justify-content: space-between; align-items: center">
                                   <?php $percentuale = (intval($numero_allegati_caricati)  / intval($numero_allegati_totali) ) * 100; ?>
                                    <span>
                                        <?php echo $percentuale_arrotondata = intval($percentuale) ?>%
                                    </span>
                                    <div style="margin-left: 10px; width: 95%; height: 20px; border: 2px solid rgba(41, 156, 219, .18); background-color: rgba(41, 156, 219, .18)">
                                        <p style="padding: 0; margin: 0; height: 100%; width: <?php echo $percentuale_arrotondata ?>%; background-color: white"></p>
                                    </div>
                                </div>
                            </div>

                            <?php foreach ($allegati as $all){ ?>
                                <?php $bandi_allegati_utenti = DB::table('bandi_allegati_utenti')->where('id_bando', $b->id)->where('id_cliente', $cl->id)->where('id_allegato', $all->id)->first() ?>
                                <div style="margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between">
                                    <div>
                                        <?php if($bandi_allegati_utenti){ ?>
                                            <i style="margin-right: 10px" class="ri-checkbox-circle-line"></i>
                                        <?php }else{ ?>
                                            <i style="margin-right: 10px; color: red" class="ri-close-circle-line"></i>
                                        <?php } ?>
                                        {{$all->descrizione}}
                                        <?php if($bandi_allegati_utenti){ ?>
                                            <a target="_blank" style="margin-left: 10px" class="btn btn-sm btn-primary me-2" href="{{asset($bandi_allegati_utenti->path_allegato)}}"><i class="ri-article-line"></i></a>
                                        <?php } ?>
                                    </div>
                                    <?php if($bandi_allegati_utenti){ ?>
                                        <?php if($bandi_allegati_utenti->valore != null){ ?>
                                            <span>{{$bandi_allegati_utenti->valore}}€</span>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <hr>
                            <?php $totaleBPU = 0 ?>
                            <?php foreach($bandi_preventivi_utenti as $index => $bpu) { ?>
                                <?php $totaleBPU += $bpu->valore ?>
                                <div style="display: flex; justify-content: space-between;">
                                    <div class="mb-2">
                                        Preventivo n.{{$index + 1}}
                                        <a target="_blank" style="margin-left: 10px" class="btn btn-sm btn-primary me-2" href="{{asset($bpu->path_allegato)}}"><i class="ri-article-line"></i></a>
                                    </div>
                                    <span>{{$bpu->valore}}€</span>
                                </div>
                            <?php } ?>
                            <div class="d-flex justify-content-between">
                                <b>
                                    Totale Preventivi:
                                </b>
                                <span style="border-top: 1px solid lightgrey">
                                    {{$totaleBPU}}€
                                </span>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php } ?>

