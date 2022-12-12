<?php
/**
 * Config class to deal with /cfg/* files which are php files stored as array.
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

class Config {
    protected static $cache = [];

    /**
     * Get the directory config
     * @param string|null $key
     * @return mixed|null
     */
    public static function dircfg(string $key = null) {
        $dircfg = require __DIR__.'/../../dircfg.php';
        if (is_null($key)){
            return $dircfg;
        }

        if (isset($dircfg[$key])){
            return $dircfg[$key];
        }else{
            return null;
        }
    }

    /**
     * Get the hidden dir
     * @return string
     * @throws \Exception
     */
    public static function hidden_dir(): string {
        if (!isset(self::$cache['hidden'])) {
            if (file_exists(self::dircfg('hidden'))){
                self::$cache['hidden'] = self::dircfg('hidden');
            }else {
                $first = '/../../..';
                if (is_dir($first . '/cfg') &&
                    is_dir($first . '/data') &&
                    is_dir($first . '/inc')) {
                    self::$cache['hidden'] = $first;
                } else {
                    throw new \Exception('Hidden directory does not exist or is invalid.');
                }
            }
        }
        return self::$cache['hidden'];
    }

    /**
     * Get config value (or all)
     * @param string|null $key
     * @return mixed|null
     */
    public static function Get(string $key = null){
        $cfg = include self::hidden_dir().'/cfg/config.php';
        if (is_null($key)){
            return $cfg;
        }else{
            return $cfg[$key] ?? null;
        }
    }

    /**
     * Get Users (or one user row)
     * @param string|null $username
     * @return array|null
     */
    public static function Users(string $username = null) : ?array{
        $users = include self::hidden_dir().'/cfg/users.php';
        if (is_null($username)){
            return $users;
        }else{
            return $users[$username] ?? null;
        }
    }

    /**
     * Generate PHP Code from $array
     * @param array $array
     * @return string
     */
    public static function Array2PHPCode(array $array): string {
        $s = '<?php '."\n//This file has been generated, changes will be overridden.\n".'return ';
        $s .= var_export($array, true);
        $s .= '; '."\n";
        return $s;
    }

    /**
     * Save Settings to users config file
     * @param string $username
     * @param array $update_array
     * @return bool
     * @throws Exception
     */
    public static function SaveSetting(string $username, array $update_array): bool {
        $allusers = self::Users();

        if (empty($allusers[$username])){
            throw new \Exception("User $username does not exist.");
        }
        $user = $allusers[$username];
        // only allow modifying of specific items
        $whitelist = ['editor_theme'];
        $finalarray = [];
        foreach($update_array as $key => $value){
            if (in_array($key, $whitelist)){
                $finalarray[$key] = $value;
            }else{
                throw new \Exception($key.' is not valid (does not match whitelist)');
            }
        }
        $user2 = array_merge($user, $finalarray);
        // Update user
        $allusers[$username] = $user2;
        $userscfg = self::Array2PHPCode($allusers);

        // Save to file (yeah this is why we use databases, normally)
        if (file_put_contents(Config::hidden_dir().'/cfg/users.php', $userscfg)){
            if (!is_array(self::Users())){
                throw new \Exception('File format is invalid.');
            }
            return true;
        }else{
            throw new Exception('Could not save user file');
        }
        return false;
    }
}
