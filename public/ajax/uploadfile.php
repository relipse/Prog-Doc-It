<?php
/**
 * Upload file to /datauploads/
 * @author James Kinsman
 * @copyright 2021 
 */
namespace ProgDocIt;

require_once(__DIR__.'/../inc/inc.php');
if (!Login::IsLoggedIn()){
    die("");
}

if (empty($_FILES['file']['name'])){
    die('');
}

$allowed_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];

if (!(in_array($_FILES['file']['type'], $allowed_types))) {
    die();
}

$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
//$mimetype = MimeTypes::GetMimeType($extension);

// Grab a short cut of the md5 of the filename, to save images as short as possible, if collision, increase the length until no more collision
$length = 3;
do {
    $filename = substr(md5(time().Page::SanitizeFilename($_FILES['file']['name'])), 0, $length) . ".$extension";
    $saveto = Config::hidden_dir() . '/datauploads/' . $filename;
    $length++;
}while(file_exists($saveto));

//$filename = date('Ymd_His').'_' . substr(md5(Page::SanitizeFilename($_FILES['file']['name'])), 0, 5).".$extension";

$moved = move_uploaded_file($_FILES['file']['tmp_name'], $saveto);

if (empty($moved)){
    die("");
}

die('<img alt="'.htmlentities(Page::SanitizeFilename($_FILES['file']['name'])).'" style="width: 70%" src="srv.php?src=' . urlencode($filename).'">');
