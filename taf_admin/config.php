<?php

use Taf\TafAuth;

try {
    require '../TafConfig.php';
    $reponse = array();
    $taf_config = new \Taf\TafConfig();
    $taf_config->allow_cors();
    if (file_get_contents('php://input')=="") {
        $params=[];
    } else {
        $params=json_decode(file_get_contents('php://input'),true);
    }    
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();
    echo json_encode($reponse);
}
