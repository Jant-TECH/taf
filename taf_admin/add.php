<?php

use Taf\TafAuth;
use Taf\TableQuery;

try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }

    $table_name = $params["table_name"];
    $table_query = new TableQuery($table_name);
    /* 
        $params["params"]
        contient tous les parametres envoyés par la methode POST
     */

    if (empty($params["params"])) {
        $reponse["status"] = false;
        $reponse["erreur"] = "Parameters required";
        echo json_encode($reponse);
        exit;
    }
    // pour charger l'heure courante
    // $params["params"]["date_enregistrement"]=date("Y-m-d H:i:s");
    $query = $table_query->dynamicInsert2($params["params"]["les_champs"]);
    $reponse["query"]=$query;
    if ($taf_config->get_db()->exec($query)) {
        $reponse["status"] = true;
        $reponse["data"] = $taf_config->get_db()->lastInsertId();
    } else {
        $reponse["status"] = false;
        $reponse["erreur"] = "Erreur d'insertion à la base de ";
    }
    echo json_encode($reponse);
} catch (\Throwable $th) {

    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
