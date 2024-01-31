<?php

use Taf\TafConfig;
use Taf\TafAuth;

$params = json_decode(file_get_contents('php://input'), true) ?? [];
$reponse = array();

try {
    require '../TafConfig.php';
    require '../taf_auth/TafAuth.php';
    $taf_config = new TafConfig();
    $taf_auth = new TafAuth();

    $taf_config->allow_cors();
    if (!isset($params["username"]) || !isset($params["password"])) {
        $reponse["status"] = false;
        $reponse["erreur"] = "Username and password are required";
        echo json_encode($reponse);
        exit;
    }
    $username = htmlspecialchars($params["username"]);
    $password = htmlspecialchars($params["password"]);
    $resultat = $taf_config->verify_documentation_auth($username, $password);
    if (!$resultat) {
        $reponse["status"] = false;
        $reponse["erreur"] = "Authentication failed";
        echo json_encode($reponse);
        exit;
    }
    $reponse["status"] = true;
    $reponse["data"] = $taf_auth->get_token(["username" => $username, "id_privilege" => 1]);
    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
