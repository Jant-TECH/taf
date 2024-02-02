<?php

use Taf\TafAuth;

try {
    require './config.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    if ($auth_reponse["status"] == false && count($params) == 0) {
        echo json_encode($auth_reponse);
        die;
    }

    /*{{content}}*/
    $reponse["data"]=array();
    foreach ($params["les_tables"] as $value) {
        $reponse["data"][$value] = $taf_config->get_db()->query("select *from $value ")->fetchAll(PDO::FETCH_ASSOC);
    }


    $reponse["status"] = true;

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
