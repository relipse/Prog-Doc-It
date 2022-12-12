<?php
/**
 * Edit, Delete, Move, Copy (Save As) an existing page
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;
require_once(__DIR__ . '/inc/inc.php');
if (isset($_POST['p']) && isset($_POST['contents']) && Login::IsLoggedIn()){
    $error = '';
    $pagetosave = $_POST['p'];
    if (!empty($_POST['delete'])){
        Page::Delete($pagetosave);
        header("Location: ".Config::dircfg('base_url'));
        exit;
    }
    $oldpage = $pagetosave;
    if (!empty($_POST['saveas']) || (!empty($_POST['moveas']))){
        if (empty($_POST['saveasname'])){
            throw new \Exception('Save As Name must not be empty.');
        }
        $oldpage = $pagetosave;
        $pagetosave = Page::SanitizeFilename($_POST['saveasname']).'.html';

        if (strpos($pagetosave,'.html.html') !== false){
            $pagetosave = str_replace('.html.html','.html', $pagetosave);
        }
        if (empty($pagetosave) || $pagetosave == '.html'){
            throw new Exception('Invalid page name');
        }
        if (strtolower($oldpage) != strtolower($pagetosave) && !empty($_POST['moveas'])){
            $moveas = true;
        }

        $forwardtonewpage = true;
    }

    Page::Save($pagetosave, $_POST['contents'], $error, $oldpage);

    if (!empty($oldpage) && !empty($moveas)){
        Page::Delete($oldpage);
    }
    if (!empty($forwardtonewpage)){
        header("Location: ".Config::dircfg('base_url')."/?p=".urlencode(Data::StripDotHtml($pagetosave)));
        exit;
    }
}


$title = 'New Page';
$tagline = Config::Get('tagline');
$action = 'new';
$contents = '';
$p = '';
if (isset($_GET['p'])){
    $title = 'Edit '.Page::SanitizeFilename($_GET['p']);
    $action = 'editing';
}
include(__DIR__ . '/inc/header.php');
?>
<form class="frmCodeSave drop-zone" method="post">
        <div id="drag_upload_file">
            <p>Paste Image (CTRL+V) in editor or Drag and Drop image here or <input type="button" value="Upload Image" onclick="file_explorer();" /></p>
            <input type="file" id="selectfile" />
        </div>
    <div class="upload-status"></div>
    <p>Allowed HTML Tags: <b><?=htmlentities(Config::Get('allowed_tags'))?></b><br>
        For code, surround with three backticks: <b>```&lt;script src="foo.js"&gt;&lt;/script&gt;```</b></p>
    <input type="hidden" name="p" value="<?=htmlentities($p)?>">
    <div>
        <label for="themes">Editor Theme: </label><select id="themes">
        </select>
        <button type="button" id="savetheme">Save Theme as Default</button>
    </div>
    <textarea class="htmleditor" data-editor="html" name="contents" rows="15" ace-theme="<?=htmlentities(Login::GetLoggedInUser()['editor_theme']??'ace/theme/chrome')?>" ace-gutter="true"><?=
        htmlentities($contents)
    ?></textarea>
    <?php if ($action == 'editing'){ ?>
        <button id="regularboringsave" type="submit" title="Save this file">Save</button>
        <input type="submit" name="moveas" value="Move As" title="Move to new file"
               onclick="if (document.getElementsByName('saveasname')[0].value == ''){
            alert('Enter a page name.'); return false;
        }">
    <?php } ?>
    <input type="submit" name="saveas" value="Save<?php if ($action == 'editing'){ ?> As<?php } ?>" title="Save as new file"
           onclick="if (document.getElementsByName('saveasname')[0].value == ''){
        alert('Enter a page name.'); return false;
    }"><input type="text" name="saveasname" value="<?=isset($p) ? Data::StripDotHtml($p) : ''?>" placeholder="new-page-name">
            <?php if ($action == 'editing') { ?>
    <input type="submit" name="delete" value="Delete Page" title="Delete this page" onclick="if (!confirm('Are you sure you wish to delete: ' + document.getElementsByName('p')[0].value)){
        return false;
    }">
    <?php } ?>

</form>
<?php
// Set up local JavaScript variables to pass to edit.js script (via global variables)
?>
<script>
    var selected_theme = <?=json_encode(Login::GetLoggedInUser()['editor_theme']??'')?>;
    var p = <?=json_encode(isset($p) ? Data::StripDotHtml($p) : '')?>;
</script>
<script src="js/edit.js"></script>
<?php
include(__DIR__ . '/inc/footer.php');