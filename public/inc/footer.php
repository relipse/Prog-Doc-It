<?php
/**
 * Footer shown on every page
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

if (Login::IsLoggedIn()){
    $data = Data::GetAll();
    if (!empty($data)){
        ?><h4 class="sitemaptitle">Site Map</h4><?php
    }
    ?><menu class="sitemap"><?php
    foreach($data as $d){
        if (Data::IsHtmlFile($d)){
            $nohtml = Data::StripDotHtml($d);
            ?><li><a href="<?=Config::dircfg('base_url')?>/?p=<?=urlencode($nohtml)?>"><?=htmlentities($nohtml)?></a></li><?php
        }else{ // a directory
            ?><li><a href="<?=Config::dircfg('base_url')?>/?d=<?=urlencode($d)?>"><?=htmlentities($d)?></a></li><?php
        }
    }
    ?></menu><?php
}
?>
<footer>
    <menu>
        <li>Copyright (C) <?=date('Y')?> <a href="<?=Config::dircfg('base_url')?>/"><?=Config::Get('site_name')?></a></li>

        <?php
        if (Login::IsLoggedIn()){
            ?><li>Logged in as <?=Login::GetLoggedInUser()['username']?> (<a href="logout.php">logout</a>)</li><?php
        } ?>

        <li><a href="new.php">new page</a></li>
    </menu>
    <script src="js/main.js"></script>
</footer>
</body>
</html>
