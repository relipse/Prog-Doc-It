<?php
/**
 * Page class for dealing with ProgDocIt createad pages
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

class Page {
    /**
     * Return <page-name>.html
     * @param string $p
     * @return string
     */
    public static function PageNameWithDotHtml(string $p){
        return Data::StripDotHtml(self::SanitizeFilename($p)).'.html';
    }


    /**
     * Sanitize the file name
     * @param string $p
     * @return array|string|string[]|null
     */
    public static function SanitizeFilename(string $p){
        $s = str_replace('..','', $p);
        $s = preg_replace('/[^a-z.0-9_\-]/i','', $s);
        return $s;
    }

    /**
     * Sanitize the html
     * @param string $html
     * @return string
     */
    public static function SanitizeHTML(string $html): string{
        //$html = strip_tags($html, Config::Get('allowed_tags'));
        return $html;
    }

    /**
     * Delete a page (file)
     * @param string $file
     * @return bool
     * @throws Exception
     */
    public static function Delete(string $file): bool {
        $p = Page::SanitizeFilename($file);
        if ($p !== $file){
            throw new \Exception('Filename error');
        }
        $fullpath = Data::GetFullPagePath($p);
        if (empty($fullpath)){
            return false;
        }
        if (Config::Get('backups') === true) {
            //Make a backup
            copy($fullpath, $fullpath . '.' . date('YmdHis').'.backup');
        }
        $actual_page_deleted = unlink($fullpath);
        $infopath = Page::GetInfoPath($fullpath);
        if ($infopath){
            if (Config::Get('backups') === true) {
                //Make a backup
                copy($infopath, $infopath . '.' . date('YmdHis').'.backup');
            }
            $infopath_deleted = unlink($infopath);
        }
        return !empty($actual_page_deleted) && !empty($infopath_deleted);
    }

    /**
     * Save file and contents
     * @param string $file
     * @param string $contents
     * @param string $error
     * @param string $oldpage
     * @return bool
     * @throws Exception
     */
    public static function Save(string $file, string& $contents, string &$error, string $oldpage): bool {
        $error = '';
        $p = Page::SanitizeFilename($file);
        if ($p !== $file){
            throw new \Exception('Filename error');
        }
        $oldpage = Page::SanitizeFilename($oldpage);
        $oldfullpath = Data::GetDir()."/$oldpage";
        $fullpath = Data::GetDir()."/".Page::PageNameWithDotHtml($p);
        $contents = Page::SanitizeHTML($contents);

        if (file_exists($fullpath)){
            if (is_dir($fullpath)){
                throw new \Exception('Cannot edit a directory.');
            }
            if (Config::Get('backups') === true) {
                //Make a backup
                copy($fullpath, $fullpath . '.' . date('YmdHis').'.backup');
            }

            if (file_put_contents($fullpath, $contents) === false){
                throw new \Exception('Could not write');
            }
            $info = self::GetInfo($fullpath);
            if (!isset($info['created'])){
                $info['created'] = Login::GetLoggedInUser()['username'];
            }
            if (!isset($info['created_date'])){
                $info['created_date'] = date('Y-m-d H:i:s');
            }
            $info['modified'] = Login::GetLoggedInUser()['username'];
            $info['modified_date'] = date('Y-m-d H:i:s');

            $oldinfofilepath = self::GetInfoPath($oldfullpath);
            if (!empty($oldinfofilepath)){
                unlink($oldinfofilepath);
            }
            self::PutInfo($info, $fullpath);
            return true;
        }else{
            // File does not exist, create it
            if (file_put_contents($fullpath, $contents) === false){
                throw new \Exception('Could not write');
            }
            $info = [
                'created'=> Login::GetLoggedInUser()['username'],
                'created_date' => date('Y-m-d H:i:s'),
                'modified'=> Login::GetLoggedInUser()['username'],
                'modified_date' => date('Y-m-d H:i:s'),
            ];
            self::PutInfo($info, $fullpath);
            return true;
        }
        return false;
    }

    /**
     * Put Info array into the .info.json file
     * @param array $info
     * @param string $fullpagepath
     * @return bool
     */
    public static function PutInfo(array $info, string $fullpagepath): bool {
        if (strpos($fullpagepath, Data::GetDir()) !== 0){
            // not in data directory
            return false;
        }
        if (!isset($info['created'])){
            // it must have a created
            return false;
        }
        if (file_put_contents("$fullpagepath.info.json", json_encode($info)) === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get Info file path based on pagepath
     * @param string $fullpathtopage
     * @return string|null
     */
    public static function GetInfoPath(string $fullpathtopage): ?string {
        if (file_exists($fullpathtopage.'.info.json')) {
            return $fullpathtopage . '.info.json';
        }else{
            return null;
        }
    }

    /**
     * Get Info based on full page path
     * @param string $fullpagepath
     * @return array
     */
    public static function GetInfo(string $fullpagepath): array{
        if (file_exists("$fullpagepath.info.json")){
            $contents = file_get_contents("$fullpagepath.info.json");
            $info = json_decode($contents, true);
            return $info;
        }else{
            return [];
        }
    }

    /**
     * Transform the progdocithtml, which is basically html that allows ``` for code blocks
     * @param $progdocithtml
     * @return string
     */
    public static function Transform(string $progdocithtml): string{
        $transformedhtml = $progdocithtml;
        preg_match_all('/```(.*?)```/s', $progdocithtml, $result, PREG_SET_ORDER);
        for ($matchi = 0; $matchi < count($result); $matchi++) {
            $codeblock = $result[$matchi][1];
            // This is a code block, we will convert it to a <pre>
            $transformedhtml = str_replace('```'.$codeblock.'```', '<pre class="codeblock">'.htmlentities($codeblock).'</pre>', $transformedhtml);
        }
        // we can now strip the tags because the code blocks have run htmlentities on them
        $transformedhtml = strip_tags($transformedhtml, Config::Get('allowed_tags'));
        //now, we will remove on* attributes on remaining html
        $transformedhtml = static::RemoveOnScriptAttributes($transformedhtml);
        return $transformedhtml;
    }

    /**
     * Remove on* attributes from html because those are dangerous and allow script injection
     * @param string $html
     * @return string
     */
    public static function RemoveOnScriptAttributes(string $html): string{
        $htmlnoscriptattributes = $html;
        // Keep doing preg_replace, until no more deadly attributes are found
        while (preg_match('/(<[^>]*)(\bon\w+\s*=\s*[^\b\s>]+)/', $htmlnoscriptattributes)) {
            $htmlnoscriptattributes = preg_replace('/(<[^>]*)(\bon\w+\s*=\s*[^\b\s>]+)/', '$1', $htmlnoscriptattributes);
        }
        return $htmlnoscriptattributes;
    }
}
