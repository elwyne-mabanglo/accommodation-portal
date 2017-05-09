<?php
# UploadImage - Uploads image to database
# 
# Reference Material:
# - Upload Image: http://stuweb.cms.gre.ac.uk/~ha07/web/PHP/imageUpload.html
# - preg_match: http://regexr.com/
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
# variable to hold error
$error = "";
# open file array and retrieve name
$fna = explode('.', $_FILES['userFile']['name']);
$ext = $fna[count($fna) - 1];
# get property id
$propertyId = $_POST["property_id"];
# check file type
if (!preg_match('/png|jpeg/', $_FILES['userFile']['type'])) {
    $error = "Sorry, only png|jpeg images allowed";
    # check file type
} else if (!preg_match('/png|jpg|jpeg/', $ext)) {
    $error = "Sorry, only png|jpeg images allowed";
    # check that we have an image file smaller than 99999k bytes
} else if ($_FILES['userFile']['size'] > 99999) {
    $error = "Sorry file too large";
    # check name
} else if (strlen($_POST['imageName']) < 3) {
    $error = "Sorry Image Name is too short, min 3 characters";
    # check name is not invalid
} else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\._<>-])/", $_POST["imageName"])) {
    $error = "Invalid Image Name";
} else {
    # retrieve image details
    $imgData = addslashes(file_get_contents($_FILES['userFile']['tmp_name']));
    $imageProperties = getimageSize($_FILES['userFile']['tmp_name']);
    $imageName = test_input($_POST["imageName"]);
    # setup database connection
    $db = mysqli_connect("mysql.cms.gre.ac.uk", "me324", "12El05ma90", "me324"); //keep your db name
    # sql to insert new image
    $sql = "INSERT INTO images(imageType,imageData,imageName,propertyId)VALUES('{$imageProperties['mime']}', '{$imgData}','{$imageName}','{$propertyId}')";
    # check connection
    if ($db->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    # excute query
    $result = $db->query($sql);
    # close connection
    $db->close();
    # redirect user back to viewProperties
    header("Location: viewProperties.php");
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
        <h1><?php echo $error; ?></h1>
        <table class="center">
            <tr>
                <td>                  
                    <a class="button" href="uploadPicturePage.php?property_id=<?php echo $_POST['property_id'] ?>" class="removeLink">Back</a>
                </td>
            </tr>
        </table>
    </body>
</html>