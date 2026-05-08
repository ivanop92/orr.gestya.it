-- --------------------------------------------------------
-- Host:                         135.125.180.188
-- Versione server:              8.0.26 - MySQL Community Server - GPL
-- S.O. server:                  Linux
-- HeidiSQL Versione:            12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dump della struttura di tabella gestya.it.articoli
CREATE TABLE IF NOT EXISTS `articoli` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  `codice_articolo` varchar(250) DEFAULT NULL,
  `immagine` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '/placehold_immagine.png',
  `titolo` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `descrizione` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `prezzo` decimal(10,2) DEFAULT NULL,
  `tipologia` int DEFAULT (0),
  `um` varchar(100) DEFAULT NULL,
  `giacenza` double DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `data_creazione` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ateco_codici
CREATE TABLE IF NOT EXISTS `ateco_codici` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sezione` int NOT NULL DEFAULT '0',
  `sezione` varchar(50) NOT NULL,
  `codice` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `descrizione` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3158 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ateco_sezioni
CREATE TABLE IF NOT EXISTS `ateco_sezioni` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sezione` varchar(250) NOT NULL DEFAULT '0',
  `descrizione` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.aziende
CREATE TABLE IF NOT EXISTS `aziende` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titolo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descrizione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ragione_sociale` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `partita_iva` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comune` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dipendenti` int DEFAULT NULL,
  `codice_ateco` int DEFAULT NULL,
  `descrizione_codice_ateco` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_ricezione_fatture` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cap` int DEFAULT NULL,
  `provincia` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codice_sdi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pec` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  `regime_fiscale` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nazione` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'IT',
  `id_modulo` int DEFAULT NULL,
  `invio_mail_sollecito` int DEFAULT '0',
  `template_oggetto_sollecito` text COLLATE utf8mb4_general_ci,
  `template_testo_sollecito` text COLLATE utf8mb4_general_ci,
  `template_testo_sollecito_rate` text COLLATE utf8mb4_general_ci,
  `email_smtp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_smtp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `modalita_pagamento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'MP05',
  `condizioni_pagamento` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'TP02',
  `istituto_finanziario` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `natura` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `capitale_sociale` decimal(10,2) DEFAULT NULL,
  `nr_rea` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `immagine` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '/placehold_immagine.png',
  `token_azienda` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.celle_tecnopack
CREATE TABLE IF NOT EXISTS `celle_tecnopack` (
  `id` int NOT NULL AUTO_INCREMENT,
  `row_index` int NOT NULL,
  `col_index` int NOT NULL,
  `cell_text` text COLLATE utf8mb4_general_ci,
  `cell_color` varchar(7) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '#ffffff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cell` (`row_index`,`col_index`)
) ENGINE=InnoDB AUTO_INCREMENT=581 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.clienti
CREATE TABLE IF NOT EXISTS `clienti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_utente` int NOT NULL DEFAULT '0',
  `id_agente` int NOT NULL DEFAULT '0',
  `id_reparto` int DEFAULT NULL,
  `id_sezione` int DEFAULT NULL,
  `immagine` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '/default/assets/images/users/user-dummy-img.jpg',
  `nome` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cognome` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_nascita` date DEFAULT NULL,
  `luogo_nascita` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `piva` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ragione_sociale` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cciaa` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rea` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `esigibilita_iva` varchar(100) DEFAULT 'I',
  `telefono` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `indirizzo` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cap` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `comune` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `provincia` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `regione` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fatturato` int DEFAULT NULL,
  `dipendenti` int DEFAULT '0',
  `ateco_codice` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ateco_descrizione` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `grandezza_azienda` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cf` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `sdi` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pec` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_recapito` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_leads` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `referente` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono_referente` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `id_tipologia` int DEFAULT '0',
  `verification_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_recupero_password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `abilitato` int DEFAULT '1',
  `accesso_inviato` int DEFAULT '0',
  `timeins` datetime DEFAULT NULL,
  `token_utente_per_bando` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `onesignal_token` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `onesignal_token_mobile` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cd_cf` varchar(8) DEFAULT NULL,
  `nazione` varchar(50) DEFAULT 'IT',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.commesse
CREATE TABLE IF NOT EXISTS `commesse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  `codice_commessa` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_general_ci,
  `data_apertura` date DEFAULT NULL,
  `data_chiusura` date DEFAULT NULL,
  `stato` enum('aperta','in_corso','completata','annullata') COLLATE utf8mb4_general_ci DEFAULT 'aperta',
  `budget` decimal(10,2) DEFAULT '0.00',
  `costi` decimal(10,2) DEFAULT '0.00',
  `ricavi` decimal(10,2) DEFAULT '0.00',
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.commesse_attivita
CREATE TABLE IF NOT EXISTS `commesse_attivita` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_commessa` int NOT NULL,
  `id_azienda` int DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  `titolo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_general_ci,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `data_inizio_effettiva` date DEFAULT NULL,
  `data_fine_effettiva` date DEFAULT NULL,
  `completamento` int DEFAULT '0',
  `costo` decimal(10,2) DEFAULT '0.00',
  `stato` enum('da_iniziare','in_corso','completata','in_ritardo') COLLATE utf8mb4_general_ci DEFAULT 'da_iniziare',
  `priorita` enum('bassa','media','alta') COLLATE utf8mb4_general_ci DEFAULT 'media',
  `id_attivita_precedente` int DEFAULT NULL,
  `id_responsabile` int DEFAULT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.commesse_extra
CREATE TABLE IF NOT EXISTS `commesse_extra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_commessa` int unsigned NOT NULL,
  `id_azienda` int unsigned DEFAULT NULL,
  `id_utente` int unsigned DEFAULT NULL,
  `tipo` enum('ricavo','costo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ricavo',
  `descrizione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `data` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.conti
CREATE TABLE IF NOT EXISTS `conti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codice_contabile` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('entrata','uscita') COLLATE utf8mb4_general_ci DEFAULT 'entrata',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.contratti
CREATE TABLE IF NOT EXISTS `contratti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `cliente_id` int NOT NULL,
  `descrizione` text COLLATE utf8mb4_general_ci NOT NULL,
  `data` date NOT NULL,
  `allegati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `prezzo` decimal(10,2) DEFAULT '0.00',
  `iva` int DEFAULT '0',
  `giorno_fatturazione` int DEFAULT NULL,
  `prossima_fattura` date DEFAULT NULL,
  `contratto_orario` int DEFAULT '0',
  `ore` int DEFAULT '0',
  `costo_orario` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.distinta_base
CREATE TABLE IF NOT EXISTS `distinta_base` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `posizione` int DEFAULT '0',
  `id_articolo` int DEFAULT NULL,
  `id_fase_articolo` int DEFAULT '0',
  `id_materiale` int DEFAULT '0',
  `qta` decimal(10,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.do
CREATE TABLE IF NOT EXISTS `do` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_utente` int NOT NULL DEFAULT '0',
  `flusso` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cd_do` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `descrizione` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `attivo` int NOT NULL DEFAULT (0),
  `passivo` int NOT NULL DEFAULT (0),
  `ordinamento` int NOT NULL DEFAULT (0),
  `impegno` int NOT NULL DEFAULT (0),
  `scarico` int NOT NULL DEFAULT (0),
  `carico` int NOT NULL DEFAULT (0),
  `trasferimento` int NOT NULL DEFAULT '0',
  `id_mg_p` int NOT NULL DEFAULT '0',
  `id_mg_a` int NOT NULL DEFAULT '0',
  `fatturazione_ingresso` int NOT NULL DEFAULT '0',
  `fatturazione_uscita` int NOT NULL DEFAULT '0',
  `scan_code` int DEFAULT NULL,
  `ordine` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.dorig
CREATE TABLE IF NOT EXISTS `dorig` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `id_cliente` int DEFAULT NULL,
  `id_dotes` int DEFAULT NULL,
  `numero_doc` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_doc` date DEFAULT NULL,
  `cd_do` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_documento` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_articolo` int DEFAULT NULL,
  `cd_cf` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cd_ar` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'codice articolo',
  `cd_mg_esercizio` year DEFAULT NULL COMMENT 'anno esercizio',
  `qta` decimal(10,3) DEFAULT NULL,
  `prezzo_unitario` double DEFAULT NULL,
  `prezzo_totale` double DEFAULT NULL,
  `prezzo_totale_iva` double DEFAULT NULL,
  `natura` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iva` int DEFAULT NULL,
  `nome_prodotto` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dettagli_prodotto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `n_riga` int DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qta_evasa` int DEFAULT (0),
  `id_dorig_evade` int DEFAULT NULL,
  `lotto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scadenza_lotto` date DEFAULT NULL,
  `um` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'PZ',
  `pu` decimal(10,2) DEFAULT NULL,
  `pt` decimal(10,2) DEFAULT NULL,
  `qta_evadibile_prod` int DEFAULT '0',
  `qta_evasa_prod` int DEFAULT '0',
  `stato_prod` int DEFAULT '0',
  `id_testata` int DEFAULT '0',
  `descrizione` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imposta` decimal(10,3) DEFAULT '0.000',
  `descrizione_imposta` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imponibile` decimal(10,2) DEFAULT NULL,
  `totale` decimal(10,2) DEFAULT NULL,
  `codice_iva` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rif_normativo` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rif_normativo_pdf` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_pratica` int DEFAULT '0',
  `fattura` int DEFAULT '0',
  `fattura_in_ingresso` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.dotes
CREATE TABLE IF NOT EXISTS `dotes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL DEFAULT '0',
  `id_cliente` int NOT NULL DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `id_commessa` int DEFAULT NULL,
  `cd_do` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'codice documento',
  `tipo_documento` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipologia_documento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'TD01',
  `cd_cf` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'codice cliente',
  `numero_doc` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_doc` date DEFAULT NULL,
  `oggetto_visibile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `oggetto_interno` text COLLATE utf8mb4_general_ci,
  `partita_iva` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iban` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_consegna` date DEFAULT NULL,
  `cd_mg_esercizio` year DEFAULT NULL,
  `indirizzo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comune` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ragione_sociale_fatturazione` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo_fatturazione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `partita_iva_fatturazione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ragione_sociale_consegna` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo_consegna` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `costo_totale` double(9,2) DEFAULT NULL,
  `costo_totale_iva` double(9,2) DEFAULT NULL,
  `iva` int DEFAULT NULL,
  `cap` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ragione_sociale` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pec` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qta_totale` int DEFAULT NULL,
  `comune_fatturazione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `provincia_fatturazione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comune_consegna` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imponibile` decimal(10,2) DEFAULT NULL,
  `sconto` double(9,2) DEFAULT NULL,
  `costo_trasporto` double(7,2) DEFAULT NULL,
  `iva_percentuale` int DEFAULT NULL,
  `sconto_percentuale` int DEFAULT NULL,
  `costo_trasporto_percentuale` int DEFAULT NULL,
  `id_dotes_evade` int DEFAULT NULL,
  `modalita_pagamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'MP05',
  `istituto_finanziario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'MP05',
  `stato` int DEFAULT (0),
  `anno` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sdi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `data` date DEFAULT NULL,
  `esigibilita_iva` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nominativo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cf` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `piva` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `citta` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `provincia` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nazione` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'IT',
  `condizioni_pagamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'TP02',
  `divisa` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'EUR',
  `imposta` decimal(10,2) DEFAULT NULL,
  `totale` decimal(10,2) DEFAULT NULL,
  `tipo_ritenuta` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `importo_ritenuta` decimal(10,2) DEFAULT NULL,
  `aliquota_ritenuta` decimal(5,2) DEFAULT NULL,
  `causale_pagamento` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_cassa` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aliquota_cassa` decimal(5,2) DEFAULT NULL,
  `importo_cassa` decimal(10,2) DEFAULT NULL,
  `imponibile_cassa` decimal(10,2) DEFAULT NULL,
  `aliquota_iva_cassa` decimal(5,2) DEFAULT NULL,
  `sconto_maggiorazione` decimal(10,2) DEFAULT NULL,
  `da_registrare` int DEFAULT (1),
  `split_payment` int DEFAULT NULL,
  `errore_testata` text COLLATE utf8mb4_general_ci,
  `allegato` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `allegato2` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome_allegato` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome_allegato2` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fattura_in_ingresso` int DEFAULT '0',
  `nome_file_fattura` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rc_ricezione` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rc_consegna` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ns_codice` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ns_descrizione` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ns_suggerimento` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `saldata` int DEFAULT '0',
  `data_ultimo_sollecito` datetime DEFAULT NULL,
  `numero_solleciti` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=645 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.fasi
CREATE TABLE IF NOT EXISTS `fasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `descrizione` varchar(200) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.fasi_articoli
CREATE TABLE IF NOT EXISTS `fasi_articoli` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `id_fase` int DEFAULT NULL,
  `id_articolo` int DEFAULT NULL,
  `tempo_medio_minuti` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.fatture_righe
CREATE TABLE IF NOT EXISTS `fatture_righe` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_utente` int unsigned NOT NULL DEFAULT '1',
  `id_testata` int DEFAULT '0',
  `descrizione` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `qta` decimal(10,3) DEFAULT NULL,
  `um` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'PZ',
  `pu` decimal(10,2) DEFAULT NULL,
  `pt` decimal(10,2) DEFAULT NULL,
  `iva` int DEFAULT NULL,
  `imposta` decimal(10,3) DEFAULT NULL,
  `imponibile` decimal(10,2) DEFAULT NULL,
  `codice_iva` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `rif_normativo` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `id_pratica` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.fatture_testata
CREATE TABLE IF NOT EXISTS `fatture_testata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_utente` int unsigned NOT NULL DEFAULT '0',
  `id_documento` int unsigned NOT NULL DEFAULT '0',
  `tipologia_documento` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'TD01',
  `divisa` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'EUR',
  `data` date NOT NULL,
  `numero` int DEFAULT '0',
  `anno` int DEFAULT '2022',
  `pec` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sdi` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `nominativo` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  `indirizzo` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cap` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `citta` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `provincia` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `nazione` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'IT',
  `cf` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `piva` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `imponibile` decimal(10,2) DEFAULT '0.00',
  `iva` int DEFAULT '22',
  `imposta` decimal(10,2) DEFAULT NULL,
  `esigibilita_iva` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `condizioni_pagamento` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tipologia_pagamento` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `totale` decimal(10,2) DEFAULT '0.00',
  `sconto_maggiorazione` decimal(10,2) DEFAULT '0.00',
  `stato` int DEFAULT '0',
  `split_payment` int DEFAULT '0',
  `allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `allegato2` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `nome_allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `nome_allegato2` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `errore_testata` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ft_modalita_pagamento
CREATE TABLE IF NOT EXISTS `ft_modalita_pagamento` (
  `codice` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_modifica` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attivo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ft_nature
CREATE TABLE IF NOT EXISTS `ft_nature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `preferito` tinyint(1) DEFAULT '0',
  `aliquota` decimal(5,2) DEFAULT '0.00',
  `descrizione` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descrizione_pdf` text COLLATE utf8mb4_general_ci,
  `natura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ft_regimi_fiscali
CREATE TABLE IF NOT EXISTS `ft_regimi_fiscali` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codice` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ft_ritenute
CREATE TABLE IF NOT EXISTS `ft_ritenute` (
  `codice` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_soggetto` enum('PF','PG','ENTRAMBI') COLLATE utf8mb4_general_ci DEFAULT 'ENTRAMBI',
  `percentuale_default` decimal(5,2) DEFAULT NULL,
  `è_contributo_previdenziale` tinyint(1) DEFAULT '0',
  `conto_contabile` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_modifica` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attivo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ft_tipologia_documento
CREATE TABLE IF NOT EXISTS `ft_tipologia_documento` (
  `codice` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione_breve` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `riferimento_normativo` text COLLATE utf8mb4_general_ci,
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_modifica` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attivo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`codice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.giacenze
CREATE TABLE IF NOT EXISTS `giacenze` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_articolo` int NOT NULL,
  `id_mg` int NOT NULL,
  `lotto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ubicazione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scadenza_lotto` date DEFAULT NULL,
  `qta` decimal(10,2) DEFAULT '0.00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_giacenza` (`id_azienda`,`id_articolo`,`id_mg`,`lotto`,`ubicazione`,`scadenza_lotto`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.lavori
CREATE TABLE IF NOT EXISTS `lavori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL DEFAULT '0',
  `descrizione` text NOT NULL,
  `stato` int DEFAULT (0),
  `scadenza` date DEFAULT NULL,
  `data_chiusura` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `data_archiviazione` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.leads
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT '0',
  `id_assegnazione` int DEFAULT '0',
  `data` date NOT NULL,
  `descrizione` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `totale` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `status` int DEFAULT '0',
  `mail_inviata` int DEFAULT '0',
  `contatto_telefonico` int DEFAULT '0',
  `timeins` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.materiali
CREATE TABLE IF NOT EXISTS `materiali` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `um` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `costo_kg` decimal(10,2) DEFAULT NULL,
  `soglia` double(10,2) DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.mg
CREATE TABLE IF NOT EXISTS `mg` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `codice_magazzino` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descrizione` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `produzione` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.mgmov
CREATE TABLE IF NOT EXISTS `mgmov` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_mg` int DEFAULT NULL,
  `id_odl` int DEFAULT '0',
  `id_riga_odl` int DEFAULT '0',
  `id_dorig` int DEFAULT '0',
  `id_commessa` int DEFAULT '0',
  `datamov` timestamp NULL DEFAULT (now()),
  `causale` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `lotto` varchar(400) DEFAULT NULL,
  `scadenza_lotto` date DEFAULT NULL,
  `id_articolo` int DEFAULT '0',
  `qta` decimal(10,6) DEFAULT NULL,
  `car` int DEFAULT '0',
  `sca` int DEFAULT '0',
  `ret` int DEFAULT '0',
  `ini` int DEFAULT '0',
  KEY `Indice 1` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.moduli
CREATE TABLE IF NOT EXISTS `moduli` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descrizione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `azienda_id` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.movimenti
CREATE TABLE IF NOT EXISTS `movimenti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_fattura` int DEFAULT NULL,
  `id_conto` int DEFAULT NULL,
  `importo` decimal(10,2) NOT NULL,
  `tipo` enum('entrata','uscita') COLLATE utf8mb4_general_ci DEFAULT 'entrata',
  `data_movimento` date DEFAULT NULL,
  `codice_contabile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descrizione` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_fattura` (`id_fattura`),
  KEY `id_conto` (`id_conto`),
  CONSTRAINT `movimenti_ibfk_1` FOREIGN KEY (`id_fattura`) REFERENCES `dotes` (`id`),
  CONSTRAINT `movimenti_ibfk_2` FOREIGN KEY (`id_conto`) REFERENCES `conti` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.odl
CREATE TABLE IF NOT EXISTS `odl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_dotes` int NOT NULL DEFAULT '0',
  `id_dorig` int NOT NULL DEFAULT '0',
  `id_utente` int NOT NULL DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `lotto_produzione` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `commessa` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_articolo` int DEFAULT '0',
  `qta` decimal(10,2) DEFAULT NULL,
  `stato` int DEFAULT '0',
  `note` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `data_chiusura` timestamp NULL DEFAULT NULL,
  `timeins` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Indice 1` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.odl_righe
CREATE TABLE IF NOT EXISTS `odl_righe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_odl` int DEFAULT NULL,
  `id_dorig` int DEFAULT '0',
  `id_fase` int DEFAULT NULL,
  `inizio` timestamp NULL DEFAULT NULL,
  `fine` timestamp NULL DEFAULT NULL,
  `ricetta` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `odl` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `qta` decimal(10,2) DEFAULT NULL,
  `qta_fatta` decimal(10,2) DEFAULT '0.00',
  `qta_iniziale` decimal(14,2) DEFAULT '0.00',
  `qta_finale` decimal(14,2) DEFAULT '0.00',
  `completato` int DEFAULT '0',
  `note` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  KEY `Indice 1` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.preventivi
CREATE TABLE IF NOT EXISTS `preventivi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_lead` int unsigned NOT NULL DEFAULT '0',
  `id_utente` int DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `ordine_di_lavoro` int DEFAULT '0',
  `data` date NOT NULL,
  `descrizione` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `allegato` varchar(50) NOT NULL DEFAULT '',
  `totale` decimal(10,2) DEFAULT '0.00',
  `provvigione` decimal(10,2) DEFAULT '0.00',
  `incassato` decimal(10,2) DEFAULT '0.00',
  `pagato` decimal(10,2) DEFAULT '0.00',
  `canone` decimal(10,2) DEFAULT '0.00',
  `data_canone` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `note` text,
  `timeins` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.progetti
CREATE TABLE IF NOT EXISTS `progetti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_reparto` int NOT NULL DEFAULT '0',
  `id_utente` int NOT NULL DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `titolo` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `descrizione` text,
  `descrizione_ultimo_sal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `id_operatore_ultimo_sal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `timestamp_ultimo_sal` timestamp NULL DEFAULT NULL,
  `timestamp_prossimo_sal` timestamp NULL DEFAULT NULL,
  `id_assegnatario` int DEFAULT NULL,
  `status` int DEFAULT (0),
  `archiviato` int DEFAULT (0),
  `label_ex1` varchar(100) DEFAULT NULL,
  `label_ex2` varchar(100) DEFAULT NULL,
  `label_ex3` varchar(100) DEFAULT NULL,
  `label_ex4` varchar(100) DEFAULT NULL,
  `label_ex5` varchar(100) DEFAULT NULL,
  `label_ex6` varchar(100) DEFAULT NULL,
  `label_ex7` varchar(100) DEFAULT NULL,
  `label_ex8` varchar(100) DEFAULT NULL,
  `label_ex9` varchar(100) DEFAULT NULL,
  `label_ex10` varchar(100) DEFAULT NULL,
  `val_ex1` varchar(100) DEFAULT NULL,
  `val_ex2` varchar(100) DEFAULT NULL,
  `val_ex3` varchar(100) DEFAULT NULL,
  `val_ex4` varchar(100) DEFAULT NULL,
  `val_ex5` varchar(100) DEFAULT NULL,
  `val_ex6` varchar(100) DEFAULT NULL,
  `val_ex7` varchar(100) DEFAULT NULL,
  `val_ex8` varchar(100) DEFAULT NULL,
  `val_ex9` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `val_ex10` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.progetti_allegati
CREATE TABLE IF NOT EXISTS `progetti_allegati` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `id_progetto` int NOT NULL,
  `nome_allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT (now()),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.reparti
CREATE TABLE IF NOT EXISTS `reparti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `descrizione` varchar(100) DEFAULT NULL,
  `archiviato` int DEFAULT (0),
  `stati` varchar(500) DEFAULT 'In Attesa;In Corso;Fermo;Concluso',
  `colori` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'bg-danger;bg-primary;bg-warning;bg-success',
  `label_ex1` varchar(100) DEFAULT '',
  `label_ex2` varchar(100) DEFAULT '',
  `label_ex3` varchar(100) DEFAULT '',
  `label_ex4` varchar(100) DEFAULT '',
  `label_ex5` varchar(100) DEFAULT '',
  `label_ex6` varchar(100) DEFAULT '',
  `label_ex7` varchar(100) DEFAULT '',
  `label_ex8` varchar(100) DEFAULT '',
  `label_ex9` varchar(100) DEFAULT '',
  `label_ex10` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.reparti_copy
CREATE TABLE IF NOT EXISTS `reparti_copy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `descrizione` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `archiviato` int DEFAULT '0',
  `stati` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'In Attesa;In Corso;Fermo;Concluso',
  `colori` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'bg-danger;bg-primary;bg-warning;bg-success',
  `label_ex1` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex2` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex3` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex4` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex5` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex6` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex7` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex8` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex9` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `label_ex10` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.ruoli
CREATE TABLE IF NOT EXISTS `ruoli` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `titolo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.sal_progetti
CREATE TABLE IF NOT EXISTS `sal_progetti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_progetto` int DEFAULT NULL,
  `descrizione_ultimo_sal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `id_operatore_ultimo_sal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `timestamp_ultimo_sal` timestamp NULL DEFAULT NULL,
  `timestamp_prossimo_sal` timestamp NULL DEFAULT NULL,
  `id_assegnatario` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `allegato` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=339 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.scadenziario
CREATE TABLE IF NOT EXISTS `scadenziario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_azienda` int DEFAULT NULL,
  `id_cliente` int DEFAULT (0),
  `id_dotes` int NOT NULL,
  `data_scadenza` date NOT NULL,
  `importo` decimal(10,2) NOT NULL,
  `termini` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `importo_pagato` decimal(10,2) DEFAULT '0.00',
  `tipo_movimento` enum('entrata','uscita') COLLATE utf8mb4_general_ci NOT NULL,
  `modalita_pagamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iban` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stato` enum('da_pagare','parziale','pagato') COLLATE utf8mb4_general_ci DEFAULT 'da_pagare',
  `note` text COLLATE utf8mb4_general_ci,
  `data_ultimo_sollecito` datetime DEFAULT NULL,
  `numero_solleciti` int DEFAULT '0',
  `data_pagamento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tracking_id` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_aperta` tinyint(1) DEFAULT '0',
  `data_apertura_email` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.task
CREATE TABLE IF NOT EXISTS `task` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int DEFAULT NULL,
  `id_azienda` int DEFAULT NULL,
  `id_lavoro` int NOT NULL DEFAULT '0',
  `id_dipendente` int NOT NULL DEFAULT '0',
  `allegato` varchar(250) DEFAULT NULL,
  `descrizione` text,
  `stato` int DEFAULT (1),
  `created_at` date DEFAULT NULL,
  `data_assegnazione` date DEFAULT NULL,
  `data_chiusura` date DEFAULT NULL,
  `data_sospensione` date DEFAULT NULL,
  `note_chiusura` text,
  `note_sospensione` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.utenti
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_agente` int NOT NULL DEFAULT '0',
  `id_azienda` int DEFAULT NULL,
  `id_reparto` int DEFAULT NULL,
  `id_sezione` int DEFAULT NULL,
  `super_admin` int DEFAULT (0),
  `admin_azienda` int DEFAULT NULL,
  `immagine` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '/default/assets/images/users/user-dummy-img.jpg',
  `nome` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cognome` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_nascita` date DEFAULT NULL,
  `luogo_nascita` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `piva` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ragione_sociale` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cciaa` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rea` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `telefono` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `indirizzo` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cap` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `comune` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `provincia` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `regione` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fatturato` int DEFAULT NULL,
  `dipendenti` int DEFAULT (0),
  `ateco_codice` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ateco_descrizione` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `grandezza_azienda` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cf` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `sdi` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pec` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_recapito` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_leads` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `referente` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono_referente` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_tipologia` int DEFAULT '0',
  `verification_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_recupero_password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `abilitato` int DEFAULT '1',
  `accesso_inviato` int DEFAULT '0',
  `timeins` datetime DEFAULT CURRENT_TIMESTAMP,
  `token_utente_per_bando` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `onesignal_token` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `onesignal_token_mobile` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella gestya.it.utenti_allegati
CREATE TABLE IF NOT EXISTS `utenti_allegati` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `nome_allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `allegato` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT (now()),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- L’esportazione dei dati non era selezionata.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
