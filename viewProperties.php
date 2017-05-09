<?php
# viewProperties - This page alllows to view user property, add new, delete and upload images
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
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: viewProperties.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: viewProperties.php");
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
# sql query to retrieve user id
$query = "SELECT id FROM users WHERE username = :username";
# bind parameter to query
$query_params = array(
    ':username' => $_SESSION['username']
);
try {
    # Prepare query
    $stmt = $db->prepare($query);
    # excute query
    $result = $stmt->execute($query_params);
} catch (PDOException $ex) {
    # Prevent unexpected behaviour after redirection
    die("Failed to run query: " . $ex->getMessage());
}
# fetch results
$row3 = $stmt->fetch();
# sql query to retrieve all property own by the user
$sql1 = "SELECT * FROM property WHERE userId=" . $row3["id"] . " ORDER BY propertyId DESC";
# excute query
$result1 = $conn->query($sql1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

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
        <!--Cookie consent-->
        <?php cookieConset(); ?>
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
        <h1>Your Properties</h1>
        <table border='0' cellpadding='0' width='100%'>
            <thead> 
                <tr>
                    <!--headers-->
                    <td axis="title">Title</td>
                    <td axis="price">Weekly Price</td>
                    <td axis="type">Type</td>
                    <td axis="bedroom">Bedroom</td>
                    <td axis="location">Location</td>
                    <td axis="address">Address</td>
                    <td axis="description">Description</td> 
                    <td axis="image">Image</td>
                    <td axis="editControl">Edit Control</td>
                    <td axis="uploadControl">Upload Control</td>
                    <td axis="deleteControl">Delete Control</td>
                </tr> 
            </thead>
            <?php
            # output properties owned by the user
            while ($row1 = $result1->fetch_assoc()) {
                echo "<tr>";
                echo '<td>' . $row1['title'] . '</td>';
                echo '<td>£' . $row1['price'] . '</td>';
                echo '<td>' . $row1['typeProperty'] . '</td>';
                echo '<td>' . $row1['bedroom'] . '</td>';
                echo '<td>' . $row1['location'] . '</td>';
                echo '<td>' . $row1['address'] . '</td>';
                echo '<td>' . $row1['description'] . '</td>';
                echo "<td>";
                # sql to retrieve property images
                $sql2 = "SELECT * FROM images where propertyId=" . $row1["propertyId"];
                # excute query
                $result2 = $conn->query($sql2);
                echo "<table class='center'>";
                echo "<tr>";
                # for loop for the number of image found 
                while ($row2 = $result2->fetch_assoc()) {
                    echo '<td>';
                    # get image 
                    echo '<img src="getImage.php?id=' . $row2['imageId'] . '" alt="' . testAlt($row2['imageName']) . '" title="' . testAlt($row2['imageName']) . '" height="150" width="150"/>';
                    echo'</td>';
                    echo'<td>';
                    # link to delete image
                    echo'<p><a href="deleteImage.php?image_id=' . $row2['imageId'] . '">Delete</a></p>';
                    # link to edit image
                    echo'<a href="editImagePage.php?image_id=' . $row2['imageId'] . '&property_id=' . $row1['propertyId'] . '">Edit</a>';
                    echo'</td>';
                }
                echo "</tr>";
                echo "</table>";
                echo "</td>";
                # link to update property
                echo '<td><a href="updateProperty.php?property_id=' . $row1['propertyId'] . '">Edit Property</a></td>';
                # link to upload more images
                echo '<td><a href="uploadPicturePage.php?property_id=' . $row1['propertyId'] . '">Upload Images</a></td>';
                # link to delete property
                echo '<td><a href="deleteProperty.php?property_id=' . $row1['propertyId'] . '">Delete Property</a></td>';
                echo "</tr>";
            }
            echo "</table>";
            # close connection
            $conn->close();
            ?>
    </body>
</html>
