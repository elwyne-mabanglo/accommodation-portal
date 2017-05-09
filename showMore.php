<?php
# showMore - Show additional property content details 
# 
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
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
?>
<!--Version of HTML will be written in.-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--The xmlns attribute specifies the xml namespace for a document.-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <!--Page Title-->
        <title>BH Brighton and Hove</title>
        <!--Meta defines which character set is used, page description, keywords, author, and other metadata-->
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"></meta>
        <!--CSS describes how HTML elements should be displayed-->
        <link rel="stylesheet" type="text/css" href="mystyle.css"/>             
    </head>
    <body>
        <?php
        # check if username session is set  
        if (isset($_SESSION['username'])) {
            # check if active session is set
            if (isset($_SESSION['active'])) {
                # Check if active session is true
                if ($_SESSION['active'] == "yes") {
                    ?>
                    <!--Navigation for full membership-->
                    <ul>
                        <li><a href="index.php"><b>BH</b> Brighton and Hove</a></li>
                        <li><a class="active" href="index.php">Home</a></li>
                        <li><a href="edit_account.php">Account</a></li>
                        <li><a href="viewProperties.php">Your Properties</a></li>
                        <li><a href="addProperty.php">Add New Property</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>!</a></li>
                    </ul>

                    <?php
                    # Check if active session is false
                } else if ($_SESSION['active'] == "no") {
                    ?>
                    <!--Navigation for limited access members-->
                    <ul>
                        <li><a href="index.php"><b>BH</b> Brighton and Hove</a></li>
                        <li><a class="active" href="index.php">Home</a></li>
                        <li><a href="activatePage.php">Verify Account</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>! (Account not verified)</a></li>
                    </ul>
                    <?php
                }
            }
            # All conditions false, show standard visitors view
        } else {
            ?>
            <ul>
                <li><a href="index.php"><b>BH</b> Brighton and Hove</a></li>
                <li><a class="active" href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
            <?php
        }
        ?>
        <h1>Brighton and Hove's Homes</h1>
        <!--return to home page-->
        <a class="button" href="index.php">Back</a>
        <?php
        # query to get property details
        $sql1 = "SELECT * from property where propertyId=" . $_GET["property_id"];
        # excute query
        $result1 = $conn->query($sql1);
        # fetch results
        $row1 = $result1->fetch_assoc();
        ?>
        <table border="0" align="center" width="1450">
            <tr>
                <td>
                    <!--output results-->
                    <h2><?php echo $row1['title'] ?></h2>
                    <p>Type: <?php echo $row1['typeProperty'] ?></p>
                    <p>Location: <?php echo $row1['location'] ?></p>
                    <p>Address: <?php echo $row1['address'] ?></p>
                    <p><b>Full Description</b></p>
                    <p><?php echo $row1['description'] ?></p>
                    <h2>£<?php echo $row1['price'] ?></h2>
                    <h3>Contact Number: 123123123123123</h3>
                </td>
            </tr>
            <tr align="center">
                <td>
                    <?php
                    # sql to get image from property
                    $sql2 = "SELECT imageId, imageData, imageName FROM images where propertyId=" . $_GET["property_id"];
                    # excute query
                    $result2 = $conn->query($sql2);
                    # get results
                    while ($row2 = $result2->fetch_assoc()) {
                        echo '<img src="getImage.php?id=' . $row2['imageId'] . '" alt="' . testAlt($row2['imageName']) . '" title="' . testAlt($row2['imageName']) . ' "height="350" width="350" "/>  ' . "\n";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php
        # close connection
        $conn->close();
        ?>
    </body>
</html>