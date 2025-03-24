-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 24, 2025 alle 11:32
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sodalidas_quaerito`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `articoli`
--

CREATE TABLE `articoli` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `sinossi` text NOT NULL,
  `testo` text NOT NULL,
  `immagine` varchar(255) DEFAULT NULL,
  `autore_id` int(11) NOT NULL,
  `data_pubblicazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `articoli`
--

INSERT INTO `articoli` (`id`, `titolo`, `sinossi`, `testo`, `immagine`, `autore_id`, `data_pubblicazione`) VALUES
(2, 'Gli eventi OMEGA', 'Breve descrizione degli eventi OMEGA e delle loro conseguenze', '<p><strong>Cosa &egrave; un evento OMEGA</strong></p>\r\n<p dir=\"ltr\">Un evento OMEGA &egrave; un fenomeno inspiegabile che sfida le leggi conosciute della fisica. Questi eventi si manifestano in modi diversi in tutto il pianeta. Possono essere anomalie temporali, distorsioni spaziali, fenomeni psicologici collettivi, oggetti o persone con propriet&agrave; inspiegabili. Gli eventi OMEGA sono classificati secondo una scala che ne misura l&rsquo;intensit&agrave; e l&rsquo;impatto sulla societ&agrave;, convenzionalmente indicata come <strong>coefficiente di liminalit&agrave;</strong> e con valori compresi tra 1 e 5. Un luogo dove il ripetuto lancio di una moneta d&agrave; sempre e solo un singolo esito, ad esempio, &egrave; considerato un evento OMEGA di classe 1.<br>Tali fenomeni sono oggetto di indagine, catalogazione e contenimento da parte di una pletora di organizzazioni internazionali e paranazionali, che nascondono attivamente il loro operato alla popolazione mondiale poich&eacute; gli effetti di questi eventi possono rappresentare una seria minaccia per la sicurezza e la salute pubblica.</p>\r\n<p dir=\"ltr\"><strong>Gli eventi OMEGA sono la prova dell&rsquo;esistenza di vita intelligente oltre all&rsquo;essere umano?</strong></p>\r\n<p dir=\"ltr\">Non possiamo affermarlo con certezza, n&eacute; escluderlo completamente.<br>Secondo la teoria delle stringhe, il nostro universo e il tessuto spazio-tempo che lo compone, per essere spiegato, richiede pi&ugrave; di 10 dimensioni, il che apre alla possibilit&agrave; che gli eventi OMEGA siano manifestazioni di dimensioni aggiuntive o alternative ancora non comprese dalla scienza convenzionale. Questi fenomeni potrebbero essere, quindi, semplici fluttuazioni extradimensionali che interagiscono con le 4 dimensioni che siamo in grado di esperire, ma nessuno pu&ograve; attualmente dirlo con certezza.<br>&Egrave;, tuttavia, possibile che gli eventi OMEGA siano anche il risultato di interazioni con esseri provenienti da altri mondi. Queste ipotetiche entit&agrave; &ldquo;altre&rdquo; potrebbero aver trovato il modo di attraversare l&rsquo;enormit&agrave; del cosmo o di travalicare i vincoli delle barriere dimensionali. Gli innumerevoli avvistamenti di UFO e UAP in concomitanza del verificarsi di eventi OMEGA potrebbero avallare questa teoria. <br>Qualcuno, all&rsquo;interno dei gruppi di investigazione pi&ugrave; radicali, ipotizza che potrebbe addirittura trattarsi dell&rsquo;umanit&agrave; stessa, che in un futuro non troppo lontano ha iniziato, o sta per iniziare, a interagire con i fenomeni liminali, rendendo gli eventi OMEGA il risultato di esperimenti o evoluzioni tecnologiche che in quella linea temporale stanno spingendo la nostra razza oltre i propri limiti.<br>Attualmente non esiste nessuna spiegazione rigorosa per il verificarsi degli eventi OMEGA, n&eacute; per i loro effetti. Nessuno &egrave; in grado di prevedere la loro comparsa o il loro impatto, relegando i pochi a conoscenza del fenomeno a poco pi&ugrave; che testimoni, chiamati il pi&ugrave; delle volte a opporsi a una minaccia totalmente ignota.</p>\r\n<p dir=\"ltr\"><strong id=\"docs-internal-guid-ce6ff50f-7fff-5fd0-c284-834285ecc021\">Perch&eacute; non si verificano pi&ugrave; eventi OMEGA di impatto mondiale?</strong></p>\r\n<p dir=\"ltr\">Sono ormai trascorsi 40 anni dall&rsquo;ultimo evento OMEGA di rilevanza planetaria documentato, avvenuto nell&rsquo;autunno del 1984. Tale evento, la cui natura e dettagli rimangono tuttora secretati, rappresenta l&rsquo;ultima manifestazione di un fenomeno che ha sfidato le leggi della fisica e la comprensione scientifica convenzionale su scala cos&igrave; grande. Dopo quel momento si sono verificate altre anomalie che, tuttavia, non hanno messo in pericolo centri abitati o larghe fette della popolazione. Da allora, le Agenzie e gli Istituti di Ricerca, operanti sotto l&rsquo;egida della misteriosa Lighthouse - organismo sovranazionale istituito dall&rsquo;ONU per affrontare tali anomalie - sono rimaste dormienti e tra le loro fila si sono avvicendate quasi due generazioni di agenti. Sebbene abbiano continuato a prepararsi per un eventuale ritorno degli eventi OMEGA di classe planetaria, la maggior parte del personale attualmente in servizio ha affrontato in prima persona solo fenomeni a basso coefficiente di liminalit&agrave;.<br>Lighthouse ha, nel corso degli anni, condiviso con gli agenti informazioni sugli eventi passati con estrema cautela e parsimonia, ritenendo tale divulgazione rischiosa tanto per la sicurezza globale quanto per la comprensione stessa del personale coinvolto. I report e i dati sono stati deliberatamente frammentati e compartimentati, lasciando cos&igrave; molte domande senza risposta anche tra coloro che operano all&rsquo;interno della stessa organizzazione.</p>\r\n<p dir=\"ltr\"><strong>Gli eventi OMEGA possono essere previsti?</strong></p>\r\n<p>Alcuni ricercatori hanno in passato ipotizzato che gli eventi OMEGA seguano un ciclo preciso, per quanto difficile da prevedere. Tali fenomeni non si verificherebbero in modo completamente casuale, ma piuttosto come parte di una sequenza ricorrente che cresce progressivamente in intensit&agrave;. Ogni ciclo potrebbe iniziare con eventi minori per poi culminare in manifestazioni di enorme portata capaci di destabilizzare intere aree del pianeta. Il ciclo sarebbe simile a una molla compressa, che accumula energia fino a un momento di rilascio, o potrebbe assomigliare a ci&ograve; che avviene con i terremoti, dove scosse premonitrici anticipano il cataclisma principale. Se confermata, questa teoria suggerirebbe che il lungo periodo di quiete attuale altro non sia stato che una fase di preludio a un futuro evento dalle conseguenze potenzialmente devastanti. La scarsit&agrave; di dati e la segretezza che circonda la natura di questi fenomeni impediscono di validare definitivamente questa ipotesi, ma pi&ugrave; di una delle organizzazioni che opera sotto l&rsquo;egida della Lighthouse sta cercando prove concrete a favore di questa teoria, colloquialmente indicata come <strong>Lo Schema</strong>.</p>', 'omega.jpg', 1, '2025-03-24 08:40:12');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `foto_profilo` varchar(255) DEFAULT NULL,
  `ruolo` enum('utente','admin') NOT NULL DEFAULT 'utente',
  `stato` enum('attivo','sospeso','disattivato') NOT NULL DEFAULT 'attivo',
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `username`, `password_hash`, `email`, `nome`, `cognome`, `foto_profilo`, `ruolo`, `stato`, `data_creazione`) VALUES
(1, 'ottavio.caetani', '$2y$10$iBT55AleYWRAM7gNRRWi1.2qXhRHiKGgEtwN2nNkrWMu6XZ.NV/KS', 'emanuele.izzo_1998@outlook.it', 'Ottavio', 'Caetani', 'ottavio_caetani.jpg', 'admin', 'attivo', '2025-03-20 11:18:50'),
(2, 'Dante.Bellini', '$2y$10$2mNRBP9IKIhWqW8HVC//a.yB3rTh6kG1S4dsuRGJeqC6/gStEift.', 'edoardo.chiappa94@gmail.com', 'Dante', 'Bellini', 'dante_bellini.jpg', 'utente', 'attivo', '2025-03-21 08:06:47'),
(4, 'Mateo.Fernandéz', '$2y$10$XV.PEaaZzx8VpJWBESsby.yhH46tU7lM/vM2MOW8nIEdn7CW3zP2e', 'manuel.battista@gmail.com', 'Mateo', 'Fernàndez', 'default.jpg', 'utente', 'attivo', '2025-03-21 08:11:05'),
(5, 'Bernadette.Kerzau', '$2y$10$vMssVVi0E8HpYmqQhUh5J.ZGh9h23v1ntwNMkgP1Hc8rmQNg1mPdW', 'chronicle1093@gmail.com', 'Bernadette', 'Kerzau', 'default.jpg', 'utente', 'attivo', '2025-03-21 08:12:11'),
(6, 'Rosario.De La Cruz', '$2y$10$VVvxa4H1w43nk7QS1lM0OeiDX/kt2h8A3duO5AgszbBDeuTdV6mrO', 'digiluca@gmail.com', 'Rosario', 'De La Cruz', 'rosario_de_la_cruz.jpg', 'utente', 'attivo', '2025-03-21 08:13:35'),
(7, 'Malcom.Rhodes', '$2y$10$rrXRg04zjYIPBJiaX0.bpuLwW0Yg3xETLjw9lDV8sYBpQ3/SbvUa6', 'gentilid1@hotmail.it', 'Malcom', 'Rhodes', 'default.jpg', 'utente', 'attivo', '2025-03-21 08:14:08'),
(8, 'Gabriella.Castellani', '$2y$10$SJ3WVHJI/r2ypjbH0tiuOu1sxcon3pwkyuAMOrTQi2ZqCrhKVz3sq', 'delrecamilla@gmail.com', 'Gabriella', 'Castellani', 'default.jpg', 'utente', 'attivo', '2025-03-21 08:14:26');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `articoli`
--
ALTER TABLE `articoli`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autore_id` (`autore_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `articoli`
--
ALTER TABLE `articoli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `articoli`
--
ALTER TABLE `articoli`
  ADD CONSTRAINT `articoli_ibfk_1` FOREIGN KEY (`autore_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
