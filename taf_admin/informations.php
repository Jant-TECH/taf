<?php
use Taf\TafAuth;
use Taf\TableDocumentation;

try {
    require '../TafConfig.php';
    require '../TableDocumentation.php';
    require '../taf_auth/TafAuth.php';
    $taf_auth = new TafAuth();
    // toutes les actions nécéssitent une authentification
    $auth_reponse = $taf_auth->check_auth();
    $taf_config = new \Taf\TafConfig();
    $taf_config->allow_cors();
    if ($auth_reponse["status"] == false) {
        echo json_encode($auth_reponse);
        die;
    }
    $tables = $taf_config->tables;
    $reponse = array();
    foreach ($tables as $table_name) {
        $file_count = 0;
        $dir = '../';
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
    $reponse["table_v1"] = [
        "projectName" => "projet1.angular",
        "decription" => "Fichier de configuration de Taf",
        "taf_base_url" => $taf_config->get_base_url(),
        "les_modules" => [
            [
                "module" => "home",
                "les_tables" => array_map(function ($une_table) {
                    $docs = new TableDocumentation($une_table);
                    return ["table" => $une_table, "description" => $docs->description, "les_types" => ["add", "edit", "list", "details"]];
                }, $taf_config->tables)
            ],
            [
                "module" => "public",
                "les_tables" => [
                    ["table" => "login", "description" => ["login", "pwd"], "les_types" => ["login"]]
                ]
            ],
        ]
    ];
    $reponse["api_service"] = $taf_config->get_api_service();
    $reponse["table_v2"] = [
        "projectName" => "projet1.angular",
        "decription" => "Fichier de configuration de Taf",
        "taf_base_url" => $taf_config->get_base_url(),
        "les_modules" => [
            [
                "module" => "home",
                "les_tables" => array_map(function ($une_table) {
                    $docs = new TableDocumentation($une_table);
                    return ["table" => $une_table, "description" => $docs->description, "table_descriptions" => $docs->table_descriptions, "les_types" => ["add", "edit", "list", "details"]];
                }, $taf_config->tables)
            ],
            [
                "module" => "public",
                "les_tables" => [
                    ["table" => "login", "description" => ["login", "pwd"], "les_types" => ["login"]]
                ]
            ],
        ]
    ];
    $reponse["tables"] = array_map(function ($une_table) {
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
