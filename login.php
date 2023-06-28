<?php
session_start();
require './TafConfig.php';
$taf_config = new \Taf\TafConfig();
$submitted = false;
$login_success = false;
$_SESSION["user_logged"] = false;
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $submitted = true;
    $username = $_POST["username"];
    $password = $_POST["password"];
    $login_success = $taf_config->verify_documentation_auth($username, $password);
    if ($login_success) {
        $_SESSION["user_logged"] = true;
        header("Location:taf.php");
        exit;
    } else {
        $_SESSION["user_logged"] = false;
    }
} else {
    # code...
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JantTaf</title>
    <link href="./taf_assets/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark">
            <div class="container-fluid">
                <a href="taf.php" class="navbar-brand text-danger">JantTaf</a>
                <span>
                    <a href="https://h24code.com/donate.html" target="_blank" class="px-2 right"><button class="btn btn-secondary">Faire un don</button></a>
                </span>
            </div>
        </nav>
    </header>
    <main class="container mt-0">
        <div class="row vh-100 justify-content-center align-items-center">
            <div class="col-12 col-md-6 bg-white p-3">
                <h3>Accéder à la documentation</h3>
                <hr>
                <?php
                if (!$login_success && $submitted) {
                    echo '<div class="alert alert-danger" role="alert">
                        Nom d\'utilisateur ou mot de passe incorrect
                      </div>';
                } else {
                    # code...
                }

                ?>
                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Nom d'utilisateur JantTaf</label>
                        <input type="text" name="username" required placeholder="admin" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text">Ces informations sont définis sur le fichier TafConfig.php</div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Mot de passe JantTaf</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>