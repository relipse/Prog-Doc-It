<?php
/**
 * Save Theme via Ajax
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

header('Content-type: application/json; charset=utf-8');
require_once(__DIR__ . '/../inc/inc.php');

if (!Login::IsLoggedIn()){
    die(json_encode(['error'=>'Not logged in.']));
}

if (isset($_GET['theme'])){
    try {
        Config::SaveSetting(Login::GetLoggedInUser()['username'], ['editor_theme' => $_GET['theme']]);
        Login::ReloadLoggedInUser();

        die(json_encode(['success'=>true,'error'=>false]));
    }catch(\Exception $e){
        die(json_encode(['error'=>$e->getMessage()]));
    }
}
die(json_encode(['error'=>'Invalid command.', 'echo'=>$_GET]));
