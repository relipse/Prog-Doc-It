<?php
/**
 * Main page for ProgDocIt
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;
require_once(__DIR__ . '/inc/inc.php');
$title = Config::Get('site_name');
$tagline = Config::Get('tagline');
include(__DIR__ . '/inc/header.php');
if (empty($p)){
    if (file_exists(Config::dircfg('pubhtml').'/images/logo.png')){
        ?><img class="biglogo" alt="<?=htmlentities($title)?>" src="images/logo.png"><?php
    }
    ?>
    <p>Welcome to <?=Config::Get('site_name')?> a wiki-style web application for documentation. Click one of the pages below.</p>
    <?php
}
include(__DIR__ . '/inc/footer.php');