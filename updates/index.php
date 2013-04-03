<?php
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
defined('UPDATES_PATH') || define('UPDATES_PATH', ROOT_PATH.'/updates');

//ajout du fichier de lib
require_once(UPDATES_PATH.'/PHPGit/Repository.php');

try {
    $repo = new PHPGit_Repository(ROOT_PATH);
} catch (InvalidGitRepositoryDirectoryException $e) {
    die("Erreur lors de l'initialisation de la mise à jour : ".$e);
//     //le dépot n'est pas initialisé
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
}

try {
    echo $repo->git('pull --rebase origin production');
} catch (GitRuntimeException $e) {
    echo "Oups lors du pull ".$e->getMessage().'<br/>'.$e->getTraceAsString();
}


try {
    if (isset($_POST['commande'])) {
    	$command = $_POST['commande'];
    	echo $repo->git($command);
    }
} catch (GitRuntimeException $e) {
    echo "Une erreur est survenue lors de l'execution de la commande : ".$command."<br/>".$e->getMessage().'<br/>';
}
echo "<pre>".$repo->git('log --oneline')."</pre>";
?>
<form action='#' method='POST'>
	<p class="champ"><label for='commande'>Commande git : </label><input type='text' name="commande"/></p>
	<input type='submit' name="appliquer" value="Appliquer"/>
</form>