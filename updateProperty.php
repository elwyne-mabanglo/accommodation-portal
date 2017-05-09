<?php
# updateProperty - Property details can be edited and updated
# 
# Reference Material:
# - Populating combo box using SQL Database: http://stackoverflow.com/questions/25963301/display-database-table-value-in-html-dropdown-select-list
# - preg_match: http://regexr.com/
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
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
# Check if back button has been pressed
if (isset($_POST['back'])) {
    # Redirect user to viewProperties
    header("Location: viewProperties.php");
}
# database details
$servername = "mysql.cms.gre.ac.uk";
$username = "me324";
$password = "12El05ma90";
$dbname = "me324";
# Define variables and set to empty values and false
$titleErr = $priceErr = $typeErr = $addressErr = $bedroomErr = $descriptionErr = $locationErr = "";
$verifytitle = $verifyprice = $verifytype = $verifyaddress = $verifybedroom = $verifydescription = $verifylocation = false;
# check if submit button has been pressed
if (!empty($_POST)) {
    # Verify Title
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
        $verifytitle = false;
    } if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["title"])) {
        $titleErr = "Invalid title";
        $verifytitle = false;
    } else {
        $verifytitle = true;
    }
    # Verify Description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
        $verifydescription = false;
    } if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["title"])) {
        $descriptionErr = "Invalid description";
        $verifydescription = false;
    } else {
        $verifydescription = true;
    }
    # Verify Type
    if (empty($_POST["type"])) {
        $typeErr = "Type is required";
    } else {
        $verifytype = true;
    }
    # Verify Address
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
        $verifyaddress = false;
    } if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["address"])) {
        $addressErr = "Invalid address";
        $verifyaddress = false;
    } else {
        $verifyaddress = true;
    }
    # Verify location
    if (empty($_POST["location"])) {
        $locationErr = "Location is required";
        $verifylocation = false;
    } if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["location"])) {
        $locationErr = "Invalid Location";
        $verifylocation = false;
    } else {
        $verifylocation = true;
    }
    # Verify price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
        $verifyprice = false;
    } else if ($_POST["price"] <= 0) {
        $verifyprice = false;
        $priceErr = "Greater than 0";
    } else {
        $verifyprice = true;
    }
    # Verify bedroom
    if (empty($_POST["bedroom"])) {
        $bedroomErr = "Bedroom is required";
        $verifybedroom = false;
    } else {
        $verifybedroom = true;
    }
    # check all fields are verified
    if ($verifytitle && $verifyprice && $verifytype && $verifyaddress && $verifybedroom && $verifydescription && $verifylocation == true) {
        # retrieve cleansed user input
        $title = test_input($_POST['title']);
        $price = test_input($_POST['price']);
        $type = test_input($_POST['type']);
        $address = test_input($_POST['address']);
        $bedroom = test_input($_POST['bedroom']);
        $propertyId = test_input($_GET['property_id']);
        $description = test_input($_POST['description']);
        $location = test_input($_POST['location']);
        # sql to update property details
        $sql = "UPDATE property SET "
                . "title='$title',"
                . "price='$price',"
                . "typeProperty='$type',"
                . "address='$address',"
                . "bedroom='$bedroom',"
                . "description='$description',"
                . "location='$location'"
                . "WHERE propertyId='$propertyId'";
        # setup database connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        # Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        # excute query
        $result = $conn->query($sql);
        # close connections
        $conn->close();
        # redirect user to viewProperties
        header("Location: viewProperties.php");
    }
}
# setup database connection
$conn = new mysqli($servername, $username, $password, $dbname);
# sql to populate property details
$sql = "SELECT * FROM property WHERE propertyId=" . $_GET['property_id'];
# excute query
$result = $conn->query($sql);
# fetch results
$row = $result->fetch_assoc();
# show results
$title = $row["title"];
$price = $row["price"];
$type = $row["typeProperty"];
$address = $row["address"];
$bedroom = $row["bedroom"];
$description = $row["description"];
$location = $row["location"];
# close connection
$conn->close();
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
        <h1>Edit Property: <?php echo $title; ?></h1>
        <form method="post" action="updateProperty.php?property_id=<?php echo $_GET['property_id']; ?>">
            <table cellpadding="0" cellspacing="0" class="center">
                <!-- Title Input -->
                <tr align='left'><!--Error message for Title -->
                    <td colspan="3"><span class="error"><?php echo $titleErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Title:</td> <!--Input for Title -->              
                    <td><textarea required name="title" cols="60" rows="5"><?php echo $title; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Price Input -->
                <tr align='left'><!--Error message for Price -->
                    <td colspan="3"><span class="error"><?php echo $priceErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Weekly Rate:</td> <!--Input for Price -->
                    <td><input required type="number" name="price" value ="<?php echo $price; ?>"></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--Setup combo values-->
                <?php
                $tempType = ['Bungalow', 'Cottage', 'End of terrace', 'Flat', 'Semi-detached', 'Terrace'];
                # selected value from database
                $settings = ['type' => $type]
                ?>
                <!-- Type Input -->
                <tr align='left'><!--Error message for Type -->
                    <td colspan="3"><span class="error"><?php echo $typeErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Type:</td> 
                    <td>
                        <select name="type" type="text"><!--Input Choice for Type -->
                            <!--For loop through array and populate combo box--> 
                            <?php foreach ($tempType as $type): ?>                         
                                <option <?php echo ($settings['type'] == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Location Input -->
                <tr align='left'><!--Error message for Address -->
                    <td colspan="3"><span class="error"><?php echo $locationErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Location:</td> <!--Input for Address -->

                    <td><textarea required name="location" cols="60" rows="1"><?php echo $location; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Address Input -->
                <tr align='left'><!--Error message for Address -->
                    <td colspan="3"><span class="error"><?php echo $addressErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Address:</td> <!--Input for Address -->
                    <td><textarea required name="address" cols="60" rows="5"><?php echo $address; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Description Input -->
                <tr align='left'><!--Error message for Description -->
                    <td colspan="3"><span class="error"><?php echo $descriptionErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Description:</td> <!--Input for Description -->
                    <td>
                        <textarea required name="description" cols="60" rows="10"><?php echo $description; ?></textarea>
                    </td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--Setup combo values-->
                <?php
                $tempBedroomNo = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $settings = ['bedroom' => $bedroom]
                ?>
                <!-- Bedroom Input -->
                <tr align='left'><!--Error message for Bedroom -->
                    <td colspan="3"><span class="error"><?php echo $bedroomErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Bedroom:</td> 
                    <td>
                        <select name="bedroom" type="text">
                            <!--For loop through array and populate combo box--> 
                            <?php foreach ($tempBedroomNo as $temp): ?>
                                <option <?php echo ($settings['bedroom'] == $temp) ? 'selected' : ''; ?>><?php echo $temp; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><span class="error">*</span></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input align='center' type="submit" name="upload" value="Update"/> 
                        <input type="submit" name="back" alt="Back" value="Back" />
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>