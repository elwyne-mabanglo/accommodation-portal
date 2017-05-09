<?php
# editImagePage - Form allowing user to edit image.
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
# check if back button has been pressed
if (isset($_POST['back'])) {
    # redirect user to viewProperties
    header("Location: viewProperties.php");
} 
# database details
$imageNameErr = $fileErr = "&nbsp";
$servername = "mysql.cms.gre.ac.uk";
$username = "me324";
$password = "12El05ma90";
$dbname = "me324";
# setup connection
$conn = new mysqli($servername, $username, $password, $dbname);
# Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
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
        <h1>Edit Image</h1>
        <form action="editImage.php" method="post" enctype="multipart/form-data">
            <table cellpadding="0" cellspacing="0" class="center">
                <tr align='left'><!--Error message for image file -->
                    <td colspan="3"><span class="error"><?php echo $fileErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Small image to upload:</td> <!--Input for image file -->
                    <td><input type="file" size="15" name="userFile" /></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <tr align='left'><!--Error message for image name -->
                    <td colspan="3"><span class="error"><?php echo $imageNameErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Enter Name:</td> <!--Input for image name -->
                    <?php
                    # sql to retrieve image name
                    $sql2 = "SELECT imageName FROM images where imageId=" . $_GET["image_id"];
                    # excute query
                    $result2 = $conn->query($sql2);
                    # fetch results
                    $row2 = $result2->fetch_assoc();
                    ?>
                    <!--output image name from database-->
                    <td><input type="text" name="imageName" value="<?php echo $row2["imageName"]; ?>"/></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--store property id and image id--> 
                <input type="hidden" name="image_id" value="<?php echo $_GET["image_id"]; ?>"/>
                <input type="hidden" name="property_id" value="<?php echo $_GET["property_id"]; ?>"/>
                <tr>
                    <td colspan="3">
                        <p><i>(leave blank if you do not want to change your image name or image data)</i></p>
                        <input align='center' type="submit" name="upload" value="Update"/> 
                        <a class="button" class="button" href="viewProperties.php" class="removeLink">Back</a>
                    </td>
                </tr>
            </table>
        </form> 
    </body>
</html>