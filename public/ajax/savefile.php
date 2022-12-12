<?php
/**
 * Save file via Ajax
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

header('Content-type: application/json; charset=utf-8');
require_once(__DIR__ . '/../inc/inc.php');

if (!Login::IsLoggedIn()){
    die(json_encode(['error'=>'Not logged in.']));
}

try {
    if (isset($_POST['p']) && isset($_POST['contents'])) {
        $res = [];
        $error = '';
        $pagetosave = $_POST['p'];
        $res['pagetosave'] = $pagetosave;
        if (!empty($_POST['delete'])) {
            $res['deleted'] = Page::Delete($pagetosave);
            $res['success'] = $res['deleted'] ? 1 : 0;
            if ($res['deleted']) {
                $res['forwardto'] = Config::dircfg('base_url');
            }
            die(json_encode($res));
            exit;
        }
        $oldpage = $pagetosave;
        if (!empty($_POST['saveas']) || (!empty($_POST['moveas']))) {
            if (empty($_POST['saveasname'])) {
                throw new \Exception('Save As Name must not be empty.');
            }
            $oldpage = $pagetosave;
            $pagetosave = Page::SanitizeFilename($_POST['saveasname']) . '.html';

            if (strpos($pagetosave, '.html.html') !== false) {
                $pagetosave = str_replace('.html.html', '.html', $pagetosave);
            }
            if (empty($pagetosave) || $pagetosave == '.html') {
                throw new Exception('Invalid page name');
            }
            $res['oldpage'] = $oldpage;
            $res['pagetosave'] = $pagetosave;

            if (strtolower($oldpage) != strtolower($pagetosave) && !empty($_POST['moveas'])) {
                $moveas = true;
                $res['moveas'] = true;
            }

            $forwardtonewpage = true;
        }
        $contents_sanitized = $_POST['contents'];
        $res['saved'] = Page::Save($pagetosave, $contents_sanitized, $error, $oldpage);
        $contents_transformed = Page::Transform($contents_sanitized);

        if (!empty($oldpage) && !empty($moveas)) {
            $res['oldpage'] = $oldpage;
            $res['deleted_oldpage'] = Page::Delete($oldpage);
        }

        if (!empty($forwardtonewpage)) {

            $res['forwardto'] = Config::dircfg('base_url') . "/?p=" . urlencode(Data::StripDotHtml($pagetosave));
            $res['page'] = Data::StripDotHtml($pagetosave);
            if (empty($error)) {
                $res['success'] = 1;
            } else {
                $res['error'] = $error;
            }
            die(json_encode($res));
        }

        if ($res['saved']){
            $res['success'] = 1;
            //this is used in the editor
            $res['contents_source'] = $contents_sanitized;
            //this is used when viewing
            $res['contents_html'] = $contents_transformed;
        }
        die(json_encode($res));
    }
}catch(\Throwable $e){
    $res['error'] = $e->getMessage();
    die(json_encode($res));
}

$res['error'] = "POST['p'] and POST['contents'] must exist.";
die(json_encode($res));
