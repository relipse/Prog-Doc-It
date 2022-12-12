<?php
/**
 * Primary include required for all pages
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

session_start();
ini_set('display_errors', 1);
require_once(__DIR__ . '/autoload.inc.php');
require_once(__DIR__ . '/misc.inc.php');

if (defined('SERVE_FILE') && SERVE_FILE){
    // We are trying to serve a file
    if (!Login::IsLoggedIn()){
        header("HTTP/1.0 404 Not Found");
        exit;
    }
}


if (!Login::IsLoggedIn()){
    $error = false;
    Login::AuthenticateIfRequested($error);
    // If not logged in, forward to login.php (unless already there)
    $curpagename = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
    if ($curpagename != 'login.php') {
        Login::SetGoToPage($_GET['p'] ?? '');
        header('Location: login.php');
        exit;
    }
}
