<?php
/**
 * Directory Listing Page (disabled)
 * @author James Kinsman
 * @copyright 2021 
 */

//disable directory editing for now
exit;

require_once(__DIR__ . '/inc/inc.php');
if (isset($_POST['p']) && Login::IsLoggedIn()){
    $error = '';
    $p = Page::SanitizeFilename($_POST['p']);
    if (!empty($_POST['delete'])){
        Page::Delete($p);
        header("Location: ".Config::dircfg('base_url'));
        exit;
    }

    if (!empty($_POST['dirname'])){
        $dir = Page::SanitizeFilename($_POST['dirname']);
        if (empty($dir)){
            throw new \Exception('Invalid dir.');
        }
        if (!empty($_POST['save'])) {
            mkdir(Data::GetDir() . '/' . $dir, 0660, false);
        }else{
            rename(Data::GetDir().'/'.$p, Data::GetDir().'/'.$dir);
        }
        header("Location: ".Config::dircfg('base_url')."/?p=$dir");
        exit;
    }
}


$title = 'New Directory';
$action = 'new';
$contents = '';
$p = '';
if (isset($_GET['p'])){
    $action = 'editing';
    $title = 'Edit '.Page::SanitizeFilename($_GET['p']);
}
include(__DIR__ . '/inc/header.php');
?>
<form method="post">
    <input type="hidden" name="p" value="<?=htmlentities($p)?>">
    <?php if ($action == 'editing'){ ?><input type="submit" name="rename" value="Rename" title="Rename this dir"
           <?php } else {?><input type="submit" name="save" value="Save" title="Save new directory"<?php } ?> onclick="if (document.getElementsByName('dirname')[0].value == ''){
        alert('Enter a directory name.'); return false;
    }"><input type="text" name="dirname" value="<?=$p?>" placeholder="new-dir-name"><?php if ($action == 'editing'){ ?>
    <input type="submit" name="delete" value="Delete Directory" title="Delete this directory" onclick="if (!confirm('Are you sure you wish to delete: ' + document.getElementsByName('p')[0].value)){
        return false;
    }"><?php } ?>
</form>
<?php
include(__DIR__ . '/inc/footer.php');