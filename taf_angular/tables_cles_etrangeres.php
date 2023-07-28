<?php

use Taf\TableDocumentation;

session_start();
require '../TafConfig.php';
require '../TableDocumentation.php';
$taf_config = new \Taf\TafConfig();
$taf_config->check_documentation_auth();


$query = "SELECT TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
          FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
          WHERE CONSTRAINT_SCHEMA='db_these_1' and REFERENCED_TABLE_NAME IS NOT NULL
          order by REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME";
$result = $taf_config->get_db()->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Parcourir les résultats
/* while ($row = $result) {
    $colonne = $row["Field"];
    $cle_primaire = ($row["Key"] == "PRI") ? "Oui" => "Non";

    echo "Colonne => $colonne\n";
    echo "Clé primaire => $cle_primaire\n\n";
} */
echo "<body style='color:white;background-color:black'><pre>";
$result_mapped = array_map(function ($une_colonne) {
    /* {
        "Field"=> "id_utilisateur",
        "Type"=> "int(255)",
        "Null"=> "NO",
        "Key"=> "PRI",
        "Default"=> null,
        "Extra"=> "auto_increment"
    }, */
    /* if ($une_colonne["Key"] == "PRI") { // il  s'agit d'un e cle primaire

    } else if ($une_colonne["Key"] == "MUL") { // il  s'agit d'un e cle etrangère
        $une_colonne["table"] = array(
            [
                "Field" => "id_privilege",
                "Type" => "int(255)",
                "Null" => "NO",
                "Key" => "PRI",
                "Default" => null,
                "Extra" => "auto_increment"
            ],
            [
                "Field" => "titre",
                "Type" => "varchar(255)",
                "Null" => "NO",
                "Key" => "",
                "Default" => null,
                "Extra" => ""
            ],
            [
                "Field" => "description",
                "Type" => "text",
                "Null" => "NO",
                "Key" => "",
                "Default" => null,
                "Extra" => ""
            ],
            [
                "Field" => "created_at",
                "Type" => "datetime",
                "Null" => "NO",
                "Key" => "",
                "Default" => "CURRENT_TIMESTAMP",
                "Extra" => ""
            ]
        );
    } else {
        # code...
    } */

    return $une_colonne;
}, $result);
echo json_encode($result_mapped, JSON_PRETTY_PRINT);

echo "</pre></body>";
