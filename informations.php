<?php

use Taf\TafAuth;

try {
    require './TafConfig.php';
    $taf_config = new \Taf\TafConfig();  
    $taf_config->allow_cors();
    $tables = $taf_config->tables;
    $reponse = array();
    foreach ($tables as $table_name) {
        $file_count = 0;
        $dir    = './';
        $files = scandir($dir);
        foreach ($files as $file) {
            if (array_search($table_name, $files)) {
                $file_count = 1;
            }
        }
        $reponse["data"][] = array(
            "nom_table" => $table_name,
            "file_count" => $file_count
        );
    }
    $reponse["nom_base_de_donnees"] = $taf_config->database_name;
    $reponse["connexion"] = $taf_config->is_connected();
    $reponse["username"] = $taf_config->user;
    $reponse["status"] = true;
    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();
    echo json_encode($reponse);
}

