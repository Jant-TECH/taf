<?php

use Taf\TableDocumentation;


try {
    require '../TafConfig.php';
    require '../TableDocumentation.php';
    $taf_config = new \Taf\TafConfig();
    $taf_config->allow_cors();
    $tables = $taf_config->tables;
    $reponse = array();
    foreach ($tables as $table_name) {
        $file_count = 0;
        $dir = './';
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
    $reponse["table_v1"] = array_map(function ($une_table) {
        $docs = new TableDocumentation($une_table);
        return ["table" => $une_table, "description" => $docs->description, "les_types" => ["add", "edit", "list", "details"]];
    }, $taf_config->tables);
    $reponse["table_v2"] = array_map(function ($une_table) {
        $docs = new TableDocumentation($une_table);
        return ["table" => $une_table, "description" => $docs->description, "table_descriptions" => $docs->table_descriptions, "les_types" => ["add", "edit", "list", "details"]];
    }, $taf_config->tables);
    $reponse["base_url"] = $taf_config->get_base_url();
    $reponse["status"] = true;
    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();
    echo json_encode($reponse);
}

