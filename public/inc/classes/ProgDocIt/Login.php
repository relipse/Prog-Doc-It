<?php
/**
 * Login class - session management for ProgDocIt
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

class Login {
    /**
     * Set the GoTo Page
     * @param string $p
     */
    public static function SetGoToPage(string $p): void{
        self::SetupSession();
        $_SESSION[Config::Get('shortname')]['gotopage'] = Data::StripDotHtml($p);
    }

    /**
     * Get the GoTo Page (after logging in, redirect)
     * @param bool $nullify
     * @return string
     */
    public static function GetGoToPage(bool $nullify = false): string {
        $gotopage = $_SESSION[Config::Get('shortname')]['gotopage'] ?? '';
        if ($nullify){
            $_SESSION[Config::Get('shortname')]['gotopage'] = '';
        }
        return $gotopage;
    }


    /**
     * Authenticate If Requested, then set up a session
     * @throws Exception
     */
    public static function AuthenticateIfRequested(string &$error = ''): void{
        if (isset($_REQUEST['u']) && isset($_REQUEST['password'])){
            $error = '';
            if (Login::Authenticate($_REQUEST['u'], $_REQUEST['password'], $error)){
                self::SetupSession($_REQUEST['u']);
            }else{

            }
        }
    }

    /**
     * Check if logged in
     * Not only check if the user has a valid session, but also check if the user from the user config exists.
     * @return bool
     */
    public static function IsLoggedIn(): bool {
        return !empty($_SESSION[Config::Get('shortname')]['loggedinuser']['username']) &&
            Config::Users($_SESSION[Config::Get('shortname')]['loggedinuser']['username']) !== null;
    }

    /**
     * Get the logged in user
     * @return mixed|null
     */
    public static function GetLoggedInUser(): ?array{
        return $_SESSION[Config::Get('shortname')]['loggedinuser'] ?? null;
    }

    /**
     * Reload the logged in user from file into $_SESSION
     * @throws Exception
     */
    public static function ReloadLoggedInUser(): void{
        if (!isset($_SESSION[Config::Get('shortname')]['loggedinuser']['username'])){
            throw new Exception('Invalid user');
        }
        $session_loggedin_username = $_SESSION[Config::Get('shortname')]['loggedinuser']['username'];
        $user_from_file = Config::Users($session_loggedin_username);
        if (empty($user_from_file)){
            throw new Exception('User in file does not exist');
        }
        // Finally, replace session
        $_SESSION[Config::Get('shortname')]['loggedinuser'] = $user_from_file;
    }

    /**
     * Logout
     */
    public static function Logout(): void {
        if (!isset($_SESSION[Config::Get('shortname')])){
            $_SESSION[Config::Get('shortname')] = [];
        }
        $_SESSION[Config::Get('shortname')]['loggedinuser'] = null;
    }

    /**
     * Set up the session, if username is null, don't set a logged in user
     * @param string $username
     * @throws Exception
     */
    public static function SetupSession(string $username = null): void {
        if (!isset($_SESSION[Config::Get('shortname')])){
            $_SESSION[Config::Get('shortname')] = [];
        }

        if (!empty($username)) {
            $user = Config::Users($username);
            if (empty($user)) {
                throw new \Exception("Invalid user: $username");
            }
            $_SESSION[Config::Get('shortname')]['loggedinuser'] = $user;
        }
    }


    /**
     * Authenticate user in cfg/users.php file.
     * @see /tools/passgen.php
     * @param string $username
     * @param string $password
     * @param string $error
     * @return bool
     */
    public static function Authenticate(string $username, string $password, string &$error): bool {
        $users = require Config::hidden_dir().'/cfg/users.php';
        if (isset($users[$username]['password'])){
            if (password_verify($password, $users[$username]['password'])){
                return true;
            }else{
                $error = 'Password is invalid.';
                return false;
            }
        }else{
            $error = 'User '.$username.' does not exist.';
            return false;
        }
    }
}
