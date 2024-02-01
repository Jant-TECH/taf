<?php

use Taf\TafConfig;

$reponse = array();

try {
    if (!isset($_GET["table"]) && !isset($_GET["tout"])) {
        $reponse["status"] = false;
        $reponse["erreur"] = "parameters required";
        echo json_encode($reponse);
        exit;
    }
    require './TafConfig.php';
    $taf_config = new TafConfig();
    $reponse["data"] = [
        "all_tables" => false,
        "config" => false,
        "get_form_details" => false,
        "add" => false,
        "delete" => false,
        "edit" => false,
        "get" => false,
        "index" => false
    ];

    function generate($table_name)
    {
        global $taf_config, $reponse;
        if (!is_dir("./" . $table_name)) {
            mkdir("./" . $table_name);
        }

        // mise à jour du contenu  du fichier de configuration suivi de la réation du fichier
        $config_content = str_replace("{{{table_name}}}", $table_name, file_get_contents("./api/config.php"));
        if (!file_exists('./' . $table_name . "/config.php")) {
            file_put_contents('./' . $table_name . "/config.php", $config_content);
            $reponse["data"]["config"] = true;
        }

        // mise à jour du contenu  du fichier de configuration suivi de la réation du fichier
        $table_descriptions = $taf_config->get_table_descriptions($table_name, [$table_name]);
        $referenced_tables_queries = implode("\n", array_map(function ($une_table) {
            return '$reponse["data"]["les_' . $une_table . 's"] = $taf_config->get_db()->query("select * from ' . $une_table . '")->fetchAll(PDO::FETCH_ASSOC);';
        }, $table_descriptions["les_referenced_table"]));
        $config_content = str_replace("/*{{content}}*/", $referenced_tables_queries, file_get_contents("./api/get_form_details.php"));

        if (!file_exists('./' . $table_name . "/get_form_details.php")) {
            file_put_contents('./' . $table_name . "/get_form_details.php", $config_content);
            $reponse["data"]["get_form_details"] = true;
        }
        if (!file_exists("./" . $table_name . '/add.php')) {
            copy('./api/add.php', "./" . $table_name . "/add.php", );
            $reponse["data"]["add"] = true;
        }
        if (!file_exists("./" . $table_name . '/delete.php')) {
            copy('./api/delete.php', "./" . $table_name . "/delete.php");
            $reponse["data"]["delete"] = true;
        }
        if (!file_exists("./" . $table_name . '/edit.php')) {
            copy('./api/edit.php', "./" . $table_name . "/edit.php");
            $reponse["data"]["edit"] = true;
        }
        if (!file_exists("./" . $table_name . '/get.php')) {
            copy('./api/get.php', "./" . $table_name . "/get.php");
            $reponse["data"]["get"] = true;
        }
        if (!file_exists("./" . $table_name . '/index.php')) {
            copy('./api/index.php', "./" . $table_name . "/index.php");
            $reponse["data"]["index"] = true;
        }
        $reponse["status"] = true;
        echo json_encode($reponse);
    }
    if (isset($_GET["table"])) {
        $table_name = $_GET["table"];
        generate($table_name);
        $reponse["status"] = true;
        echo json_encode($reponse);
    } elseif (isset($_GET["tout"])) {
        $query = "SHOW TABLES";
        $tables = $taf_config->get_db()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tables as $key => $value) {
            $table_name = $value["Tables_in_" . $taf_config->database_name];
            generate($table_name);
        }
        $reponse["status"] = true;
        $reponse["data"]["all_tables"] = true;
        echo json_encode($reponse);
    }
} catch (\Throwable $th) {
    $reponse["status"] = true;
    $reponse["erreur"] = $th->getMessage();
    echo json_encode($reponse);
}