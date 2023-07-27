<?php

use Taf\TafAuth;
use Taf\TableQuery;
try {
    require './config.php';
    require '../TableQuery.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse=$taf_auth->check_auth($reponse);
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }
    
    $table_query=new TableQuery($table_name);
    /* 
        $params
        contient tous les parametres envoyés par la methode POST
     */

    
    if(count($params)==0){
        $reponse["status"] = false;
        $reponse["erreur"] = "Parameters required";
        echo json_encode($reponse);
        exit;
    }
    // recupération de a clé primaire de la table pour la condition de modification
    $query_primary_key="SHOW KEYS FROM $table_name WHERE Key_name = 'PRIMARY'";
    $primary_key= $taf_config->get_db()->query($query_primary_key)->fetch()["Column_name"];
    $condition="where $primary_key=".$params[$primary_key];
    // execution de la requete de modification
    $query="delete from $table_name ".$condition;
    // $reponse["query"]=$query;
    $resultat=$taf_config->get_db()->exec($query);
    if ($resultat) {
        $reponse["status"] = true;
    } else {
        $reponse["status"] = false;
        $reponse["erreur"] = "Erreur $resultat";
    }
    echo json_encode($reponse);
} catch (\Throwable $th) {
    
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}

?>