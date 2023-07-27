<?php

namespace Taf;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;

require '../taf_auth/vendor/autoload.php';

class TafAuth
{
    public $secretKey  = 'notre_clef_prive';
    private $issuedAt   = null;
    public $expire = null;
    public $serverName = "your.domain.name";

    private $time_to_expire = 120;

    public function __construct()
    {
        $this->issuedAt   = new DateTimeImmutable();
        $this->expire = $this->issuedAt->modify('+' . $this->time_to_expire . ' minutes')->getTimestamp();
    }

    public function get_data($taf_data = ["id_taf_user" => "1", "email" => "amarmouhamed4@gmail.com", "password" => "amar1", "created_at" => "2022-08-01 04:15:14"])
    {
        return array(
            'iat'  => $this->issuedAt->getTimestamp(),
            'iss'  => $this->serverName,
            'nbf'  => $this->issuedAt->getTimestamp(),
            'exp'  => $this->expire,
            'taf_data'  => $taf_data
        );
    }
    public function get_token($taf_data)
    {
        $data = $this->get_data($taf_data);
        // create token
        $jwt = JWT::encode($data, $this->secretKey, 'HS256');
        return $jwt;
    }
    public function check_auth()
    {
        $auth_reponse=array("status"=>false,"message"=>"");
        // try {
        //     if (!isset($_SERVER['HTTP_AUTHORIZATION']) || !preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        //         $auth_reponse["status"] = false;
        //         $auth_reponse["message"] = 'Une connexion est requise pour accéder à cette ressource';
        //         // var_dump($_SERVER);
        //     } else {
        //         $jwt = $matches[1];
        //         if (!$jwt) {
        //             $auth_reponse["status"] = false;
        //             $auth_reponse["message"] = 'Une connexion est requise pour accéder à cette ressource';
        //         } else {
        //             $token = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));
        //             $now = new DateTimeImmutable();

        //             if ($token->nbf > $now->getTimestamp()) {
        //                 $auth_reponse["status"] = false;
        //                 $auth_reponse["message"] = 'Il n\'est pas encore l\'heure de démarrer la session';
        //             }elseif ($token->exp < $now->getTimestamp()) {
        //                 $auth_reponse["status"] = false;
        //                 $auth_reponse["message"] = 'votre session est expirée';
        //             }else{
        //                 $auth_reponse["status"] = true;
        //                 $auth_reponse["message"] = 'session valide';
        //             }
        //         }
        //     }
        // } catch (\Throwable $th) {
        //     $auth_reponse["status"] = false;
        //     $auth_reponse["message"] = $th->getMessage();
        // }
        $auth_reponse["status"] = true;
        $auth_reponse["message"] = 'session valide';
        return $auth_reponse;
    }
}
