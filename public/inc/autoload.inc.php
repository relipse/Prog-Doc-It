<?php
/**
 * Class autoloader
 * @author James Kinsman
 * @copyright 2021 
 */

spl_autoload_register(function ($class) {
    $filepath = str_replace('\\', '/', $class).'.php';
    $file = __DIR__ . '/classes/' .$filepath;
    if (file_exists($file)){
        require_once($file);
    }
});
