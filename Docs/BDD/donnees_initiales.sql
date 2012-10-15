-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 15 Octobre 2012 à 21:19
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `admissible_dsi`
--

--
-- Contenu de la table `administration`
--

INSERT INTO `administration` (`ID`, `PARAMETRE`, `VALEUR`) VALUES
(0, 'administrateur', 'nicolas.grorod');

--
-- Contenu de la table `ref_categories`
--

INSERT INTO `ref_categories` (`ID`, `NOM`) VALUES
(7, 'HÃ´tels');

--
-- Contenu de la table `ref_etablissements`
--

INSERT INTO `ref_etablissements` (`ID`, `NOM`, `COMMUNE`) VALUES
(95, 'Berthollet', 'ANNECY'),
(96, 'Montaigne', 'BORDEAUX'),
(97, 'Malherbe', 'CAEN'),
(98, 'Victor Grignard', 'CHERBOURG'),
(99, 'Carnot', 'DIJON'),
(100, 'Champollion', 'GRENOBLE'),
(101, 'Schweitzer', 'LE RAINCY'),
(102, 'Faidherbe', 'LILLE'),
(103, 'La MartiniÃ¨re Monplaisir', 'LYON'),
(104, 'Le Parc', 'LYON'),
(105, 'Les Lazaristes', 'LYON'),
(106, 'Thiers', 'MARSEILLE'),
(107, ' Fabert', 'METZ'),
(108, 'Henri PoincarÃ©', 'NANCY'),
(109, 'Clemenceau', 'NANTES'),
(110, 'Pasteur', 'NEUILLY'),
(111, 'Massena', 'NICE'),
(112, 'Pothier', 'ORLEANS'),
(113, 'Blaise Pascal', 'ORSAY'),
(114, 'Chaptal', 'PARIS'),
(115, 'Charlemagne', 'PARIS'),
(116, 'Condorcet', 'PARIS'),
(117, 'Fenelon', 'PARIS'),
(118, 'Fenelon Sainte Marie', 'PARIS'),
(119, 'Henri IV', 'PARIS'),
(120, 'Janson de Sailly', 'PARIS'),
(121, 'Louis le Grand', 'PARIS'),
(122, 'Saint Louis', 'PARIS'),
(123, 'Stanislas', 'PARIS'),
(124, 'Louis Barthou', 'PAU'),
(125, 'Camille Guerin', 'POITIER'),
(126, 'Chateaubriand', 'RENNES'),
(127, 'Corneille', 'ROUEN'),
(128, 'Marcelin Berthelot', 'SAINT MAUR DES FOSSES'),
(129, 'Leconte de Lisle', 'SAINTE CLOTHILDE'),
(130, 'Jean Baptiste Corot', 'SAVIGNY SUR ORGE'),
(131, 'Lakanal', 'SCEAUX'),
(132, 'Valbonne', 'SOPHIA ANTIPOLIS'),
(133, 'Kleber', 'STRASBOURG'),
(134, 'Bellevue', 'TOULOUSE'),
(135, 'Pierre de Fermat', 'TOULOUSE'),
(136, 'Descartes', 'TOURS'),
(137, 'Michelet', 'VANVES'),
(138, 'Hoche', 'VERSAILLES'),
(139, 'Sainte Genevieve', 'VERSAILLES'),
(140, 'Joffre', 'MONPELLIER'),
(141, 'UniversitÃ© de Nice', 'NICE'),
(148, 'UniversitÃ©', 'TOULOUSE'),
(149, 'UniversitÃ©', 'PARIS'),
(150, 'UniversitÃ©', 'LYON'),
(151, 'UniversitÃ©', 'MARSEILLE'),
(152, 'UniversitÃ©', 'VERSAILLES'),
(153, 'UniversitÃ©', 'BATH'),
(154, 'UniversitÃ©', 'BORDEAUX'),
(155, 'UniversitÃ©', 'MUNICH'),
(156, 'UniversitÃ©', 'LILLE'),
(157, 'UniversitÃ©', 'MONTREAL'),
(158, 'UniversitÃ©', 'CERGY PONTOISE'),
(159, 'UniversitÃ©', 'MONPELLIER'),
(160, 'UniversitÃ©', 'AVIGNON'),
(161, 'UniversitÃ©', 'TOULOUSE'),
(162, 'UniversitÃ©', 'METZ');

--
-- Contenu de la table `ref_filieres`
--

INSERT INTO `ref_filieres` (`ID`, `NOM`) VALUES
(1, 'MPI'),
(2, 'MPSI'),
(3, 'PC'),
(4, 'PSI'),
(5, 'PT'),
(6, 'TSI'),
(7, 'Universitaire');

--
-- Contenu de la table `ref_promotions`
--

INSERT INTO `ref_promotions` (`ID`, `NOM`) VALUES
(1, '2011'),
(4, '2012');

--
-- Contenu de la table `ref_sections`
--

INSERT INTO `ref_sections` (`ID`, `NOM`) VALUES
(1, 'Escalade'),
(2, 'Aviron'),
(3, 'Basket'),
(4, 'Boxe'),
(5, 'Equitation'),
(6, 'Escrime'),
(7, 'Football'),
(8, 'Handball'),
(9, 'Judo'),
(10, 'Natation'),
(11, 'Raid'),
(12, 'Rugby'),
(13, 'Tennis'),
(14, 'Volley'),
(15, 'Badminton');

--
-- Contenu de la table `statuts`
--

INSERT INTO `statuts` (`ID`, `NOM`) VALUES
(0, 'En cours de validation par l''admissible'),
(1, 'En attente d''acceptation'),
(2, 'Validée'),
(3, 'Annulée');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
