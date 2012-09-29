-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 29 Septembre 2012 à 22:13
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `admissible_dsi`
--

-- --------------------------------------------------------

--
-- Structure de la table `administration`
--

CREATE TABLE IF NOT EXISTS `administration` (
  `ID` varchar(250) NOT NULL,
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
  `ID_FILIAIRE` int(11) NOT NULL,
  `ID_ETABLISSEMENT` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ref_sexes_admissibles_fk` (`SEXE`),
  KEY `ref_etablissements_admissibles_fk` (`ID_ETABLISSEMENT`),
  KEY `ref_filiaires_admissibles_fk` (`ID_FILIAIRE`),
  KEY `series_admissibles_fk` (`SERIE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tables contenant les admissibles ˆ l''Žcole polytechnique' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE IF NOT EXISTS `annonces` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  `RANG` int(11) DEFAULT NULL COMMENT 'Rang d''affichage sur la page',
  `TELEPHONE` int(11) NOT NULL,
  `DESCRIPTION` varchar(250) NOT NULL,
  `VALIDATION` tinyint(1) NOT NULL,
  `ADRESSE_MAIL` varchar(250) NOT NULL,
  `ADRESSE` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
-- Structure de la table `ref_etablissements`
--

CREATE TABLE IF NOT EXISTS `ref_etablissements` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  `COMMUNE` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_filiaires`
--

CREATE TABLE IF NOT EXISTS `ref_filiaires` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOM` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ref_promotions`
--

CREATE TABLE IF NOT EXISTS `ref_promotions` (
  `ID` int(11) NOT NULL,
  `ANNEE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ref_sections`
--

CREATE TABLE IF NOT EXISTS `ref_sections` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LIBELLE` varchar(250) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NUMERO` int(11) NOT NULL,
  `DATE_DEBUT` datetime NOT NULL,
  `DATE_FIN` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `statuts`
--

CREATE TABLE IF NOT EXISTS `statuts` (
  `ID` int(11) NOT NULL,
  `LIBELLE` varchar(250) NOT NULL,
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
  `DISPO_S1` int(1) NOT NULL,
  `DISPO_S2` int(1) NOT NULL,
  `DISPO_S3` int(1) NOT NULL,
  `DISPO_S4` int(1) NOT NULL,
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
  ADD CONSTRAINT `ref_filiaires_admissibles_fk` FOREIGN KEY (`ID_FILIAIRE`) REFERENCES `ref_filiaires` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `admissibles_demandes_fk` FOREIGN KEY (`ID_ADMISSIBLE`) REFERENCES `admissibles` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `statuts_demandes_fk` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuts` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `x_demandes_fk` FOREIGN KEY (`USER_X`) REFERENCES `x` (`USER`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `x`
--
ALTER TABLE `x`
  ADD CONSTRAINT `ref_etablissements_x_fk` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `ref_etablissements` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_filiaires_x_fk` FOREIGN KEY (`ID_FILIERE`) REFERENCES `ref_filiaires` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_promotions_x_fk` FOREIGN KEY (`ID_PROMOTION`) REFERENCES `ref_promotions` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ref_sections_x_fk` FOREIGN KEY (`ID_SECTION`) REFERENCES `ref_sections` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;