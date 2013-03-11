-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 11 Mars 2013 à 20:53
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

-- --------------------------------------------------------

--
-- Structure de la table `admissibles`
--

CREATE TABLE IF NOT EXISTS `admissibles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(50) NOT NULL,
  `PRENOM` varchar(50) NOT NULL,
  `SEXE` varchar(1) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `SERIE` int(1) NOT NULL,
  `ID_FILIERE` int(11) NOT NULL,
  `ID_ETABLISSEMENT` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ref_sexes_admissibles_fk` (`SEXE`),
  KEY `ref_etablissements_admissibles_fk` (`ID_ETABLISSEMENT`),
  KEY `ref_filiaires_admissibles_fk` (`ID_FILIERE`),
  KEY `series_admissibles_fk` (`SERIE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Tables contenant les admissibles ˆ l''Žcole polytechnique' AUTO_INCREMENT=6 ;

--
-- Contenu de la table `admissibles`
--

INSERT INTO `admissibles` (`ID`, `NOM`, `PRENOM`, `SEXE`, `ADRESSE_MAIL`, `SERIE`, `ID_FILIERE`, `ID_ETABLISSEMENT`) VALUES
(5, 'espinet', 'francois', 'M', 'nicolasgrorod@hotmail.com', 2, 1, 95);

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE IF NOT EXISTS `annonces` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  `TELEPHONE` varchar(50) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `VALIDATION` tinyint(1) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ADRESSE` varchar(250) NOT NULL,
  `ID_CATEGORIE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `annonces`
--

INSERT INTO `annonces` (`ID`, `NOM`, `TELEPHONE`, `DESCRIPTION`, `VALIDATION`, `ADRESSE_MAIL`, `ADRESSE`, `ID_CATEGORIE`) VALUES
(6, 'HÃ´tel le Beau Regard', '06 56 87 75 65', 'Magnifique !', 1, 'nicolas@rasmotte.com', '34 rue mes glios', 7);

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE IF NOT EXISTS `demandes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_ADMISSIBLE` int(11) NOT NULL,
  `USER_X` varchar(150) NOT NULL,
  `LIEN` varchar(250) DEFAULT NULL COMMENT 'Lien en get (code unique)\r\n',
  `ID_STATUS` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `statuts_demandes_fk` (`ID_STATUS`),
  KEY `x_demandes_fk` (`USER_X`),
  KEY `admissibles_demandes_fk` (`ID_ADMISSIBLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `demandes`
--

INSERT INTO `demandes` (`ID`, `ID_ADMISSIBLE`, `USER_X`, `LIEN`, `ID_STATUS`) VALUES
(2, 5, 'nicolas.grorod', '156c1c9da5122fe4bc0bb7d13c441b78', 2);

-- --------------------------------------------------------

--
-- Structure de la table `disponibilites`
--

CREATE TABLE IF NOT EXISTS `disponibilites` (
  `ID_X` varchar(250) NOT NULL,
  `ID_SERIE` int(11) NOT NULL,
  KEY `ref_series_disponibilites_fk` (`ID_SERIE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ref_categories`
--

CREATE TABLE IF NOT EXISTS `ref_categories` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Référence pour les catégories d''annonces proposées' AUTO_INCREMENT=8 ;

--
-- Contenu de la table `ref_categories`
--

INSERT INTO `ref_categories` (`ID`, `NOM`) VALUES
(7, 'HÃ´tels');

-- --------------------------------------------------------

--
-- Structure de la table `ref_etablissements`
--

CREATE TABLE IF NOT EXISTS `ref_etablissements` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `COMMUNE` varchar(250) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=163 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `ref_filieres`
--

CREATE TABLE IF NOT EXISTS `ref_filieres` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `INTITULE` varchar(100) NOT NULL,
  `DATE_DEBUT` int(11) NOT NULL,
  `DATE_FIN` int(11) NOT NULL,
  `OUVERTURE` int(11) NOT NULL,
  `FERMETURE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `series`
--

INSERT INTO `series` (`ID`, `INTITULE`, `DATE_DEBUT`, `DATE_FIN`, `OUVERTURE`, `FERMETURE`) VALUES
(2, 'SÃ©rie 1', 1370995200, 1371513600, 1363034800, 1370995200);

-- --------------------------------------------------------

--
-- Structure de la table `statuts`
--

CREATE TABLE IF NOT EXISTS `statuts` (
  `ID` int(11) NOT NULL,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `statuts`
--

INSERT INTO `statuts` (`ID`, `NOM`) VALUES
(0, 'En cours de validation par l''admissible'),
(1, 'En attente d''acceptation'),
(2, 'Validée'),
(3, 'Annulée');

-- --------------------------------------------------------

--
-- Structure de la table `x`
--

CREATE TABLE IF NOT EXISTS `x` (
  `USER` varchar(150) NOT NULL COMMENT 'Issue du ldap',
  `SEXE` varchar(1) NOT NULL,
  `SECTION` varchar(250) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ID_FILIERE` int(11) NOT NULL,
  `PROMOTION` varchar(100) NOT NULL,
  `ID_ETABLISSEMENT` int(11) NOT NULL,
  PRIMARY KEY (`USER`),
  KEY `ref_sections_x_fk` (`SECTION`),
  KEY `ref_promotions_x_fk` (`PROMOTION`),
  KEY `ref_sexes_x_fk` (`SEXE`),
  KEY `ref_etablissements_x_fk` (`ID_ETABLISSEMENT`),
  KEY `ref_filiaires_x_fk` (`ID_FILIERE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `x`
--

INSERT INTO `x` (`USER`, `SEXE`, `SECTION`, `ADRESSE_MAIL`, `ID_FILIERE`, `PROMOTION`, `ID_ETABLISSEMENT`) VALUES
('nicolas.gro', 'M', '2', 'LDAP@poly.edu', 1, '1', 103),
('nicolas.grorod', 'M', 'Escalade', 'nicolas.grorod@polytechnique.edu', 1, '2011', 104),
('nicolas.grorot', 'M', '1', 'LDAP@poly.edu', 1, '1', 104);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `admissibles`
--
ALTER TABLE `admissibles`
  ADD CONSTRAINT `ref_etablissements_admissibles_fk` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `ref_etablissements` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_filieres_admissibles_fk` FOREIGN KEY (`ID_FILIERE`) REFERENCES `ref_filieres` (`ID`);

--
-- Contraintes pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `admissibles_demandes_fk` FOREIGN KEY (`ID_ADMISSIBLE`) REFERENCES `admissibles` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `statuts_demandes_fk` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuts` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `x_demandes_fk` FOREIGN KEY (`USER_X`) REFERENCES `x` (`USER`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `disponibilites`
--
ALTER TABLE `disponibilites`
  ADD CONSTRAINT `ref_series_disponibilites_fk` FOREIGN KEY (`ID_SERIE`) REFERENCES `series` (`ID`);

--
-- Contraintes pour la table `x`
--
ALTER TABLE `x`
  ADD CONSTRAINT `ref_etablissements_x_fk` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `ref_etablissements` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_filieres_x_fk` FOREIGN KEY (`ID_FILIERE`) REFERENCES `ref_filieres` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
