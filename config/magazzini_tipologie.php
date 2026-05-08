<?php

/*
 * Tipologie di magazzino canoniche per Gestya.
 *
 * Ogni magazzino in `mg` ha:
 *   tipologia   -> categoria funzionale (vedi 'tipologie' qui sotto)
 *   is_default  -> 1 se e' il default per la sua tipologia in quell'azienda
 *
 * Multi-magazzino: una stessa azienda puo' avere PIU' magazzini per
 * la stessa tipologia (es. "Prodotti Finiti Capannone A" + "Prodotti Finiti Capannone B").
 * Solo uno pero' e' il default: e' quello usato dal sistema quando deve
 * scegliere automaticamente (es. carico ODL a chiusura).
 *
 * Il flag legacy `produzione` resta per retrocompatibilita': vale 1
 * quando tipologia='prodotti_finiti' AND is_default=1.
 */

return [

    // Set di magazzini standard creati automaticamente alla nascita di una nuova azienda.
    // Ne aggiunge uno per tipologia, gia' marcato is_default = 1.
    'standard' => [
        [
            'codice_magazzino' => 'MGPF',
            'descrizione'      => 'Magazzino Prodotti Finiti',
            'tipologia'        => 'prodotti_finiti',
            'is_default'       => 1,
            'produzione'       => 1, // backwards compat con il flag legacy
        ],
        [
            'codice_magazzino' => 'MGMP',
            'descrizione'      => 'Magazzino Materie Prime',
            'tipologia'        => 'materie_prime',
            'is_default'       => 1,
            'produzione'       => 0,
        ],
        [
            'codice_magazzino' => 'MGCO',
            'descrizione'      => 'Magazzino Articoli Commerciali',
            'tipologia'        => 'commerciali',
            'is_default'       => 1,
            'produzione'       => 0,
        ],
    ],

    // Tipologie disponibili: chiave -> { label, help (testo del popover 'i') }
    'tipologie' => [
        'prodotti_finiti' => [
            'label' => 'Prodotti Finiti',
            'help'  => 'Magazzino dove vengono caricati gli articoli al termine di un ODL (chiusura ordine di lavoro). Se ne hai piu\' d\'uno, quello marcato come "Default" e\' il magazzino usato automaticamente dal sistema.',
        ],
        'materie_prime' => [
            'label' => 'Materie Prime',
            'help'  => 'Magazzino delle materie prime e dei semilavorati impiegati nella produzione. Quando un ODL si chiude e bisogna scaricare un materiale che non ha lotto/giacenza, il sistema lo cerca qui.',
        ],
        'commerciali' => [
            'label' => 'Articoli Commerciali',
            'help'  => 'Magazzino degli articoli rivenduti senza lavorazione (acquistati e rivenduti come sono). Non e\' coinvolto nel carico/scarico ODL.',
        ],
        'altro' => [
            'label' => 'Altro',
            'help'  => 'Magazzino di servizio (resi, scarti, conto deposito, ecc.). Non viene usato automaticamente dal sistema: i movimenti li fai a mano o via DDT/BDC.',
        ],
    ],

];
