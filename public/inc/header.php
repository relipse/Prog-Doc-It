<?php
/**
 * Header shown on every page
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;
?>
<!doctype html>
<html lang="en">
<head>
    <title><?php if (isset($title) && $title != Config::Get('site_name')){ ?><?=$title?> - <?php } ?><?=Config::Get('site_name')?></title>
    <style>
        body {
            font-family: <?=Config::Get('body-font-family')?>;
            font-size: <?=Config::Get('body-font-size')?>;
        }
    </style>
    <?php
    if (file_exists(Config::dircfg('pubhtml').'/images/logo-icon.png')){
        ?>
        <link rel="icon" href="<?=Config::dircfg('base_url')?>/images/logo-icon.png" sizes="any">
        <?php
        }
    ?>
    <link rel="stylesheet" href="css/main.css" type="text/css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="jslib/ace/src-min-noconflict/ace.js"></script>
    <link rel="stylesheet" href="jslib/ace/css/ace.css">
</head>
<body>
    <div class="top-bar">
    <?php
    if (file_exists(Config::dircfg('pubhtml').'/images/logo.png')){
        $showlogo = true;
        ?><div class="top-bar-item logo"><a href="index.php"><img alt="<?=htmlentities($title)?>" src="<?=Config::dircfg('base_url')?>/images/logo.png"></a></div><?php
    }
    if (empty($showlogo) && isset($title)){?>
         <div class="top-bar-item"><h1 class="maintitle"><?=htmlentities($title)?></h1></div>
         <?php
    }
    if (isset($tagline)){?>
        <div class="top-bar-item center"><h2 class="tagline"><a href="<?=Config::dircfg('base_url')?>"><?=htmlentities($tagline)?></a></h2></div>
         <?php
    }
    ?>
        <div class="top-bar-item">
            <span class="loggedinusername"><?=Login::IsLoggedIn() ? Login::GetLoggedInUser()['username'] : 'Not logged in'?></span>
            <a title="Menu" href="#menu" class="box-shadow-menu"></a>
            <menu class="top-bar-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="sitemap.php">Site Map</a></li>
                <li><a href="new.php">New Page</a></li>
                <?php
                if (Login::IsLoggedIn()){
                ?>
                    <li><a href="settings.php" title="Settings">Settings (<?=Login::GetLoggedInUser()['username']?>)</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php
                }
                ?>
            </menu>
        </div>
    </div>
<?php
if (!empty($error)){
    ?><div class="box error"><?=htmlentities($error)?></div><?php
}
if (!empty($success)){
    ?><div class="box success"><?=htmlentities($success)?></div><?php
}
if (isset($_REQUEST['p'])) {
    $p = Page::SanitizeFilename($_REQUEST['p']);
}else if (isset($_REQUEST['d'])) {
    $p = Page::SanitizeFilename($_REQUEST['d']);
}

 if (!empty($p)){
     if (strpos($p,'.html') !== false){
         $p = str_replace('.html','',$p);
         if (empty($p)){
             throw new Exception('Invalid page name');
         }
     }
     $pwd = Data::GetDir();
     if (Data::Exists($p)) {
             $contents = Data::GetContent($p);
             $tcontents = Page::Transform($contents);
             echo '<main class="transformed_content">
' . $tcontents . '
</main>';
             $info = Page::GetInfo($pwd.'/'.Data::StripDotHtml($p).'.html');
             if (!empty($info)){
                 ?><div class="who"><?php
                 if (isset($info['created'])){
                     ?>Created by <b class="created_who"><?=$info['created']?></b><?php
                 }
                 if (isset($info['created_date'])){
                     ?> (<b><span class="created_date" title="<?=$info['created_date']?>"><?=TimeUtil::Ago($info['created_date'])?></span></b>)<?php
                 }
                 if (isset($info['modified'])){
                     ?>, Modified by <b class="modified_who"><?=$info['modified']?></b> <?php
                 }
                 if (isset($info['modified_date'])){
                     ?> (<b><span class="modified_date" title="<?=$info['modified_date']?>"><?=TimeUtil::Ago($info['modified_date'])?></span></b>) <?php
                 }
                 ?></div><?php
             }

             if (basename($_SERVER["SCRIPT_FILENAME"]) != 'edit.php'){
                 ?><div class="pagecontrols">
                    <a class="button" href="edit.php?p=<?=$p?>" title="Edit (or Delete, Copy, Move) <?=$p?>">Edit Page</a>
                 </div>
                 <?php
             }
             ?><br class="clear"><?php
         /*****}else if (is_dir("$pwd/$p")){
             $filesindir = Data::GetAll($p);
             if (!empty($filesindir)){
                 ?><ul><?php
                 foreach($filesindir as $ford){
                     ?><li><a href="edit.php?p=<?=$p.'/'.$ford?>"><?=$ford?></a></li><?php
                 }
                 ?></ul><?php
             }
             ?><a href="dir.php?p=<?=$p?>" title="Edit Directory <?=$p?>">edit</a><?php
          }else{
             $fourzerofour = true;
         }
       **********/
     }else{
         $fourzerofour = true;
     }

     if (!empty($fourzerofour)){
        ?><h2>404</h2>
        <p><?=$p?> is not a valid page.</p><?php
     }
 }