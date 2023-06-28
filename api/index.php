<?php
session_start();
try {
    require './config.php';
    require '../TableDocumentation.php';
    $table_documentation = new \Taf\TableDocumentation($table_name);
    $taf_config->check_documentation_auth();
} catch (\Throwable $th) {
    echo "<h1>" . $th->getMessage() . "</h1>";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JantTaf</title>
    <link href="../taf_assets/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../taf_assets/taf_index.css">
    <link href="../taf_assets/css/custom.ace.css" rel="stylesheet">
</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark">
            <div class="container-fluid">
                <a href='../taf.php' class="navbar-brand text-danger">JantTaf</a>
                <span>
                    <a href="https://h24code.com/donate.html" target="_blank" class="px-2 right"><button class="btn btn-secondary">Faire un don</button></a>
                    <a href="../login.php" class="px-2 right"><button class="btn btn-danger">Déconnexion</button></a>
                </span>
            </div>
        </nav>
    </header>
    <main class="container mt-5">
        <div class="">
            <p class="col-12 fs-3 text-justify">
            <h1>Description de la table <span class="text-danger"><?= $table_documentation->table_name ?></span></h1>
            <ol>
                <?php
                try {
                    if (count($table_documentation->table_descriptions["les_colonnes"]) > 0) {
                        foreach ($table_documentation->table_descriptions["les_colonnes"] as $key => $value) {
                            echo "<li class=\"\">" . $value["Field"] . " <span class=\"text-primary\">" . $value["explications"] . "</span></li>";
                        }
                    } else {
                        foreach ($table_documentation->description as $key => $value) {
                            echo "<li class=\"\">" . $value . "</li>";
                        }
                    }
                } catch (\Throwable $th) {
                    echo "<li>" . $th->getMessage() . "</li>";
                }
                ?>
            </ol>
        </div>
        <div>
            <h1>Action(s) possible(s) dans la table <span class="text-danger"><?= $table_documentation->table_name ?></span></h1>
            <?php
            $dir    = './';
            $files = scandir($dir);
            foreach ($files as $key => $value) {
                if ($value != "." && $value != ".."  && $value != "index.php"  && $value != "config.php") {
                    $action = str_replace(".php", "", $value);

                    if ($action == "get") {
                        echo $table_documentation->get();
                    } else if ($action == "add") {
                        echo $table_documentation->add();
                    } else if ($action == "edit") {
                        echo $table_documentation->edit();
                    } else if ($action == "delete") {
                        echo $table_documentation->delete();
                    }
                }
            }
            ?>
        </div>

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </main>
</body>
<script src="../taf_assets/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="../taf_assets/bootstrap.bundle.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../taf_assets/ace.beautify.js"></script>
<script src="../taf_assets/js/custom.ace.js"></script>


</html>