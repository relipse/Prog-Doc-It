<?php
/**
 * Serve a file (or image) that is protected and stored in /datauploads
 * Only Members can be served files or images with this script
 * @author James Kinsman
 * @copyright 2021 
 */
namespace ProgDocIt;

const SERVE_FILE = true;
require_once(__DIR__.'/inc/inc.php');

if (empty($_GET['src'])){
    header("HTTP/1.0 404 Not Found");
    exit;
}
$src = Page::SanitizeFilename($_GET['src']);
$fullpath = Config::hidden_dir().'/datauploads/'.$src;

if (!\file_exists($fullpath)){
    header("HTTP/1.0 404 Not Found");
    exit;
}

$extension = pathinfo($fullpath, PATHINFO_EXTENSION);
// You need to install extension for the below to work.
//$mimetype = \mime_content_type($fullpath);

//work around:
$mimetype = MimeTypes::GetMimeType($extension);

if (empty($mimetype)){
    header("HTTP/1.0 404 Not Found");
    exit;
}

$filesize = \filesize($fullpath);
if ($filesize === false){
    header("HTTP/1.0 404 Not Found");
    exit;
}

$filesize_oct = \decoct($filesize);
$filemtime = \filemtime($fullpath);
if ($filemtime === false){
    header("HTTP/1.0 404 Not Found");
    exit;
}

header('Content-Type: '.$mimetype);
header('Content-Length: '.$filesize_oct);
header('Last-Modified: '.\date(DATE_RFC2822, $filemtime));

readfile($fullpath);
