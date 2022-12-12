<?php
/**
 * User settings
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

require_once(__DIR__ . '/inc/inc.php');
if (isset($_POST['new_password'])){
    $error = false;
    $success = false;
    if (empty($_POST['new_password'])){
        $error = 'New password cannot be empty.';
    }else if ($_POST['new_password'] != $_POST['new_password_confirm']){
        $error = "New password does not match confirmation.";
    }else if (!password_verify($_POST['current_password'], Login::GetLoggedInUser()['password'])){
        $error = 'Current password does not match.';
    }else{
        $allusers = include Config::hidden_dir().'/cfg/users.php';
        if (isset($allusers[Login::GetLoggedInUser()['username']]['password'])){
            $allusers[Login::GetLoggedInUser()['username']]['password'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            if (Config::Get('backups')){
                copy(Config::hidden_dir().'/cfg/users.php',Config::hidden_dir().'/cfg/users.backup-'.date('YmdHis').'.php');
            }
            $code = Config::Array2PHPCode($allusers);
            if (file_put_contents(Config::hidden_dir().'/cfg/users.php', $code)){
                $newallusers = Config::hidden_dir().'/cfg/users.php';
                if (is_array($newallusers)){
                    try {
                        Login::ReloadLoggedInUser();
                    }catch(\Exception $e){
                        $error = $e->getMessage();
                    }
                    $success = 'You have updated your password!';
                }
            }else{
                $error = 'File write error.';
            }
        }else{
            $error = 'User does not exist';
        }
    }
}
$title = 'Settings';
$tagline = Config::Get('tagline');

include(__DIR__ . '/inc/header.php');
Login::ReloadLoggedInUser();
$user = Login::GetLoggedInUser();
unset($user['password']);
?>
<h3>My Information</h3>
<div class="myinfo">
    <?=array2table($user)?>
</div>
<form method="post">
    <h3>Update Password</h3>
    <table class="password_changer">
        <tr>
            <td><label for="current_password">Current Password</label></td>
            <td><input id="current_password" type="password" name="current_password" required></td>
        </tr>
        <tr>
            <td><label for="new_password">New Password</label></td>
            <td><input id="new_password" type="password" name="new_password" minlength="1" required></td>
        </tr>
        <tr>
            <td><label for="new_password_confirm">Confirm New Password</label></td>
            <td><input id="new_password_confirm" type="password" name="new_password_confirm" required></td>
        <tr>
            <td><input type="submit" name="update_password" value="Update Password"></td>
        </tr>
    </table>
</form><?php
include(__DIR__ . '/inc/footer.php');