<?php

use Taf\TableDocumentation;

session_start();
require '../TafConfig.php';
require '../TableDocumentation.php';
$taf_config = new \Taf\TafConfig();
$taf_config->check_documentation_auth("../login.php");
?>

<!DOCTYPE html>
<html lang=\"fr\">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JantTaf</title>
    <link href="../taf_assets/bootstrap.min.css" rel="stylesheet">
    <link href="./taf_assets/css/custom.ace.css" rel="stylesheet">
</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark">
            <div class="container-fluid">
                <a href="../taf.php" class="navbar-brand text-danger">JantTaf</a>
                <span>
                    <a href="https://h24code.com/donate.html" target="_blank" class="px-2 right"><button class="btn btn-secondary">Faire un don</button></a>
                    <a href="login.php" class="px-2 right"><button class="btn btn-danger">Déconnexion</button></a>
                </span>
            </div>
        </nav>
    </header>
    <main class="container mt-5">
        <?php if ($taf_config->is_connected()) : ?>
            <div class="d-flex align-items-center justify-content-between">
                <h1>Fichier de configuration pour le projet angular <span class="text-danger"><?= $taf_config->database_name ?></span>
                </h1>
                <!-- <a href="./generate.php?tout=oui" class="px-2 right"><button class="btn btn-primary">Télécharger</button></a> -->
            </div>
            <p>
                Créez un fichier JSON dans la racine de votre projet <span class="text-danger">Angular</span> nommé taf.config.json avec le contenu suivant : <br>
                <a href="https://www.npmjs.com/package/jant-taf" target="_blank">cliquez ici pour consulter la documentation complete</a>
            </p>
            <div class="my-3 ace_js">
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
            <div class="d-flex align-items-center justify-content-between">
                <h1>Service angular <span class="text-danger">api.service.ts</span>
                </h1>
                <!-- <a href="./generate.php?tout=oui" class="px-2 right"><button class="btn btn-primary">Télécharger</button></a> -->
            </div>
            <h3>Instalations à faire</h3>
            <ul>
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
                echo "import { HttpClient, HttpHeaders } from '@angular/common/http';
                import { Injectable } from '@angular/core';
                import { Router } from '@angular/router';
                import { JwtHelperService } from '@auth0/angular-jwt';
                import * as moment from 'moment';


                @Injectable({
                providedIn: 'root'
                })
                export class ApiService {
                local_storage_prefixe = \"prefix.\";
                taf_base_url = \"" . $taf_config->get_base_url() . "\";


                network: any = {
                    token: undefined,
                    status: true,
                    message: \"Aucun probléme détecté\",
                }
                token: any = {
                    token_key: null,
                    token_decoded: null,
                    user_connected: null,
                    is_expired: null,
                    date_expiration: null
                }

                constructor(private http: HttpClient, private route: Router) { }
                // sauvegardes
                async get_from_local_storage(key: string): Promise<any> {
                    let res: any = await localStorage.getItem(this.local_storage_prefixe + key);
                    return JSON.parse(res)
                }
                async save_on_local_storage(key: string, value: any): Promise<void> {
                    await localStorage.setItem(this.local_storage_prefixe + key, JSON.stringify(value));
                }
                async delete_from_local_storage(key: string) {
                    await localStorage.setItem(this.local_storage_prefixe + key, 'null');
                }

                async get_token() {
                    //le token n'est pas encore chargé
                    if (this.network.token == undefined) {
                    this.network.token = await this.get_from_local_storage(\"token\")
                    if (this.network.token != undefined && this.network.token != null) {// token existant
                        this.update_data_from_token()// mise a jour du token
                    }
                    } else {// token dèja chargé
                    this.update_data_from_token()// mise a jour du token
                    }
                    return this.network.token
                }
                //les requetes http
                async taf_get(path: string, on_success: Function, on_error: Function) {
                    let api_url = this.taf_base_url + path;
                    const httpOptions = {
                    headers: new HttpHeaders({
                        Authorization: \"Bearer \" + await this.get_token(),
                    })
                    };

                    this.http.get(api_url, httpOptions).subscribe(
                    (reponse: any) => {// on success
                        on_success(reponse)
                    },
                    (error: any) => {// on error
                        this.on_taf_get_error(error, on_error)
                    }
                    )
                }
                on_taf_get_error(error: any, on_error: Function) {
                    this.network.status = false;
                    this.network.message = error
                    alert(\"Merci de vérifier votre connexion\")
                    on_error(error)
                }
                async taf_post(path: string, data_to_send: any, on_success: Function, on_error: Function) {
                    let api_url = this.taf_base_url + path;
                    const httpOptions = {
                    headers: new HttpHeaders({
                        Authorization: \"Bearer \" + await this.get_token(),
                    })
                    };
                    this.http.post(api_url, data_to_send, httpOptions).subscribe(
                    (reponse: any) => {// on success
                        on_success(reponse)
                    },
                    (error: any) => {// on error
                        this.on_taf_post_error(error, on_error)
                    }
                    )
                }
                on_taf_post_error(error: any, on_error: any) {
                    this.network.status = false;
                    this.network.message = error
                    alert(\"Merci de vérifier votre connexion\")
                    on_error(error)
                }
                async update_data_from_token() {
                    let token_key = await this.get_from_local_storage(\"token\")
                    const helper = new JwtHelperService();
                    const decodedToken = helper.decodeToken(token_key);
                    const expirationDate = helper.getTokenExpirationDate(token_key);
                    const isExpired = helper.isTokenExpired(token_key);

                    this.token = {
                    token_key: token_key,
                    token_decoded: decodedToken,
                    user_connected: decodedToken.taf_data,
                    is_expired: isExpired,
                    date_expiration: expirationDate
                    }
                    if (this.token.is_expired) {
                    this.on_token_expire()
                    }
                }
                on_token_expire() {
                    alert(\"Votre session s'est expiré! Veuillez vous connecter à nouveau\")
                    this.delete_from_local_storage(\"token\")
                    this.route.navigate(['/public/login'])
                }
                format_date(date_string: string) {
                    return {
                    full: moment(date_string).locale(\"fr\").format(\"dddd Do MMMM YYYY\"),// 27 février 2023 
                    jma: moment(date_string).locale(\"fr\").format(\"Do MMMM YYYY\"),// jeudi ...
                    jma2: moment(date_string).locale(\"fr\").format(\"DD-MM-YYYY\"),// 01-11-2023
                    jma3: moment(date_string).locale(\"fr\").format(\"YYYY-MM-DD\"),// 2023-10-21
                    full_datetime: moment(date_string).locale(\"fr\").format(\"dddd Do MMMM YYYY à HH:mm\"),// 27 février 2023 
                    }
                }
                format_current_date() {
                    return {
                    full: moment().locale(\"fr\").format(\"dddd Do MMMM YYYY\"),// 27 février 2023 
                    jma: moment().locale(\"fr\").format(\"Do MMMM YYYY\"),// jeudi ...
                    jma2: moment().locale(\"fr\").format(\"DD-MM-YYYY\"),// 01-11-2023
                    full_datetime: moment().locale(\"fr\").format(\"dddd Do MMMM YYYY à HH:mm\"),// 27 février 2023 
                    }
                }
                custom_menu() {
                    let id_privilege = parseInt(this.token.token_decoded.taf_data.id_privilege) //2
                    console.log(\"id_privilege= \", id_privilege)


                    this.menu = this.full_menu.filter((un_menu: any) => {
                    un_menu.items = un_menu.items.filter((un_item: any) => { return un_item.privileges.indexOf(id_privilege) != -1 })

                    return un_menu.items.length > 0
                    })
                }
                has_acces(route: string) {

                }
                }";
                //echo "</pre>";

                ?>
            </div>
        <?php elseif (!$taf_config->is_connected() && ($taf_config->host != "" || $taf_config->user != "" || $taf_config->password != "" || $taf_config->database_name != "")) : ?>
            <div class="alert alert-danger fs-3" role="alert">
                Echec de connexion à votre base de données <span class="text-danger"><?= $taf_config->database_name; ?></span> avec l'utilisateur <span class="text-danger"><?= $taf_config->user; ?></span>
            </div>
        <?php else : ?>
            <div class="alert alert-warning fs-3" role="alert">
                Après la configuration, vous actualisez cette page
            </div>
        <?php endif; ?>

        </div>
    </main>
</body>
<!-- JavaScript Bundle with Popper -->
<script src="../taf_assets/bootstrap.bundle.min.js"></script>
<script src="../taf_assets/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="./taf_assets/js/custom.ace.js"></script>


</html>