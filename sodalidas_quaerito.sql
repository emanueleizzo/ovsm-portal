-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 21, 2025 alle 12:37
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
