<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<p:FatturaElettronica versione="FPR12" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">
    <FatturaElettronicaHeader>
        <DatiTrasmissione>
            <IdTrasmittente>
                <IdPaese>{{$azienda->nazione}}</IdPaese>
                <IdCodice>{{ $azienda->partita_iva }}</IdCodice>
            </IdTrasmittente>
            <ProgressivoInvio>{{ $testata->id }}</ProgressivoInvio>
            <FormatoTrasmissione>FPR12</FormatoTrasmissione>
            <CodiceDestinatario>{{ !empty($testata->sdi) ? $testata->sdi : '0000000' }}</CodiceDestinatario>
            @if (!empty($testata->pec))
                <PECDestinatario>{{ $testata->pec }}</PECDestinatario>
            @endif
        </DatiTrasmissione>
        <CedentePrestatore>
            <DatiAnagrafici>
                <IdFiscaleIVA>
                    <IdPaese>{{ $azienda->nazione }}</IdPaese>
                    <IdCodice>{{ $azienda->partita_iva }}</IdCodice>
                </IdFiscaleIVA>
                <CodiceFiscale>{{ $azienda->partita_iva }}</CodiceFiscale>
                <Anagrafica>
                    <Denominazione>{{ str_replace('&', '', $azienda->ragione_sociale) }}</Denominazione>
                </Anagrafica>
                <RegimeFiscale>{{ $azienda->regime_fiscale }}</RegimeFiscale>
            </DatiAnagrafici>
            <Sede>
                <Indirizzo>{{ $azienda->indirizzo }}</Indirizzo>
                {{--<NumeroCivico>{{ $azienda->numero_civico ?? 'N/A' }}</NumeroCivico>--}} <!-- Aggiungi una colonna numero_civico se necessario -->
                <CAP>{{ $azienda->cap }}</CAP>
                <Comune>{{ $azienda->comune }}</Comune>
                <Provincia>{{ $azienda->provincia }}</Provincia>
                <Nazione>{{$azienda->nazione}}</Nazione>
            </Sede>
        </CedentePrestatore>

        <CessionarioCommittente>
            <DatiAnagrafici>
                <?php if($testata->partita_iva_fatturazione != ''){ ?>
                <IdFiscaleIVA>
                    <IdPaese><?php echo $testata->nazione ?></IdPaese>
                    <IdCodice><?php echo $testata->partita_iva_fatturazione ?></IdCodice>
                </IdFiscaleIVA>
                <?php } ?>
                <?php if($testata->codice_fiscale_fatturazione != ''){ ?>
                <CodiceFiscale><?php echo $testata->codice_fiscale_fatturazione ?></CodiceFiscale>
                <?php } ?>
                <Anagrafica>
                    <Denominazione><?php echo str_replace('&', '', $testata->ragione_sociale_fatturazione) ?></Denominazione>
                </Anagrafica>
            </DatiAnagrafici>
            <Sede>
                <Indirizzo><?php echo $testata->indirizzo ?></Indirizzo>
                <CAP><?php echo $testata->cap ?></CAP>
                <Comune><?php echo $testata->comune_fatturazione ?></Comune>
                <Provincia><?php echo strtoupper($testata->provincia_fatturazione) ?></Provincia>
                <Nazione><?php echo $testata->nazione ?></Nazione>
            </Sede>
        </CessionarioCommittente>
    </FatturaElettronicaHeader>
    <FatturaElettronicaBody>

        <DatiGenerali>
            <DatiGeneraliDocumento>
                <TipoDocumento><?php echo $testata->tipologia_documento ?></TipoDocumento>
                <Divisa><?php echo $testata->divisa ?></Divisa>
                <Data><?php echo $testata->data_doc ?></Data>
                <Numero><?php echo $testata->numero_doc ?></Numero>
                <ImportoTotaleDocumento><?php echo number_format($testata->totale - $testata->sconto_maggiorazione,2,'.','') ?></ImportoTotaleDocumento>

                <?php if($testata->oggetto_visibile != ''){ ?>
                <Causale><?php echo $testata->oggetto_visibile ?></Causale>
                <?php } ?>

            </DatiGeneraliDocumento>
        </DatiGenerali>

        <DatiBeniServizi>

            <?php $i = 1; ?>
            <?php foreach($righe as $r){ ?>
            <DettaglioLinee>
                <NumeroLinea><?php echo $i; ?></NumeroLinea>
                <Descrizione><?php echo str_replace('’','\'',str_replace('–',' ',$r->descrizione)) ?></Descrizione>
                <Quantita><?php echo number_format($r->qta,2,'.','') ?></Quantita>
                <UnitaMisura><?php echo ($r->um != '')?$r->um:'NR' ?></UnitaMisura>
                <PrezzoUnitario><?php echo number_format($r->prezzo_unitario,2,'.','') ?></PrezzoUnitario>
                <PrezzoTotale><?php echo number_format($r->prezzo_totale,2,'.','') ?></PrezzoTotale>
                <AliquotaIVA><?php echo number_format($r->iva,2,'.','') ?></AliquotaIVA>
                    <?php if($r->codice_iva != ''){ ?>
                <Natura><?php echo $r->codice_iva ?></Natura>
                <?php } ?>
            </DettaglioLinee>
                <?php $i++;} ?>

            <?php foreach($dati_riepilogo as $dr){ ?>
            <DatiRiepilogo>
                <AliquotaIVA><?php echo  number_format($dr->iva,2,'.','') ?></AliquotaIVA>
                    <?php if($dr->codice_iva != ''){ ?>
                <Natura><?php echo $dr->codice_iva ?></Natura>
                <?php } ?>
                <ImponibileImporto><?php echo number_format($dr->imponibile,2,'.','') ?></ImponibileImporto>
                <Imposta><?php echo number_format($dr->imposta,2,'.','') ?></Imposta>
                <EsigibilitaIVA><?php echo $testata->esigibilita_iva ?></EsigibilitaIVA>
                    <?php if($dr->rif_normativo_pdf != ''){ ?>
                <RiferimentoNormativo><?php echo $dr->rif_normativo_pdf ?></RiferimentoNormativo>
                <?php } ?>
            </DatiRiepilogo>
            <?php } ?>


        </DatiBeniServizi>

        <?php foreach($scadenziario as $s){ ?>
        <DatiPagamento>
            <CondizioniPagamento><?php echo $testata->condizioni_pagamento ?></CondizioniPagamento>
            <DettaglioPagamento>
                <ModalitaPagamento><?php echo $testata->modalita_pagamento ?></ModalitaPagamento>
                <DataScadenzaPagamento><?php echo $s->data_scadenza ?></DataScadenzaPagamento>
                <ImportoPagamento><?php echo number_format($s->importo,2,'.','') ?></ImportoPagamento>
                    <?php if($testata->istituto_finanziario != ''){ ?><IstitutoFinanziario><?php echo $testata->istituto_finanziario ?></IstitutoFinanziario><?php } ?>
                                                                                                                                                                 <?php if($testata->iban != ''){ ?> <IBAN><?php echo $testata->iban ?></IBAN> <?php } ?>

            </DettaglioPagamento>
        </DatiPagamento>
        <?php } ?>

        <?php if($testata->allegato != ''){ ?>

        <Allegati>
            <NomeAttachment><?php echo $testata->nome_allegato ?></NomeAttachment>
                <?php
                $imagedata = file_get_contents($testata->allegato);
                $base64 = base64_encode($imagedata);
                ?>
            <Attachment><?php echo $base64 ?></Attachment>
        </Allegati>

        <?php } ?>

        <?php if($testata->allegato2 != ''){ ?>

        <Allegati>
            <NomeAttachment><?php echo $testata->nome_allegato2 ?></NomeAttachment>

                <?php
                $imagedata = file_get_contents($testata->allegato2);
                $base64 = base64_encode($imagedata);
                ?>

            <Attachment><?php echo $base64 ?></Attachment>

        </Allegati>
        <?php } ?>

    </FatturaElettronicaBody>
</p:FatturaElettronica>