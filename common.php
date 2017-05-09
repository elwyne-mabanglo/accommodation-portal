<?php
# Common - Used to setup database connection, sessions and enable HTTPs.
# 
# Reference Material:
# - Common File Content: http://forums.devshed.com/php-faqs-stickies-167/program-basic-secure-login-system-using-php-mysql-891201.html
# - Enaled HTTPS: https://stackoverflow.com/questions/3865143/what-do-i-have-to-code-to-use-https
#
# enable HTTPS
if ($_SERVER['SERVER_PORT'] !== 443 &&
        (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
# database details
$username = "me324";
$password = "12El05ma90";
$host = "mysql.cms.gre.ac.uk";
$dbname = "me324";
# allows storage of special characters
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
try {
    # setup connection
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
    # catch exceptions
} catch (PDOException $ex) {
    # Prevent unexpected behaviour after redirection
    die("Failed to connect to the database: " . $ex->getMessage());
}
# configures PDO to throw an exception when it encounters an error
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
# configures PDO to return database rows from your database using an associative array.  
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
# removes magic quotes
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {

    function undo_magic_quotes_gpc(&$array) {
        foreach ($array as &$value) {
            if (is_array($value)) {
                undo_magic_quotes_gpc($value);
            } else {
                $value = stripslashes($value);
            }
        }
    }
    undo_magic_quotes_gpc($_POST);
    undo_magic_quotes_gpc($_GET);
    undo_magic_quotes_gpc($_COOKIE);
}
# submit content back using UTF-8
header('Content-Type: text/html; charset=utf-8');
# Start Sessions
session_start();