<?php
/**
 * Logout of the site
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

session_start();
require_once(__DIR__ . '/inc/autoload.inc.php');
Login::Logout();
header('Location: '.Config::dircfg('base_url'));
exit;
