<?php
if (isset($argv[1])) {
    $password = $argv[1];
}else {
    $password = readline('Enter a password: ');
}
$hash = password_hash($password, PASSWORD_BCRYPT);
echo var_export($hash);
