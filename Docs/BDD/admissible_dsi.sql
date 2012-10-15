-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 15 Octobre 2012 à 19:59
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
-- Structure de la table `administration`
--

CREATE TABLE IF NOT EXISTS `administration` (
  `ID` int(11) NOT NULL,
  `PARAMETRE` varchar(250) NOT NULL,
  `VALEUR` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Tables contenant les admissibles ˆ l''Žcole polytechnique' AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `ref_etablissements`
--

CREATE TABLE IF NOT EXISTS `ref_etablissements` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `COMMUNE` varchar(250) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_filieres`
--

CREATE TABLE IF NOT EXISTS `ref_filieres` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_promotions`
--

CREATE TABLE IF NOT EXISTS `ref_promotions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_sections`
--

CREATE TABLE IF NOT EXISTS `ref_sections` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `statuts`
--

CREATE TABLE IF NOT EXISTS `statuts` (
  `ID` int(11) NOT NULL,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `x`
--

CREATE TABLE IF NOT EXISTS `x` (
  `USER` varchar(150) NOT NULL COMMENT 'Issue du ldap',
  `SEXE` varchar(1) NOT NULL,
  `ID_SECTION` int(11) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ID_FILIERE` int(11) NOT NULL,
  `ID_PROMOTION` int(11) NOT NULL,
  `ID_ETABLISSEMENT` int(11) NOT NULL,
  PRIMARY KEY (`USER`),
  KEY `ref_sections_x_fk` (`ID_SECTION`),
  KEY `ref_promotions_x_fk` (`ID_PROMOTION`),
  KEY `ref_sexes_x_fk` (`SEXE`),
  KEY `ref_etablissements_x_fk` (`ID_ETABLISSEMENT`),
  KEY `ref_filiaires_x_fk` (`ID_FILIERE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  ADD CONSTRAINT `ref_filieres_x_fk` FOREIGN KEY (`ID_FILIERE`) REFERENCES `ref_filieres` (`ID`),
  ADD CONSTRAINT `ref_promotions_x_fk` FOREIGN KEY (`ID_PROMOTION`) REFERENCES `ref_promotions` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_sections_x_fk` FOREIGN KEY (`ID_SECTION`) REFERENCES `ref_sections` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
