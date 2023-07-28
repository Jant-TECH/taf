<?php

use Taf\TableDocumentation;

session_start();
require '../TafConfig.php';
require '../TableDocumentation.php';
$taf_config = new \Taf\TafConfig();
$taf_config->check_documentation_auth();

// Nom de la table
$table_name = $_GET["table"];
$reponse = array();
$table_documentation = new TableDocumentation($table_name);
$reponse["status"] = true;
$reponse["data"] = $table_documentation->get_table_descriptions($_GET["table"], [$_GET["table"]]);
echo "<body style='color:white;background-color:black'><pre>";
echo json_encode($reponse, JSON_PRETTY_PRINT);
echo "</pre></body>";
