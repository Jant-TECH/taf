<?php

use Taf\TafConfig;

echo "<h1><a href='./taf'>Accueil</a></h1>";
try {
    if (!isset($_GET["table"]) && !isset($_GET["tout"])) {
        echo "<h1>Paramètre(s) requi(s)</h1>";
        exit;
    }
    require './TafConfig.php';
    $taf_config = new TafConfig();

    function generate($table_name)
    {
        global $taf_config;
        echo "<h1>Génération des routes de la table \"" . $table_name . "\"</h1>";
        if (!is_dir("./" . $table_name)) {
            mkdir("./" . $table_name);
        }

        // mise à jour du contenu  du fichier de configuration suivi de la réation du fichier
        $config_content = str_replace("{{{table_name}}}", $table_name, file_get_contents("./api/config.php"));
        if (!file_exists('./' . $table_name . "/config.php")) {
            file_put_contents('./' . $table_name . "/config.php", $config_content);
        }

        // mise à jour du contenu  du fichier de configuration suivi de la réation du fichier
        $table_descriptions = $taf_config->get_table_descriptions($table_name, [$table_name]);
        $referenced_tables_queries = implode("\n", array_map(function ($une_table) {
            return '$reponse["data"]["les_' . $une_table . 's"] = $taf_config->get_db()->query("select * from ' . $une_table . '")->fetchAll(PDO::FETCH_ASSOC);';
        }, $table_descriptions["les_referenced_table"]));
        $config_content = str_replace("/*{{content}}*/", $referenced_tables_queries, file_get_contents("./api/get_form_details.php"));
        if (!file_exists('./' . $table_name . "/get_form_details.php")) {
            file_put_contents('./' . $table_name . "/get_form_details.php", $config_content);
        }
        if (!file_exists("./" . $table_name . '/add.php')) {
            copy('./api/add.php', "./" . $table_name . "/add.php", );
        }
        if (!file_exists("./" . $table_name . '/delete.php')) {
            copy('./api/delete.php', "./" . $table_name . "/delete.php");
        }
        if (!file_exists("./" . $table_name . '/edit.php')) {
            copy('./api/edit.php', "./" . $table_name . "/edit.php");
        }
        if (!file_exists("./" . $table_name . '/get.php')) {
            copy('./api/get.php', "./" . $table_name . "/get.php");
        }
        if (!file_exists("./" . $table_name . '/index.php')) {
            copy('./api/index.php', "./" . $table_name . "/index.php");
        }
        echo "<h3>Succes</h3>";
    }
    if (isset($_GET["table"])) {
        $table_name = $_GET["table"];
        generate($table_name);
        header('location:./taf#table_' . $table_name);
    } elseif (isset($_GET["tout"])) {
        $query = "SHOW TABLES";
        $tables = $taf_config->get_db()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tables as $key => $value) {
            $table_name = $value["Tables_in_" . $taf_config->database_name];
            generate($table_name);
        }
        header('location: taf.php');
    }
} catch (\Throwable $th) {

    echo "<h1>" . $th->getMessage() . "</h1>";
}