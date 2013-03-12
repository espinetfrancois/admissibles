-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 12 Mars 2013 à 09:21
-- Version du serveur: 5.5.25
-- Version de PHP: 5.4.4

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
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
-- Création: Mar 12 Mars 2013 à 08:06
--

DROP TABLE IF EXISTS `admissibles`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tables contenant les admissibles à l''École polytechnique' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--
-- Création: Lun 11 Mars 2013 à 18:39
--

DROP TABLE IF EXISTS `annonces`;
CREATE TABLE IF NOT EXISTS `annonces` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  `TELEPHONE` varchar(50) NOT NULL,
  `DESCRIPTION` mediumtext NOT NULL,
  `VALIDATION` tinyint(1) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ADRESSE` varchar(250) NOT NULL,
  `ID_CATEGORIE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--
-- Création: Mar 12 Mars 2013 à 08:01
--

DROP TABLE IF EXISTS `demandes`;
CREATE TABLE IF NOT EXISTS `demandes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_ADMISSIBLE` int(11) NOT NULL,
  `USER_X` varchar(150) CHARACTER SET utf8 NOT NULL,
  `LIEN` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Lien en get (code unique)',
  `ID_STATUS` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `statuts_demandes_fk` (`ID_STATUS`),
  KEY `x_demandes_fk` (`USER_X`),
  KEY `admissibles_demandes_fk` (`ID_ADMISSIBLE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `disponibilites`
--
-- Création: Mar 12 Mars 2013 à 08:02
--

DROP TABLE IF EXISTS `disponibilites`;
CREATE TABLE IF NOT EXISTS `disponibilites` (
  `ID_X` varchar(250) NOT NULL,
  `ID_SERIE` int(11) NOT NULL,
  KEY `ref_series_disponibilites_fk` (`ID_SERIE`),
  KEY `ID_X` (`ID_X`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ref_categories`
--
-- Création: Lun 11 Mars 2013 à 18:40
--

DROP TABLE IF EXISTS `ref_categories`;
CREATE TABLE IF NOT EXISTS `ref_categories` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Référence pour les catégories d''annonces proposées' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_etablissements`
--
-- Création: Lun 11 Mars 2013 à 18:40
--

DROP TABLE IF EXISTS `ref_etablissements`;
CREATE TABLE IF NOT EXISTS `ref_etablissements` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  `COMMUNE` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_filieres`
--
-- Création: Lun 11 Mars 2013 à 18:40
--

DROP TABLE IF EXISTS `ref_filieres`;
CREATE TABLE IF NOT EXISTS `ref_filieres` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_statuts`
--
-- Création: Lun 11 Mars 2013 à 18:40
--

DROP TABLE IF EXISTS `ref_statuts`;
CREATE TABLE IF NOT EXISTS `ref_statuts` (
  `ID` int(11) NOT NULL,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--
-- Création: Lun 11 Mars 2013 à 18:40
--

DROP TABLE IF EXISTS `series`;
CREATE TABLE IF NOT EXISTS `series` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `INTITULE` varchar(100) NOT NULL,
  `DATE_DEBUT` int(11) NOT NULL,
  `DATE_FIN` int(11) NOT NULL,
  `OUVERTURE` int(11) NOT NULL,
  `FERMETURE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `x`
--
-- Création: Mar 12 Mars 2013 à 08:05
--

DROP TABLE IF EXISTS `x`;
CREATE TABLE IF NOT EXISTS `x` (
  `USER` varchar(150) NOT NULL COMMENT 'Issue du ldap',
  `SEXE` varchar(1) NOT NULL,
  `SECTION` varchar(150) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ID_FILIERE` int(11) NOT NULL,
  `PROMOTION` int(11) NOT NULL,
  `ID_ETABLISSEMENT` int(11) NOT NULL,
  PRIMARY KEY (`USER`),
  UNIQUE KEY `USER` (`USER`),
  KEY `ref_sections_x_fk` (`SECTION`),
  KEY `ref_promotions_x_fk` (`PROMOTION`),
  KEY `ref_sexes_x_fk` (`SEXE`),
  KEY `ref_etablissements_x_fk` (`ID_ETABLISSEMENT`),
  KEY `ref_filiaires_x_fk` (`ID_FILIERE`),
  KEY `USER_2` (`USER`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD CONSTRAINT `demandes_ibfk_1` FOREIGN KEY (`USER_X`) REFERENCES `x` (`USER`),
  ADD CONSTRAINT `statuts_demandes_fk` FOREIGN KEY (`ID_STATUS`) REFERENCES `ref_statuts` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `disponibilites`
--
ALTER TABLE `disponibilites`
  ADD CONSTRAINT `disponibilites_ibfk_1` FOREIGN KEY (`ID_X`) REFERENCES `x` (`USER`),
  ADD CONSTRAINT `ref_series_disponibilites_fk` FOREIGN KEY (`ID_SERIE`) REFERENCES `series` (`ID`);

--
-- Contraintes pour la table `x`
--
ALTER TABLE `x`
  ADD CONSTRAINT `ref_etablissements_x_fk` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `ref_etablissements` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_filieres_x_fk` FOREIGN KEY (`ID_FILIERE`) REFERENCES `ref_filieres` (`ID`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
