<?php
# uploadImagePage - Used to select and name the image to be uploaded
# 
# Reference Material:
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# 
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# check if active session is not set, used to prevent unauthorised access
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
# SQL Details
$servername = "mysql.cms.gre.ac.uk";
$username = "me324";
$password = "12El05ma90";
$dbname = "me324";
# Setup connection
$conn = new mysqli($servername, $username, $password, $dbname);
# Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
# SQL Query to check how images have been uploaded
$countSQL = "SELECT COUNT(*) FROM images WHERE propertyId=" . $_GET["property_id"];
# Excute query
$total = $db->query($countSQL)->fetchColumn();
# Check image total
if ($total >= 3) {
    # if more than 3 redirect to exceedPictureLimit
    header("Location: exceedPictureLimit.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: ExceedPictureLimit.php");
}
# Check if the back button has been pressed
if (isset($_POST['back'])) {
    # Redirect to viewProperties
    header("Location: viewProperties.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: viewProperties.php");
}
# Define variables and set to empty
$imageNameErr = $fileErr = "";
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
    <body>
        <!--Navigation bar-->
        <ul>
            <li><a href="index.php" class="w3-margin-left"><b>BH</b> Brighton and Hove</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="edit_account.php">Account</a></li>
            <li><a class="active" href="viewProperties.php">Your Properties</a></li>
            <li><a href="addProperty.php">Add New Property</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>!</a></li>
        </ul>
        <h1>Upload New Image</h1>
        <form action="uploadImage.php" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" class="center">
                <!--Image file selector-->
                <tr>
                    <td colspan="3"><span class="error"><?php echo $fileErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Small image to upload:</td> <!--Input for username -->
                    <td><input type="file" size="15" name="userFile" /></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--image name input-->
                <tr>
                    <td colspan="3"><span class="error"><?php echo $imageNameErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Enter Name:</td>
                    <td><input type="text" name="imageName" /></input></td>
                    <td><span class="error">*</span></td>
                    <input type="hidden" name="property_id" value="<?php echo $_GET["property_id"]; ?>"/>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" name="upload" alt="Upload" value="Upload"/>
                        <a class="button" href="viewProperties.php" class="removeLink">Back</a>
                    </td>
                </tr>
            </table>
        </form> 
    </body>
</html>




