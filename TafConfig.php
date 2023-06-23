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
    public $database_name = "database_name"; // nom de la base de données
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
    public function check_documentation_auth()
    {
        if (isset($_SESSION['user_logged']) && $_SESSION['user_logged']) {
            // laisser passer
        } else {
            header("Location:login.php");
            exit;
        }
    }
}
