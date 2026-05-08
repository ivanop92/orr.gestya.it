<div style="margin:0 auto;display:block;width:100%;">
    <img src="/logo_dossier.png" style="width: 100%;margin-top:40px;" >
</div>

<?php if($utente->immagine != '/default/assets/images/users/user-dummy-img.jpg'){ ?>
<div style="margin:0 auto;display:block;width:20%;margin-top:20px;">
    <img src="<?php echo URL::asset($utente->immagine) ?>" style="width: 70%;margin-top:0px;" >
</div>
<?php } ?>

<h1 style="text-align: left;font-family: arial;margin-top:10px;text-transform: uppercase;padding:10px;">
    Dossier Tecnico - Finanziario<br>
    Esercizio Fiscale 2022
</h1>

<h3 style="text-align: left;font-family: arial;margin-top:10px;text-transform: uppercase;padding:10px;">
    CREDITO DI IMPOSTA FORMAZIONE 4.0
</h3>

<pagebreak>

<br><br><br>

<h3>Denominazione e Forma Giuridica</h3>
<b><?php echo $cliente->ragione_sociale ?></b><br>

<h3>Anagrafica Clienti</h3>
<span>Indirizzo: </span><b><?php echo $cliente->indirizzo ?></b><br>
<span>Codice Fiscale: </span><b><?php echo $cliente->cf ?></b><br>
<span>Partita IVA: </span><b><?php echo $cliente->piva ?></b><br>
<span>Telefono: </span><b><?php echo $cliente->telefono ?></b><br>


<h3>Unit&agrave; locale interessata ai corsi di formazione</h3>
<span>Indirizzo: </span><b><?php echo $cliente->indirizzo ?></b><br>
<span>Codice ATECO: </span><b></b><br>
<span>Descrizione settore/ attivit&agrave;: </span><b></b><br>


<h3>Legale Rappresentante</h3>
<span>Nome: </span><b><?php echo $cliente->nome ?></b><br>
<span>Cognome: </span><b><?php echo $cliente->cognome ?></b><br>

<pagebreak>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<h1 style="text-align: center;font-weight: bold;">SEZIONE 1</h1>
<h3 style="text-align: center;font-weight: bold;">NORMATIVA DI RIFERIMENTO</h3>

<pagebreak>

    <ol class="decimal_type" style="list-style-type: decimal;">
        <li>
            <h1 style='margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;text-indent:-18.0pt;line-height:120%;font-size:16px;font-family:"Book Antiqua",serif;color:black;'>Normativa di riferimento</h1>
            <h1 style='margin-top:0cm;margin-right:0cm;margin-bottom:6.0pt;text-indent:-18.0pt;line-height:120%;font-size:16px;font-family:"Book Antiqua",serif;color:black;'>Scheda di sintesi della normativa</h1>
        </li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>OBIETTIVI</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>La misura &egrave; volta a stimolare gli investimenti delle imprese nella formazione del personale sulle materie aventi ad oggetto le tecnologie rilevanti per la trasformazione tecnologica e digitale delle imprese.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>BENEFICIARI</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Tutte le imprese residenti nel territorio dello Stato Italiano, incluse le stabili organizzazioni di soggetti non residenti indipendentemente dalla natura giuridica, dal settore economico, dalla dimensione, dal regime contabile e dal sistema di determinazione del reddito ai fini fiscali.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>ESCLUSIONI</p>
    <ul class="decimal_type" style="list-style-type: undefined;">
        <li>Imprese in stato di crisi (liquidazione volontaria, fallimento, liquidazione coatta amministrativa, concordato preventivo senza continuit&agrave; aziendale o sottoposte ad altra procedura concorsuale)</li>
        <li>Imprese destinatarie di sanzioni interdittive di cui all&rsquo;art. 9, comma 2, del D. Lgs. N. 231 del 2001</li>
    </ul>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>ATTIVITA&rsquo; AMMISSIBILI</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Lo strumento nazionale &egrave; utilizzato in forma di credito d&rsquo;imposta in relazione alle attivit&agrave; di formazione, sostenute dal 1&deg; gennaio 2021 al 31 dicembre 2022, finalizzate all&rsquo;acquisizione o al consolidamento da parte del personale dipendente dell&rsquo;impresa delle competenze nelle tecnologie rilevanti per la realizzazione del processo di trasformazione tecnologica e digitale delle imprese previsto dal &ldquo;Piano nazionale Impresa 4.0&rdquo;.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Costituiscono in particolare attivit&agrave; ammissibili al credito di imposta le attivit&agrave; di formazione concernenti le</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>seguenti tipologie:</p>
    <ol start="1" style="list-style-type: lower-alpha;">
        <li>big data e analisi dei dati</li>
        <li>cloud e fog computing</li>
        <li>cyber security</li>
        <li>sistemi cyber-fisici</li>
        <li>prototipazione rapida</li>
        <li>sistemi di visualizzazione e realt&agrave; aumentata</li>
        <li>robotica avanzata e collaborativa</li>
        <li>interfaccia uomo macchina</li>
        <li>manifattura additiva</li>
        <li>internet delle cose e delle macchine</li>
        <li>integrazione digitale dei processi aziendali</li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Le attivit&agrave; formative devono riguardare i seguenti ambiti:</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>VENDITA E MARKETING</p>
    <ol style="list-style-type: upper-roman;margin-left:53.8px;">
        <li>Acquisti</li>
        <li>Commercio al dettaglio</li>
        <li>Commercio all&rsquo;ingrosso</li>
        <li>Gestione del magazzino</li>
        <li>Servizi ai consumatori</li>
        <li>Stoccaggio</li>
        <li>Tecniche di dimostrazione</li>
        <li>Marketing</li>
        <li>Ricerca di mercato</li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>INFORMATICA</p>
    <ol start="24" style="list-style-type: upper-alpha;margin-left:53.8px;">
        <li>Analisi di sistemi informatici</li>
        <li>Elaborazione elettronica dei dati</li>
        <li>Formazione degli amministratori di rete</li>
        <li>Linguaggi di programmazione</li>
        <li>Progettazione di sistemi informatici</li>
        <li>Programmazione informatica</li>
        <li>Sistemi operativi</li>
        <li>Software per lo sviluppo e la gestione di beni strumentali oggetto dell&rsquo;allegato A alla legge n. 232/2016</li>
        <li>Software oggetto dell&rsquo;allegato B alla legge n. 232/2016</li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>TECNICHE E TECNOLOGIE DI PRODUZIONE</p>
    <ol style="list-style-type: undefined;margin-left:53.8px;">
        <li>Fabbricazione di armi da fuoco</li>
        <li>Fabbricazione di utensili e stampi</li>
        <li>Fusione dei metalli e costruzione di stampi</li>
        <li>Idraulica</li>
        <li>Ingegneria meccanica</li>
        <li>Ingegneria metallurgica</li>
        <li>Lavorazione della lamiera</li>
        <li>Meccanica di precisione</li>
        <li>Lavorazione a macchina dei metalli</li>
        <li>Saldatura</li>
        <li>Siderurgia</li>
        <li>Climatizzazione</li>
        <li>Distribuzione del gas</li>
        <li>Energia nucleare, idraulica e termica</li>
        <li>Ingegneria climatica</li>
        <li>Ingegneria elettrica</li>
        <li>Installazione e manutenzione di linee elettriche</li>
        <li>Installazioni elettriche</li>
        <li>Produzione di energia elettrica</li>
        <li>Riparazione di apparecchi elettrici</li>
        <li>Elettronica delle telecomunicazioni</li>
        <li>Ingegneria del controllo</li>
        <li>Ingegneria elettronica</li>
        <li>Installazione di apparecchiature di comunicazione</li>
        <li>Manutenzione di apparecchiature di comunicazione</li>
        <li>Manutenzione di apparecchiature elettroniche</li>
        <li>Robotica</li>
        <li>Sistemi di comunicazione</li>
        <li>Tecnologie delle telecomunicazioni</li>
        <li>Tecnologie di elaborazione dati</li>
        <li>Biotecnologie</li>
        <li>Conduzione di impianti e macchinari di trasformazione</li>
        <li>Ingegneria chimica</li>
        <li>Ingegneria chimica dei processi</li>
        <li>Processi petroliferi, gas e petrolchimici</li>
        <li>Tecniche di chimica dei processi</li>
        <li>Tecniche di laboratorio (chimico)</li>
        <li>Tecnologie biochimiche</li>
        <li>Cantieristica navale</li>
        <li>Manutenzione e riparazione imbarcazioni</li>
        <li>Ingegneria automobilistica</li>
        <li>Ingegneria motociclistica</li>
        <li>Manutenzione e riparazione di veicoli</li>
        <li>Progettazione di aeromobili</li>
        <li>Manutenzione di aeromobili</li>
        <li>Agricoltura di precisione</li>
        <li>Lavorazione degli alimenti</li>
        <li>Conservazione degli alimenti</li>
        <li>Produzione bevande</li>
        <li>Lavorazione del tabacco</li>
        <li>Scienza e tecnologie alimentari</li>
        <li>Confezione di calzature</li>
        <li>Filatura</li>
        <li>Lavorazione del cuoio e delle pelli</li>
        <li>Preparazione e filatura della lana</li>
        <li>Produzione di capi di abbigliamento</li>
        <li>Produzione di cuoio e pellami</li>
        <li>Sartoria</li>
        <li>Selleria</li>
        <li>Tessitura industriale</li>
        <li>Ceramica industriale</li>
        <li>Ebanisteria</li>
        <li>Fabbricazione di mobili</li>
        <li>Falegnameria (non edile)</li>
        <li>Lavorazione della gomma</li>
        <li>Lavorazione e curvatura del legno</li>
        <li>Lavorazione industriale del vetro</li>
        <li>Produzione della plastica</li>
        <li>Produzione e lavorazione della carta</li>
        <li>Produzione industriale di diamanti</li>
        <li>Tecnologie del legno da costruzione</li>
        <li>Estrazione di carbone</li>
        <li>Estrazione di gas e petrolio</li>
        <li>Estrazione di materie grezze</li>
        <li>Ingegneria geotecnica</li>
        <li>Ingegneria mineraria</li>
        <li>Cartografia/agrimensura e rilievi</li>
        <li>Progettazione delle strutture architettoniche</li>
        <li>Progettazione e pianificazione urbana</li>
        <li>Progettazione edilizia</li>
        <li>Costruzione di ponti</li>
        <li>Costruzione di strade</li>
        <li>Edilizia</li>
        <li>Impianti idraulici, riscaldamento e ventilazione</li>
        <li>Ingegneria civile</li>
        <li>Ingegneria edile</li>
        <li>Ingegneria portuale</li>
        <li>Tecnologie edili ed ingegneristiche (Building Information Modeling)</li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Non possono essere agevolate le attivit&agrave; di formazione ordinaria o periodica che l&rsquo;impresa organizza per conformarsi alla normativa vigente in materia di sicurezza e salute sui luoghi di lavoro, di protezione dell&rsquo;ambiente e ad ogni altra normativa obbligatoria in materia di formazione</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>SPESE AMMISSIBILI</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Si considerano ammissibili al credito di imposta:</p>
    <ol start="1" style="list-style-type: lower-alpha;">
        <li>le spese di personale relative ai formatori per le ore di partecipazione alla formazione;</li>
        <li>i costi di esercizio relativi a formatori e partecipanti alla formazione direttamente connessi al progetto di formazione, quali le spese di viaggio, i materiali e le forniture con attinenza diretta al progetto, l&rsquo;ammortamento degli strumenti e delle attrezzature per la quota da riferire al loro uso esclusivo per il progetto di formazione. Sono escluse le spese di alloggio, ad eccezione delle spese di alloggio minime necessarie per i partecipanti che sono lavoratori con disabilit&agrave;;</li>
        <li>i costi dei servizi di consulenza connessi al progetto di formazione;</li>
        <li>le spese di personale relative ai partecipanti alla formazione e le spese generali indirette (spese amministrative, locazione, spese generali) per le ore durante le quali i partecipanti hanno seguito la formazione</li>
    </ol>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>AGEVOLAZIONE</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Il credito d&rsquo;imposta spetta in misura pari al:</p>
    <ul class="decimal_type" style="list-style-type: undefined;">
        <li>50% delle spese ammissibili sostenute nel periodo d&rsquo;imposta agevolabile e nel limite massimo di 300.000 euro per le piccole imprese;</li>
        <li>40% delle spese ammissibili sostenute nel periodo di imposta agevolabile e nel limite massimo di 250.000 euro per le medie imprese;</li>
        <li>30% delle spese ammissibili sostenute nel periodo d&rsquo;imposta agevolabile e nel limite massimo di 250.000 euro per le grandi imprese.</li>
    </ul>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Le aliquote del credito d&apos;imposta sono state rimodulate dal decreto Aiuti (art. 22, D.L. n. 50/2022), entrato in vigore il 18 maggio 2022. In particolare, a seguito del decreto Aiuti e previo soddisfacimento delle condizioni fissate dal decreto del Ministro dello Sviluppo Economico 1&deg; luglio 2022, il credito di imposta &egrave; aumentato nella misura del:</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>per le piccole imprese, dal 50% al 70% delle spese ammissibili, nel limite massimo annuale di 300.000 euro;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>per le medie imprese, dal 40% al 50%, nel limite massimo annuale di 250.000 euro.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Per i progetti di formazione avviati successivamente al 18 maggio 2022, che non soddisfino le condizioni previste dal suddetto decreto ministeriale 1&deg; luglio 2022, le misure del credito d&apos;imposta sono diminuite al:</p>
    <ul class="decimal_type" style="list-style-type: undefined;">
        <li>40% delle spese ammissibili, nel limite massimo annuale di 300.000 per le piccole imprese;</li>
        <li>35% delle spese ammissibili, nel limite massimo annuale di 250.000 euro, per le medie imprese.</li>
        <li>Il decreto Aiuti non modifica l&rsquo;aliquota agevolativa prevista per le grandi imprese, pari al 30% delle spese ammissibili, nel limite massimo annuale di 250.000 euro.</li>
    </ul>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>La misura del credito d&rsquo;imposta &egrave; aumentata per tutte le imprese, fermo restando i limiti massimi annuali, al</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>60% nel caso in cui i destinatari della formazione ammissibile rientrino nelle categorie dei lavoratori dipendenti svantaggiati o molto svantaggiati, come definite dal decreto del Ministro del lavoro e delle politiche sociali del 17 ottobre 2017.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>Sono altres&igrave; agevolate al 100% le spese per l&rsquo;eventuale certificazione dei costi sostenuti rilasciata da revisore contabile, per un importo massimo di &euro; 5.000, ma solo nel caso di imprese non soggette a revisione.</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>OBBLIGHI PER LE IMPRESE</p>
    <ul style="list-style-type: undefined;">
        <li>Le attivit&agrave; di formazione sono ammissibili a condizione che il loro svolgimento sia espressamente disciplinato in contratti collettivi aziendali o territoriali depositati, nel rispetto dell&rsquo;articolo 14 del decreto legislativo 15 giugno 2015, n. 151, presso l&rsquo;ispettorato Territoriale del Lavoro competente e che, con apposita dichiarazione resa dal legale rappresentante, sia rilasciata a ciascun dipendente l&rsquo;attestazione dell&rsquo;effettiva partecipazione alle attivit&agrave; formative agevolabili, con l&rsquo;indicazione dell&rsquo;ambito o degli ambiti aziendali di applicazione delle conoscenze e delle competenze acquisite o consolidate dal dipendente in esito alle stesse attivit&agrave; formative.</li>
        <li>L&rsquo;effettivo sostenimento delle spese ammissibili e la corrispondenza delle stesse alla documentazione contabile predisposta dall&rsquo;impresa devono risultare da apposita certificazione rilasciata dal soggetto incaricato della revisione legale dei conti.</li>
        <li>Le imprese beneficiarie del credito di imposta sono tenute a conservare una relazione che illustri le modalit&agrave; organizzative, i contenuti delle attivit&agrave; di formazione svolte e redigere registri nominativi del personale in formazione, sottoscritto dal docente e dai dipendenti.</li>
        <li>&Egrave; obbligatorio indicare il credito di imposta nella relativa dichiarazione dei redditi.</li>
    </ul>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>NORMATIVA DI RIFERIMENTO</p>
    <ul class="decimal_type" style="list-style-type: undefined;">
        <li>Art. 1, commi 46 &ndash; 56, legge 27 dicembre 2017, n. 205 (normattiva.it)</li>
        <li>Art. 1, commi 78 &ndash; 81, legge 30 dicembre 2018, n. 145 (normattiva.it)</li>
        <li>Art. 1, commi 210 &ndash; 217, legge 27 dicembre 2019, n. 160 (normattiva.it)</li>
        <li>Art. 1, comma 1064, lettere i) e l), legge 30 dicembre 2020, n. 178 (gazzettaufficiale.it)</li>
        <li>Art. 1, commi 46 &ndash; 56, Legge 27 dicembre 2017, n. 205 (normattiva.it)</li>
        <li>Decreto 4 maggio 2018 (pdf) - pubblicato sulla Gazzetta ufficiale n. 143 del 22 giugno 2018</li>
        <li>Relazione illustrativa del decreto 4 maggio 2018 (pdf)</li>
        <li>Circolare direttoriale n. 412088 del 3 dicembre 2018 - Chiarimenti sul credito d&apos;imposta (pdf)</li>
        <li>Circolare dell&apos;Agenzia delle Entrate n. 8 del 10 aprile 2019 - Paragrafo 3.2</li>
        <li>Art. 22, Decreto Legge 17 maggio 2022, n. 50</li>
    </ul>
    <p style='margin:0cm;text-align:justify;line-height:120%;font-size:13px;font-family:"Book Antiqua",serif;'>&nbsp;</p>

<pagebreak>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<h1 style="text-align: center;font-weight: bold;">SEZIONE 2</h1>
<h3 style="text-align: center;font-weight: bold;">ATTIVITA' DI FORMAZIONE 4.0: DESCRIZIONE E RENDICONTAZIONE</h3>

<pagebreak>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<h1 style="text-align: center;font-weight: bold;">SEZIONE 3</h1>
<h3 style="text-align: center;font-weight: bold;">CALCOLO CREDITO D'IMPOSTA</h3>

<pagebreak>

<br><br><br>

<h3>Calcolo Beneficio Fiscale</h3><br>

<table width="100%" style="font-weight: bold;">
    <tbody>
        <tr class="border_bottom">
            <td width="75%">Costi Esercizio Fiscale</td>
            <td width="25%" align="right">&euro; <?php echo number_format($corso->sez3_costi_esercizio,2,'.','') ?></td>
        </tr>
        <tr class="border_bottom">
            <td width="75%">Costi Agevolabili</td>
            <td width="25%" align="right">&euro; <?php echo number_format($corso->sez3_costi_agevolabili,2,'.','') ?></td>
        </tr>
        <tr class="border_bottom">
            <td width="75%">Credito D'imposta</td>
            <td width="25%" align="right">&euro; <?php echo number_format($corso->sez3_credito_imposta,2,'.','') ?></td>
        </tr>
        <tr class="border_bottom">
            <td width="75%">Credito D'imposta spettante</td>
            <td width="25%" align="right">&euro; <?php echo number_format($corso->sez3_credito_imposta_spettante,2,'.','') ?></td>
        </tr>
    </tbody>
</table>

<pagebreak>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<h1 style="text-align: center;font-weight: bold;">SEZIONE 4</h1>
<h3 style="text-align: center;font-weight: bold;">MODALITA' DI RIPORTO IN DICHIARAZIONE</h3>

<pagebreak>

<br><br><br>
<h3>Facsimile Modello F24</h3>
<div style="margin:0 auto;display:block;width:80%;">
    <img src="/F24_1.png" style="width: 100%;" >
</div>
<br><br>
<h3>Modalità di Riporto in Dichiarazione</h3>
<div style="margin:0 auto;display:block;width:50%;">
    <img src="/F24_2.png" style="width: 100%;" >
</div>

<pagebreak>

<br><br><br>
<h3>BOZZA NOTA INTEGRATIVA ESERCIZIO 2022</h3><br><br>

La società, nel corso dell’esercizio 2022, ha partecipato ad attività di formazione 4.0, in particolare sul seguente corso:<br><br>

Corso di formazione per la digitalizzazione dei processi aziendali<br><br>

Per il corso sopra indicato, la società ha sostenuto costi per un valore complessivo pari a € <b><?php echo number_format($corso->sez3_costi_esercizio,2,'.','') ?></b>, interamente eleggibili per il Credito d’imposta Formazione 4.0 previsto ai sensi dell’Art. 1, commi 46 – 5, Legge 27 di-cembre 2017, n. 205 (legge di bilancio 2018) e ss.mm.ii.<br><br>

La società ha intenzione, pertanto, di fruire del credito di imposta spettante secondo le indicazioni e le re-golamentazioni normative suddette richiamate, in quanto tali spese sono state correttamente rendicontante e relazionate per l’esercizio fiscale 2022.<br>
Il credito ottenuto è pari a € <b><?php echo number_format($corso->sez3_credito_imposta_spettante,2,'.','') ?></b>, al netto della spesa di certificazione dei costi di formazione 4.0.<br>

<pagebreak>

<br><br><br>

<h3>DICHIARAZIONE LEGALE RAPPRESENTNATE</h3><br>
Dichiarazione sostitutiva di atto notorio ai sensi degli articoli 46, 47 e 76 del testo unico delle disposizioni legislative e regolamentari in materia di documentazione amministrativa, emanato con decreto del Presi-dente della Repubblica n. 445 del 28 dicembre 2000.<br><br>

Il sottoscritto <b><?php echo $utente->nome.' '.$utente->cognome ?></b> nato a <b><?php echo $utente->luogo_nascita ?></b>, il <b><?php echo $utente->data_nascita ?></b>, in qualità di Legale Rappresentante della società <b><?php echo $utente->ragione_sociale ?></b>, con sede legale in <b><?php echo $utente->comune ?>, <?php echo $utente->indirizzo ?></b> , consapevole, ai sensi degli articoli 46, 47 e 76del D.P.R. n. 445 del 28/12/2000, delle responsabilità penali in cui puoi incorrere in caso di dichiarazioni mendaci o formazione o esibizione di atto falso<br><br><br>

<div style="text-align: center">Dichiara sotto la propria responsabilità</div><br><br>

Che la società nel periodo d’imposta 01/01/2022 – 31/12/2022, ha svolto attività di formazione 4.0 ricondu-cibile al seguente corso:<br><br>

<h3><?php echo $corso->descrizione ?></h3><br>

Per un valore complessivo di € <b><?php echo number_format($corso->sez3_costi_esercizio,2,'.','') ?></b>, che risulta essere congruo e inerente ai progetti formativi sopra esposti.<br><br>

Sul valore di € <b><?php echo number_format($corso->sez3_costi_esercizio,2,'.','') ?></b>, e sulla spesa di certificazione dei costi di formazione 4.0 pari a € <b><?php echo number_format($corso->sez3_costi_esercizio,2,'.','') ?></b> la società intende avvalersi del credito d’imposta per attività di formazione 4.0 Art. 1, commi 46 – 5, Legge 27 dicembre 2017, n. 205 (legge di bilancio 2018) e ss.mm.ii.<br><br>

<div style="text-align: right;padding:20px;">

    Luogo, lì<br><br><br>

    _______________________________
</div>

    <style>


        tr.header th {
            border:1px solid black;
            text-align:left;
            font-size:15px;
        }

        tbody tr.border_bottom td {
            border:1px solid black;
            height:30px;
            font-size:14px;
            text-align: left;
            padding-left:10px;
        }

        table {
            border-collapse: collapse;
        }
    </style>

