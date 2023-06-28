<?php

use Taf\TableDocumentation;

session_start();
require './TafConfig.php';
require './TableDocumentation.php';
$taf_config = new \Taf\TafConfig();
$taf_config->check_documentation_auth();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JantTaf</title>
    <link href="./taf_assets/bootstrap.min.css" rel="stylesheet">
    <link href="./taf_assets/css/custom.ace.css" rel="stylesheet">
</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark">
            <div class="container-fluid">
                <a href="#" class="navbar-brand text-danger">JantTaf</a>
                <span>
                    <a href="https://h24code.com/donate.html" target="_blank" class="px-2 right"><button class="btn btn-secondary">Faire un don</button></a>
                    <a href="login.php" class="px-2 right"><button class="btn btn-danger">Déconnexion</button></a>
                </span>
            </div>
        </nav>
    </header>
    <main class="container mt-5">
        <div class="row">
            <p class="col-12 text-justify fs-4">
                <strong class="text-danger">JantTaf</strong> est un générateur automatique d'<span class="text-danger">api</span> à partir d'une base de données <span class="text-danger">MYSQL</span> ou <span class="text-danger">PGSQL</span> ou <span class="text-danger">SQLSRV</span>.
                <br>
                Une fois le fichier de configuration (<span class="text-danger">TafConfig.php)</span> personnalisé, grâce à l'api vous pouvez voir toutes
                les tables de votre base de données et ainsi générer les fichiers nécessaires à la manipulation de ces tables
                comme la récupération, la suppression, l'ajout et la modification des données.
            </p>
            <?php if ($taf_config->is_connected()) : ?>
                <div class="d-flex align-items-center justify-content-between">
                    <h1>La(es) table(s) de la base de données <span class="text-danger"><?= $taf_config->database_name ?></span>

                    </h1>
                    <div class="d-flex">
                        <a href="./generate.php?tout=oui" class="right"> <button class="btn btn-warning">Tout générer</button></a>
                    </div>
                </div>
                <p class="col-12 fs-4 mt-2">
                <ol class="list-group" id="mes_tables">
                    <?php
                    $dir    = './';
                    $files = scandir($dir);
                    foreach ($taf_config->tables as $key => $table_name) {
                        if (array_search($table_name, $files)) { // table dèja générée
                            echo "<li id='table_$table_name' class='list-group-item fs-3  d-flex justify-content-between align-items-center bg-light'><span>" . $table_name . "</span><a class='px-2 right' href='./$table_name'><button class='btn btn-primary'> voir la documentation et les routes </button></a></li>";
                        } else { // table non encore générée
                            echo "<li id='table_$table_name' class='list-group-item fs-3  d-flex justify-content-between align-items-center'><span>" . $table_name . "</span><a class='px-2 right' href='generate?table=$table_name'><button class='btn btn-secondary'>Générer les routes et la documentation </button></a>";
                        }
                    }
                    ?>
                </ol>
            <?php elseif (!$taf_config->is_connected() && ($taf_config->host != "" || $taf_config->user != "" || $taf_config->password != "" || $taf_config->database_name != "")) : ?>
                <div class="alert alert-danger fs-3" role="alert">
                    Echec de connexion à votre base de données <span class="text-danger"><?= $taf_config->database_name; ?></span> avec l'utilisateur <span class="text-danger"><?= $taf_config->user; ?></span>
                </div>
            <?php else : ?>
                <div class="alert alert-warning fs-3" role="alert">
                    Après la configuration, vous actualisez cette page
                </div>
            <?php endif; ?>
            <h1 class="col-12 mt-5">
                Configuration
            </h1>
            <p class="col-12 fs-4 mt-2">
                La configuration repose sur le fichier <span class="text-danger">TafConfig.php</span>.
                Dans ce fichier vous devez spécifier: <br>
                • le type de la base de données <br>
                • l'adresse de votre serveur<br>
                • le port du SGBD<br>
                • le nom de votre base de donnée <br>
                • nom d'utilisateur de la base de données <br>
                • mot de passe de l'utilisateur de la base de données <br>
            </p>
            <p class="col-12 fs-4 mt-2">
                <span class="text-danger">NB:</span> <br>
                Dans le cadre de Postgres, n'oubliez pas d'activer l'extension au niveau de php.ini (";extension=pdo_pgsql" -> "extension=pdo_pgsql")) <br>

            </p>
        </div>
        <div class="col-12">
            <div class="row position-relative my-3">
                <div class="col-12 ace_php">
                    /* Information de connexion à la base de données */
                    public $database_type = "mysql"; // "mysql" | "pgsql" | "sqlsrv"
                    public $host = "localhost"; // adresse ou ip du serveur
                    public $port = "3306"; // 3306 pour mysql | 5432 pour pgsql | 1433 pour sqlsrv
                    public $database_name = "ma_bd"; // nom de la base de données
                    public $user = "root"; // nom de l'utilisateur de la base de données
                    public $password = ""; // mot de passe de l'utilisateur de la base de données

                    /* informations de connexion à la documentation */
                    public $documentation_username = "admin"; // nom d'utilisateur pour accéder à la documentation
                    public $documentation_password = "1234"; // mot de passe de l'utilisateur pour accéder à la documentation
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-5">
            <h1>Fichier de configuration pour le <span class="text-danger">projet angular</span>
            </h1>
            <!-- <a href="./generate.php?tout=oui" class="px-2 right"><button class="btn btn-primary">Télécharger</button></a> -->
        </div>
        <p>
            Créez un fichier JSON dans la racine de votre projet <span class="text-danger">Angular</span> nommé taf.config.json avec le contenu suivant : <br>
            <a href="https://www.npmjs.com/package/jant-taf" target="_blank">cliquez ici pour consulter la documentation complete</a>
        </p>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#tab_v1" type="button" role="tab" aria-controls="home" aria-selected="true">De la version 1.0 à la version 1.9</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tab_v2" type="button" role="tab" aria-controls="profile" aria-selected="false">A partir de la version 2.0</button>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="tab_v1" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="ace_js">
                    <?php
                    //echo "<pre>";
                    echo json_encode(
                        [
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
                        ],
                        JSON_PRETTY_PRINT
                    );
                    //echo "</pre>";

                    ?>
                </div>
            </div>
            <div class="tab-pane" id="tab_v2" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <div class="ace_js">
                    <?php
                    //echo "<pre>";
                    echo json_encode(
                        [
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
                        ],
                        JSON_PRETTY_PRINT
                    );
                    //echo "</pre>";

                    ?>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <h1>Service angular <span class="text-danger">api.service.ts</span>
            </h1>
            <!-- <a href="./generate.php?tout=oui" class="px-2 right"><button class="btn btn-primary">Télécharger</button></a> -->
        </div>
        <h3>Instalations à faire</h3>
        <ul>
            <li>
                <span class="text-danger">bootstrap</span> : <span class="bg-secondary badge">npm install bootstrap</span> (importez les fichiers css et js)
            </li>
            <li>
                <span class="text-danger">momentjs</span> : gestion des dates avec la commande <span class="bg-secondary badge">npm install moment</span>
            </li>
            <li>
                <span class="text-danger">@auth0/angular-jwt</span> : gestion des token <span class="bg-secondary badge">npm install @auth0/angular-jwt</span>
            </li>
        </ul>

        <p>
            Créez un service dans votre projet <span class="text-danger">Angular</span> nommé <span class="text-danger">api.service.ts</span> avec le contenu suivant : <br>
        </p>
        <div class="my-3 ace_js">
            <?php
            //echo "<pre>";
            echo $taf_config->get_api_service();
            //echo "</pre>";

            ?>
        </div>
    </main>
</body>
<!-- JavaScript Bundle with Popper -->
<script src="./taf_assets/bootstrap.bundle.min.js"></script>
<script src="./taf_assets/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="./taf_assets/ace.beautify.js"></script>
<script src="./taf_assets/js/custom.ace.js"></script>

</html>