<?php
    require './TafConfig.php';
    $taf_config = new \Taf\TafConfig();
    $taf_config->allow_cors();
    $params = json_decode(file_get_contents('php://input'), true) ?? [];
    $reponse = array();
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
    $reponse["data"] = $username;
    echo json_encode($reponse);
