<?php
# addProperty - Allows new entry of new properties
# 
# Reference Material:
# - SQL Injection Prevention: http://www.w3schools.com/sql/sql_injection.asp
# - Wildcards: http://www.w3schools.com/sql/sql_wildcards.asp
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
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: addProperty.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: addProperty.php");
}
# Define variables and set to empty values and false
$titleErr = $priceErr = $typeErr = $addressErr = $bedroomErr = $descriptionErr = $locationErr = "";
$title = $price = $type = $address = $bedroom = $location = $description = "";
$verifytitle = $verifyprice = $verifytype = $verifyaddress = $verifybedroom = $verifydescription = $verifylocation = false;
# check if the back button has been pressed
if (isset($_POST['back'])) {
    # redirect user to home page
    header("Location: viewProperties.php");
    # check if the submit button has been pressed
} else if (isset($_POST['submit'])) {
    # verify title input
    if (empty($_POST["title"])) {
        $titleErr = "Type is required";
        $verifytype = false;
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["title"])) {
        $titleErr = "Invalid Type";
        $verifytitle = false;
    } else {
        $verifytitle = true;
    }
    # verify type input
    if (empty($_POST["type"])) {
        $typeErr = "Type is required";
        $verifytype = false;
    } else {
        $verifytype = true;
    }
    # verify address input
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
        $verifytype = false;
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["address"])) {
        $addressErr = "Invalid Address";
        $verifyaddress = false;
    } else {
        $verifyaddress = true;
    }
    # verify price input
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
        $verifytype = false;
    } else if ($_POST["price"] <= 0) {
        $priceErr = "Greater than 0";
        $verifytype = false;
    } else {
        $verifyprice = true;
    }
    # verify bedroom input
    if (empty($_POST["bedroom"])) {
        $bedroomErr = "Bedroom is required";
        $verifytype = false;
    } else if ($_POST["bedroom"] <= 0) {
        $bedroomErr = "Greater than 0";
        $verifytype = false;
    } else {
        $verifybedroom = true;
    }
    # verify description input
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
        $verifytype = false;
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["description"])) {
        $descriptionErr = "Invalid description";
        $verifydescription = false;
    } else {
        $verifydescription = true;
    }
    # verify location input
    if (empty($_POST["location"])) {
        $locationErr = "Location is required";
        $verifytype = false;
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\_<>-])/", $_POST["location"])) {
        $locationErr = "Invalid Location";
        $verifylocation = false;
    } else {
        $verifylocation = true;
    }
    # check if all fields are valid
    if ($verifytitle && $verifyprice && $verifytype && $verifyaddress && $verifybedroom && $verifydescription && $verifylocation == true) {
        # sql query to get user id
        $query = "SELECT id FROM users WHERE username = :username";
        # bind parameters to query
        $query_params = array(
            ':username' => $_SESSION['username']
        );
        try {
            # prepare query
            $stmt = $db->prepare($query);
            # excute query
            $result = $stmt->execute($query_params);
        } catch (PDOException $ex) {
            # Prevent unexpected behaviour after redirection
            die("Failed to run query: " . $ex->getMessage());
        }
        # fetch detail
        $row3 = $stmt->fetch();
        # query to add new property
        $query = " 
            INSERT INTO property ( 
                title, 
                price, 
                typeProperty, 
                address,
                bedroom,
                userId,
                location,
                description
            ) VALUES ( 
                :title, 
                :price, 
                :type, 
                :address,
                :bedroom,
                :userId,
                :location,
                :description
            ) 
        ";
        # bind parameters to query
        $query_params = array(
            ':title' => test_input($_POST['title']),
            ':price' => $_POST['price'],
            ':type' => $_POST['type'],
            ':address' => test_input($_POST['address']),
            ':bedroom' => $_POST['bedroom'],
            ':userId' => $row3['id'],
            ':location' => test_input($_POST['location']),
            ':description' => test_input($_POST['description'])
        );
        try {
            # prepare query
            $stmt = $db->prepare($query);
            # excute query
            $result = $stmt->execute($query_params);
            # catch exception
        } catch (PDOException $ex) {
            # Prevent unexpected behaviour after redirection
            die("Failed to run query: " . $ex->getMessage());
        }
        # redirect user to viewProperties
        header("Location: viewProperties.php");
        # Prevent unexpected behaviour after redirection
        die("Redirecting to viewProperties.php");
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
    <body>
        <!--Cookie consent-->
        <?php cookieConset(); ?>
        <!--Navigation bar-->
        <ul>
            <li><a href="index.php" class="w3-margin-left"><b>BH</b> Brighton and Hove</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="edit_account.php">Account</a></li>
            <li><a href="viewProperties.php">Your Properties</a></li>
            <li><a class="active" href="addProperty.php">Add New Property</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>!</a></li>
        </ul>
        <h1>New Property</h1>
        <!--The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities-->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <table cellpadding="0" cellspacing="0" class="center">
                <!-- Title input -->
                <tr align='left'><!--Error message for Title -->
                    <td colspan="3"><span class="error"><?php echo $titleErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Title:</td> <!--Input for Title -->              
                    <td><textarea name="title" cols="60" rows="5"><?php echo $title; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
 
                <!-- Price input -->
                <tr align='left'><!--Error message for Price -->
                    <td colspan="3"><span class="error"><?php echo $priceErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Weekly Price:</td> <!--Input for Price -->
                    <td><input type="number" name="price" value ="<?php echo $price; ?>"></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--Setup combo values-->
                <?php
                $tempType = ['Bungalow', 'Cottage', 'End of terrace', 'Flat', 'Semi-detached', 'Terrace'];
                # selected value from database
                $settings = ['type' => $type]
                ?>
                <!-- Type input -->
                <tr align='left'><!--Error message for Type -->
                    <td colspan="3"><span class="error"><?php echo $typeErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Type:</td> 
                    <td>
                        <select name="type" type="text"><!--Input Choice for Type -->
                            <?php foreach ($tempType as $type): ?>
                                <option <?php echo ($settings['type'] == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Location input -->
                <tr align='left'><!--Error message for Address -->
                    <td colspan="3"><span class="error"><?php echo $locationErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Location:</td> <!--Input for Address -->

                    <td><textarea name="location" cols="60" rows="1"><?php echo $location; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Address input -->
                <tr align='left'><!--Error message for Address -->
                    <td colspan="3"><span class="error"><?php echo $addressErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Address:</td> <!--Input for Address -->
                    <td><textarea name="address" cols="60" rows="5"><?php echo $address; ?></textarea></input></td>
                    <td><span class="error">*</span></td>
                </tr>
                <!-- Description input -->
                <tr align='left'><!--Error message for Description -->
                    <td colspan="3"><span class="error"><?php echo $descriptionErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Description:</td> <!--Input for Description -->
                    <td>
                        <textarea name="description" cols="60" rows="10"><?php echo $description; ?></textarea>
                    </td>
                    <td><span class="error">*</span></td>
                </tr>
                <!--Setup combo values-->
                <?php
                $tempBedroomNo = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                # selected value from database
                $settings = ['bedroom' => $bedroom]
                ?>
                <!-- Bedroom input -->
                <tr align='left'><!--Error message for Bedroom -->
                    <td colspan="3"><span class="error"><?php echo $bedroomErr; ?></span></td>
                </tr>                             
                <tr>
                    <td>Bedroom:</td> 
                    <td>
                        <select name="bedroom" type="text">
                            <?php foreach ($tempBedroomNo as $temp): ?>
                                <option <?php echo ($settings['bedroom'] == $temp) ? 'selected' : ''; ?>><?php echo $temp; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><span class="error">*</span></td>
                </tr> 
                <tr>
                    <td colspan="3">
                        <input type="submit" name="submit" alt="Submit" value="Submit"/>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
