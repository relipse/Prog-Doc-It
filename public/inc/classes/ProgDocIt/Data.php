<?php
/**
 * Data class, to deal with data directory
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

class Data{
    /**
     * Get the data directory path
     * @return string
     */
    public static function GetDir(): string {
        $pwd = Config::hidden_dir().'/data';
        if (file_exists($pwd)){
            return $pwd;
        }
        return false;
    }

    /**
     * Check if .html file
     * @param $filename
     * @return bool
     */
    public static function IsHtmlFile($filename): bool{
        return (substr ($filename, -5) == '.html');
    }

    /**
     * Strip .html
     * @param $p
     * @return array|string|string[]
     */
    public static function StripDotHtml($p){
        return str_replace('.html','',$p);
    }

    /**
     * Does page exist?
     * @param $p
     * @return bool|string
     */
    public static function Exists($p) {
        $p = self::StripDotHtml($p).'.html';
        $pwd = Data::GetDir();
        if (file_exists($pwd.'/'.$p)) {
            if (is_file($pwd . '/' . $p) && Data::IsHtmlFile($p)) {
                return $pwd . '/' . $p;
            }
        }
        return false;
    }

    /**
     * Check if exists and return full page path
     * @param $p
     * @return string
     */
    public static function GetFullPagePath($p): string {
        $fullpath = self::Exists($p);
        if ($fullpath !== false){
            return $fullpath;
        }else{
            return '';
        }
    }

    /**
     * Get the Content of the page
     * @param $p
     * @return string|null
     */
    public static function GetContent($p): ?string {
        if (self::Exists($p)){
            return file_get_contents(Data::GetDir().'/'.self::StripDotHtml($p).'.html');
        }
        return null;
    }

    /**
     * Get all files in the data directory
     * @param string $dir subdir to scan
     * @return array
     */
    public static function GetAll(string $dir = ''): array{
        $dir = Page::SanitizeFilename($dir);
        $pwd = self::GetDir();
        if (strlen($dir) > 0){
            $pwd .= '/'.$dir;
        }
        $files = scandir($pwd);
        $valid = [];
        $validdirs = [];
        foreach($files as $f){
            if ($f == '.' || $f == '..'){
                continue;
            }
            if (is_dir($pwd.'/'.$f)){
                // Do not use sub-directories at this time
                //$validdirs[] = $f;
            }else{
                if (self::IsHtmlFile($f)) {
                    $valid[] = $f;
                }
            }
        }
        return array_merge($valid,$validdirs);
    }
}
