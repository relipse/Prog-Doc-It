<?php
/**
 * Login Page, accept login on inc.php
 * @author James Kinsman
 * @copyright 2021 
 */

namespace ProgDocIt;

require_once(__DIR__ . '/inc/inc.php');
if (Login::IsLoggedIn() && Login::GetGoToPage()){
    // Are we forwarding to a page?
    header("Location: ".Config::dircfg('base_url').'/?p='.urlencode(Data::StripDotHtml(Login::GetGoToPage(true))));
    exit;
}

$title = 'Login';
$tagline = Config::Get('tagline');
include(__DIR__ . '/inc/header.php');

if (!Login::IsLoggedIn()){
?><form class="frmLogin" method="post">
        <div>
            <label for="username">Username: </label><input id="username" type="text" name="u">
        </div>
        <div>
            <label for="password">Password: </label><input id="password" type="password" name="password">
        </div>
    <button type="submit">Login</button>
</form>
<?php
}else{
    ?><p>Logged in!</p><?php
}
include(__DIR__ . '/inc/footer.php');
