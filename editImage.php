<?php
# editImage - edit image details
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
# database details
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
# variable to hold error message
$error = "";
# open file array and retrieve name
$fna = explode('.', $_FILES['userFile']['name']);
$ext = $fna[count($fna) - 1];
# get image details
$propertyId = $_POST["property_id"];
$image_id = $_POST["image_id"];
$imageName = test_input($_POST["imageName"]);
# check if file is empty
if ($_FILES["userFile"]["error"] == 4) {
    # check image name size
    if (strlen($_POST['imageName']) < 3) {
        $error = "Sorry Image Name is too short, min 3 characters";
    } else {
        # sql query to update image name
        $sql = "UPDATE images SET "
                . "imageName='$imageName'"
                . " WHERE imageId='$image_id'";
        # excute query
        $result = $conn->query($sql);
        # close connection
        $conn->close();
        # redirect user to viewProperties
        header("Location: viewProperties.php");
    }
}
# check if file is not empty
if ($_FILES["userFile"]["error"] != 4) {
    # check file type
    if (!preg_match('/png|jpeg/', $_FILES['userFile']['type'])) {
        $error = "Sorry, only png|jpeg images allowed";
        # check file type
    } else if (!preg_match('/png|jpg|jpeg/', $ext)) {
        $error = "Sorry, only png|jpeg images allowed";
        # check file size
    } else if ($_FILES['userFile']['size'] < 0) {
        $error = "Sorry file too large";
        # check image name size
    } else if (strlen($_POST['imageName']) < 3) {
        $error = "Sorry Image Name is too short, min 3 characters";
    } else {
        # get image details
        $imgData = addslashes(file_get_contents($_FILES['userFile']['tmp_name']));
        $imageProperties = getimageSize($_FILES['userFile']['tmp_name']);
        # setup connection
        $db = mysqli_connect("mysql.cms.gre.ac.uk", "me324", "12El05ma90", "me324"); //keep your db name
        # check connection
        if ($db->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        # sql to delete image
        $sql = "DELETE FROM images WHERE imageId=" . $image_id;
        # excute query
        $sth = $db->query($sql);
        # sql to re-add image
        $sql = "INSERT INTO images(imageId,imageType,imageData,imageName,propertyId)VALUES( '{$image_id}','{$imageProperties['mime']}', '{$imgData}','{$imageName}','{$propertyId}')";
        # check connection
        if ($db->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        # excute query
        $result = $db->query($sql);
        # close connection
        $db->close();
        # redirect user to viewProperties
        header("Location: viewProperties.php");
    }
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
        <h1><?php echo $error; ?></h1>
        <table class="center">
            <tr>
                <td>                  
                    <a href='editImagePage.php?image_id=<?php echo $_POST['image_id'] ?>&property_id=<?php echo $_POST['property_id'] ?>' class="button">Back</a>
                </td>
            </tr>
        </table>
    </body>
</html>