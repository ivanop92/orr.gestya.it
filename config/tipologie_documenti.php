<?php

/*
 * Set standard di tipologie documento (tabella `do`).
 *
 * I codici cd_do sono CABLATI in numerosi punti del codice PHP/Blade
 * (es. WHERE cd_do = 'ORD', in_array($cd_do, ['RDO','ORDF']), ecc.):
 * non vanno mai modificati ne' rimossi - solo descrizione e flag attivo
 * sono personalizzabili dall'utente.
 *
 * I flag servono al motore documenti:
 *   attivo / passivo  -> ciclo vendita / acquisto
 *   impegno           -> impegna magazzino senza muoverlo
 *   scarico / carico  -> movimenta magazzino in uscita / ingresso
 *   trasferimento     -> spostamento tra magazzini
 *   fatturazione_uscita / fatturazione_ingresso -> conta in fatturato
 *   ordine            -> ordine di sequenza nel menu
 *   flusso            -> codici a cui questo documento puo' evolvere (CSV)
 */

return [
    [
        'cd_do' => 'PRE',
        'descrizione' => 'Preventivo',
        'attivo' => 1, 'passivo' => 0,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 1,
        'flusso' => 'ORD,DDT,FTV',
    ],
    [
        'cd_do' => 'ORD',
        'descrizione' => 'Ordine Cliente',
        'attivo' => 1, 'passivo' => 0,
        'impegno' => 1, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 2,
        'flusso' => 'DDT,FTV',
    ],
    [
        'cd_do' => 'DDT',
        'descrizione' => 'DDT',
        'attivo' => 1, 'passivo' => 0,
        'impegno' => 0, 'scarico' => 1, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 3,
        'flusso' => 'FTV',
    ],
    [
        'cd_do' => 'FTV',
        'descrizione' => 'Fattura di Vendita',
        'attivo' => 1, 'passivo' => 0,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 1, 'fatturazione_ingresso' => 0,
        'ordine' => 4,
        'flusso' => 'NC',
    ],
    [
        'cd_do' => 'NC',
        'descrizione' => 'Nota di Credito',
        'attivo' => 1, 'passivo' => 0,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 1, 'fatturazione_ingresso' => 0,
        'ordine' => 5,
        'flusso' => '',
    ],

    [
        'cd_do' => 'RDO',
        'descrizione' => 'Richiesta di Offerta',
        'attivo' => 0, 'passivo' => 1,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 10,
        'flusso' => 'ORDF',
    ],
    [
        'cd_do' => 'ORDF',
        'descrizione' => 'Ordine Fornitore',
        'attivo' => 0, 'passivo' => 1,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 11,
        'flusso' => 'BDC,FTI',
    ],
    [
        'cd_do' => 'BDC',
        'descrizione' => 'Bolla di Carico',
        'attivo' => 0, 'passivo' => 1,
        'impegno' => 0, 'scarico' => 0, 'carico' => 1, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 0,
        'ordine' => 12,
        'flusso' => 'FTI',
    ],
    [
        'cd_do' => 'FTI',
        'descrizione' => 'Fattura in Ingresso',
        'attivo' => 0, 'passivo' => 1,
        'impegno' => 0, 'scarico' => 0, 'carico' => 0, 'trasferimento' => 0,
        'fatturazione_uscita' => 0, 'fatturazione_ingresso' => 1,
        'ordine' => 13,
        'flusso' => '',
    ],
];
