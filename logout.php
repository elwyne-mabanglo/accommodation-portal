<?php
# Logout - Logs the user out of the system and removes sessions and cookies
# 
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# Remove user's data from the session
unset($_SESSION['username']);
unset($_SESSION['active']);
# Check if remember cookie is set
if (isset($_COOKIE["remember"])) {
    # Check if remember cookie is equal to 0
    if ($_COOKIE["remember"] == 0) {
        # Remove cookies
        setcookie("user", "", time() - 3600, '/');
        setcookie("remember", "", time() - 3600, '/');
    }
}
# Redirect them to the home page
header("Location: index.php");
# Prevent unexpected behaviour after redirection
die("Redirecting to: index.php");
