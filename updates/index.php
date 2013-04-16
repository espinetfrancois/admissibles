<?php
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
defined('UPDATES_PATH') || define('UPDATES_PATH', ROOT_PATH.'/updates');

//ajout du fichier de lib
require_once(UPDATES_PATH.'/PHPGit/Repository.php');
define('HOME_DIR_PATH', UPDATES_PATH.'/home');

try {
    $repo = new PHPGit_Repository(ROOT_PATH);
    if (!is_dir(HOME_DIR_PATH))
        mkdir(HOME_DIR_PATH);

    putenv('HOME='.HOME_DIR_PATH);
} catch (InvalidGitRepositoryDirectoryException $e) {
    //le dépot n'est pas initialisé
//     try {
//         define('TEMP_DIR_PATH', UPDATES_PATH.'/tempdir');
//         mkdir(TEMP_DIR_PATH);
//         //initialisation du dépot
// //         shell_exec("cd ".TEMP_DIR_PATH." && git init");
//         $repo = PHPGit_Repository::create(TEMP_DIR_PATH);
//         $conf = $repo->getConfiguration();
//         //ajout du proxy
//         shell_exec("cd ".TEMP_DIR_PATH);
//         $conf->set("http.proxy", "http://kuzh.polytechnique.fr:8080/");
//         //url du repo
//         $repo->git("remote add origin https://github.com/espinetfrancois/admissibles.git");
//         //récupération de tout
//         $repo->git("fetch --all");
//         //placement sur la branche production
//         $repo->git("pull --rebase origin production");

//         //déplacement des fichiers dans le bon dossier
//         shell_exec("cd ".ROOT_PATH." && cp -rf ".TEMP_DIR_PATH."/* ".TEMP_DIR_PATH."/.git .");

//         //suppression du dossier
//         unlink(TEMP_DIR_PATH);
//     } catch (GitRuntimeException $e) {
//         echo "Une erreur est survenue lors de l'execution du clone : <br/>".$e->getMessage().'<br/>';
//     }
    echo '<p>Le dépot n\'est pas initialisé</p>';
}

if (isset($_POST['pull']) && $_POST['pull'] == 1) {
    //on essaye de puller
    try {
        echo '<pre>'.$repo->git('pull --rebase origin production').'</pre>';
    } catch (GitRuntimeException $e) {
        echo "<br/><p>Erreur lors du pull ".$e->getMessage().'</p><br/><pre>'.$e->getTraceAsString().'</pre>';
    }
}

try {
    if (isset($_POST['commande'])) {
    	$command = $_POST['commande'];
    	echo '<pre>'.$repo->git($command).'</pre>';
    }
} catch (GitRuntimeException $e) {
    echo "Une erreur est survenue lors de l'execution de la commande : ".$command."<br/>".$e->getMessage().'<br/>';
}
// echo "<pre>".$repo->git('log --oneline')."</pre>";
?>
<form action='#' method='POST'>
	<p class="champ"><label for='commande'>Commande git : </label><input type='text' name="commande"/></p>
	<input type='submit' name="appliquer" value="Appliquer"/>
</form>

<form action='#' method='POST'>
    <p class="champ"><label>Mise à jour de l'application avec la dernière version du dépot git :</label>
    <input type='hidden' name="pull" value="1"/></p>
    <input type='submit' name="puller" value="Mettre à jour"/>
</form>