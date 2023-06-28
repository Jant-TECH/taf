<?php
/* require './TafConfig.php';
$taf_config = new \Taf\TafConfig();
$taf_config->disconnect_documentation_auth(); */
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $url = "https://";
} else {
    $url = "http://";
}
// Append the host(domain name, ip) to the URL.   
$url .= $_SERVER['HTTP_HOST'];
$url .= dirname($_SERVER['REQUEST_URI']) . "/";
echo $url;
