<?php

namespace Taf;

use PDO;

class TafConfig
{
    public static $db_instance = null;
    public static $mode_deploiement = false;
    public static $connected = null;
    public $tables = [];
    public static $user_disconnected = false;
    /* Information de connexion à la base de données */
    public $database_type = "mysql"; // "mysql" | "pgsql" | "sqlsrv"
    public $host = "localhost"; // adresse ou ip du serveur
    public $port = "3306"; // 3306 pour mysql | 5432 pour pgsql | 1433 pour sqlsrv 
    public $database_name = "porfolio"; // nom de la base de données
    public $user = "root"; // nom de l'utilisateur de la base de données
    public $password = ""; // mot de passe de l'utilisateur de la base de données

    /* informations de connexion à la documentation */
    public $documentation_username = "admin"; // nom d'utilisateur pour accéder à la documentation
    public $documentation_password = "1234"; // mot de passe de l'utilisateur pour accéder à la documentation


    public function __construct()
    {
        // $this->allow_cors();
        $this->init_data();
    }
    public function init_data()
    {
        if ($this->tables == [] && $this->is_connected()) {
            switch ($this->database_type) {
                case 'pgsql':
                    $this->tables = $this->get_db()->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'")->fetchAll(PDO::FETCH_COLUMN);
                    break;
                case 'mysql':
                    $this->tables = $this->get_db()->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    break;
                case 'sqlsrv':
                    $this->tables = $this->get_db()->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
                    break;
                default:
                    // type de base de données inconnu
                    break;
            }
        }
    }
    public function is_connected()
    {
        $this->get_db();
        return self::$connected;
    }
    public function get_db()
    {
        if (static::$db_instance == null) {
            try {
                switch ($this->database_type) {
                    case 'pgsql':
                        static::$db_instance = new PDO("{$this->database_type}:host={$this->host};port={$this->port};dbname={$this->database_name};", $this->user, $this->password);
                        break;
                    case 'mysql':
                        static::$db_instance = new PDO("{$this->database_type}:host={$this->host};port={$this->port};dbname={$this->database_name};", $this->user, $this->password);
                        break;
                    case 'sqlsrv':
                        static::$db_instance = new PDO("{$this->database_type}:Server={$this->host};Database={$this->database_name}", $this->user, $this->password);
                        break;
                    default:
                        // type de base de données inconnu
                        break;
                }

                //à commenter en mode production. Il permet de montrer les erreur explicitement
                static::$db_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connected = true;
            } catch (\Throwable $th) {
                //    var_dump($th);
                self::$connected = false;
            }

            // réglage du fuseau Horaire
            date_default_timezone_set("UTC");
        }
        return  static::$db_instance;
    }

    public function allow_cors()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: *");
    }
    public function verify_documentation_auth($username, $password)
    {
        if ($username == $this->documentation_username && $password == $this->documentation_password) {
            return true;
        } else {
            return false;
        }
    }
    public function check_documentation_auth($redirect_to = "login.php")
    {
        if (isset($_SESSION['user_logged']) && $_SESSION['user_logged']) {
            // laisser passer
        } else {
            header("Location:$redirect_to");
            exit;
        }
    }
    public function get_base_url()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
        } else {
            $url = "http://";
        }
        // Append the host(domain name, ip) to the URL.   
        $url .= $_SERVER['HTTP_HOST'];

        // Append the requested resource location to the URL   
        $url .= dirname($_SERVER['REQUEST_URI']) . "/";

        return $url;
    }
    public function get_api_service()
    {
        return "import { HttpClient, HttpHeaders } from '@angular/common/http';
                import { Injectable } from '@angular/core';
                import { Router } from '@angular/router';
                import { JwtHelperService } from '@auth0/angular-jwt';
                import * as moment from 'moment';


                @Injectable({
                providedIn: 'root'
                })
                export class ApiService {
                local_storage_prefixe = \"prefix.\";
                taf_base_url = \"" . $this->get_base_url() . "\";


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
    }
    public function get_table_descriptions($table_name, $les_based_table_name)
    {
        $resultat = array(
            "table_name" => $table_name,
            "cle_primaire" => "",
            "les_based_table_name" => $les_based_table_name,
            "les_referenced_table" => [],
            "les_colonnes" => $this->get_db()->query("DESCRIBE $table_name")->fetchAll(PDO::FETCH_ASSOC)
        );
        foreach ($resultat["les_colonnes"] as $key => $une_colonne) {
            $une_colonne["explications"] = "";
            if ($une_colonne["Key"] == "PRI") { // il  s'agit d'un e cle primaire
                $une_colonne["explications"] = "clé primaire";
                $resultat["cle_primaire"] = $une_colonne;
            } else if ($une_colonne["Key"] == "MUL") { // il  s'agit d'un e cle etrangère
                // informations sur la tables referante de cette colonne
                //$based_table = $table;
                $la_cle_etrangere = $une_colonne["Field"];
                $query2 = "SELECT TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
          FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
          WHERE CONSTRAINT_SCHEMA='" . $this->database_name . "' and REFERENCED_TABLE_NAME IS NOT NULL
          and TABLE_NAME='" . $table_name . "' and COLUMN_NAME='$la_cle_etrangere'
          order by REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME";
                $une_colonne["table"] = $this->get_db()->query($query2)->fetch(PDO::FETCH_ASSOC);
                $une_colonne["table_existant"] = false;
                $une_colonne["explications"] = "clé étrangère liée à la colonne " . $une_colonne["table"]["REFERENCED_COLUMN_NAME"] . " de la table " . $une_colonne["table"]["REFERENCED_TABLE_NAME"];
                if (in_array($une_colonne["table"]["REFERENCED_TABLE_NAME"], $les_based_table_name)) { // on revient à la table de depart et on risque de'etre en boucle infinie
                    $une_colonne["table_existant"] = true;
                    if ($une_colonne["table"]["REFERENCED_TABLE_NAME"] == $table_name) {
                        $une_colonne["referenced_table"] = array(
                            "table_name" => $une_colonne["table"]["REFERENCED_TABLE_NAME"],
                            "cle_primaire" => $resultat["cle_primaire"],
                            "les_based_table_name" => $les_based_table_name,
                            "les_referenced_table" => $resultat["les_referenced_table"],
                            "les_colonnes" => $this->get_db()->query("DESCRIBE $table_name")->fetchAll(PDO::FETCH_ASSOC)
                        );
                        $resultat['les_referenced_table'][] = $une_colonne["table"]["REFERENCED_TABLE_NAME"];
                    }
                } else if ($une_colonne["table"]) {
                    $resultat['les_referenced_table'][] = $une_colonne["table"]["REFERENCED_TABLE_NAME"];
                    $les_based_table_name[] = $une_colonne["table"]["REFERENCED_TABLE_NAME"];
                    $une_colonne["referenced_table"] = $this->get_table_descriptions($une_colonne["table"]["REFERENCED_TABLE_NAME"], $les_based_table_name);
                } else {
                    # code...
                }
            } else {
                # code...
            }

            $resultat["les_colonnes"][$key] = $une_colonne;
        }
        $resultat["les_based_table_name"] = $les_based_table_name;

        return $resultat;
    }
}
