<?php
# exceedPictureLimit - Present error message to the user has exceed the picture limit
#
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# check if active session is not set, used to prevent unauthorised access
if (!isset($_SESSION['active'])) {
    # if not set redirect user
    header("Location: login.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: login.php");
    # if it is set check value 
} else if (isset($_SESSION['active'])) {
    if ($_SESSION['active'] == "no") {
        # if value is false redirect user
        header("Location: login.php");
        # Prevent unexpected behaviour after redirection
        die("Redirecting to: login.php");
    }
}
?>
<!--Version of HTML will be written in.-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--The xmlns attribute specifies the xml namespace for a document.-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <!--Page Title-->
        <title>BH Brighton and Hove</title>
        <!--Metal defines which character set is used, page description, keywords, author, and other metadata-->
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"></meta>
        <!--CSS describes how HTML elements should be displayed-->
        <link rel="stylesheet" type="text/css" href="mystyle.css"/>             
    </head>
    <!--Navigation bar-->
    <ul>
        <li><a href="private.php" class="w3-margin-left"><b>BH</b> Brighton and Hove</a></li>
        <li><a href="private.php">Home</a></li>
        <li><a href="edit_account.php">Account</a></li>
        <li><a class="active" href="viewProperties.php">Your Properties</a></li>
        <li><a href="addProperty.php">Add New Property</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>!</a></li>
    </ul>
    <h1>Exceed Picture Limit, Please Delete Or Replace</h1>
    <table align='center'>
        <tr>
            <th>
                <a href=viewProperties.php>Back</a>
            </th>
        </tr>
    </table>
</body>
</html>