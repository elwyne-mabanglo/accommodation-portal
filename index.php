<?php
# Index - Default home page, appearance changes depending on user status.
#       - Allows users to search and view of accommodations. 
# 
# Reference Material:
# - Pagination: Reference material https://stackoverflow.com/questions/3705318/simple-php-pagination-script
# - SQL Injection Prevention: http://www.w3schools.com/sql/sql_injection.asp
# - Wildcards: http://www.w3schools.com/sql/sql_wildcards.asp
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# 
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# SQL Database details
$servername = "mysql.cms.gre.ac.uk";
$username = "me324";
$password = "12El05ma90";
$dbname = "me324";
# Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
# Check connection
if ($conn->connect_error) {
    # Prevent unexpected behaviour after redirection
    die("Connection failed: " . $conn->connect_error);
}
# Default SQL query to list proerpties
$sql1 = "SELECT * FROM property";
# Default SQL query to count all proerpties
$countSQL = "SELECT COUNT(*) FROM property";
# Check if wildCard search cookie is not set
if (!isset($_COOKIE["wildCard"])) {
    # if true set $wildCard
    $wildCard = "Search...";
} else {
    # if not true set $wildCard to cookie value
    $wildCard = $_COOKIE["wildCard"];
}
# Check if wildCardLocation search cookie is not set
if (!isset($_COOKIE["wildCardLocation"])) {
    # if not true set $wildCardLocation to cookie value
    $wildCardLocation = "";
} else {
    # if not true set $wildCardLocation to cookie value
    $wildCardLocation = $_COOKIE["wildCardLocation"];
}
# Check if bedroom search cookie is not set
if (!isset($_COOKIE["bedroom"])) {
    # if not true set $wildCardbedroom to cookie value
    $wildCardbedroom = "";
} else {
    # if not true set $wildCardbedroom to cookie value
    $wildCardbedroom = $_COOKIE["bedroom"];
}
# Check if type search cookie is not set
if (!isset($_COOKIE["type"])) {
    # if not true set $wildCardtype to cookie value
    $wildCardtype = "";
} else {
    # if not true set $wildCardtype to cookie value
    $wildCardtype = $_COOKIE["type"];
}
# Check if any of the search cookies are set
if (isset($_COOKIE['wildCardLocation']) || isset($_COOKIE['bedroom']) || isset($_COOKIE['type']) || isset($_COOKIE['wildCard'])) {
    # If true set remember search tick box to true
    $remember = 'checked="checked"';
} else {
    # If not true do not set
    $remember = "";
}
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: index.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: index.php");
}
# Reset form
if (isset($_POST['reset'])) {
    # Redirect to home page
    header("Location: index.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: index.php");
}
# Check search button has been pressed
if (isset($_POST['search'])) {
    # Get cleansed user input
    $wildCardbedroom = test_input($_POST["wildCardBedroom"]);
    $wildCardtype = test_input($_POST["wildCardType"]);
    $wildCard = test_input($_POST["wildCard"]);
    $wildCardLocation = test_input($_POST["wildCardLocation"]);
    # Check if empty
    if ($wildCardbedroom || $wildCardtype || $wildCard || $wildCardLocation != "") {
        # concatenate sql query
        $sql1 = $sql1 . " WHERE";
        $countSQL = $countSQL . " WHERE";
    }
    # Check if empty
    if ($wildCardbedroom != "") {
        # concatenate sql bedroom query
        $sql1 = $sql1 . " bedroom=" . $wildCardbedroom;
        $countSQL = $countSQL . " bedroom=" . $wildCardbedroom;
        # Check if cookie is set
        if (isset($_COOKIE["cookiebar"])) {
            # Check if remember search is set
            if (isset($_POST["remember"])) {
                # saved bedroom search
                setcookie("bedroom", $wildCardbedroom, time() + (86400 * 30), '/');
                # set remember search tickbox to true
                $remember = 'checked="checked"';
                # Check if remember search tickbox is unticked
            } else if (!isset($_POST["remember"])) {
                # if true remove cookie
                setcookie("bedroom", "", time() - 3600, '/');
                # set remember search tickbox to false
                $remember = "";
            }
        }
    }
    # Check if empty
    if ($wildCardtype != "") {
        # Store user input into an array
        $wildCardtypeArray = explode(" ", $wildCardtype);
        # Check if not empty
        if ($wildCardbedroom != "") {
            # if not empty concatenate sql query
            $sql1 = $sql1 . " AND ";
            $countSQL = $countSQL . " AND ";
        }
        # Check array size
        if (count($wildCardtypeArray) > 1) {
            $i = 0;
            # if true for loop through the array
            foreach ($wildCardtypeArray as $value) {
                $i++;
                # set first loop query
                if ($i == 1) {
                    # concatenate first loop sql query
                    $sql1 = $sql1 . " typeProperty LIKE '%" . $value . "%'";
                    $countSQL = $countSQL . " typeProperty LIKE '%" . $value . "%'";
                } else {
                    # Concatenate after first loop sql query
                    $sql1 = $sql1 . " AND typeProperty LIKE '%" . $value . "%'";
                    $countSQL = $countSQL . " AND typeProperty LIKE '%" . $value . "%'";
                }
            }
        } else {
            # concatenate sql query if array less than 1
            $sql1 = $sql1 . " typeProperty LIKE '%" . $wildCardtype . "%'";
            $countSQL = $countSQL . " typeProperty LIKE '%" . $wildCardtype . "%'";
        }
        # Check if cookie is set
        if (isset($_COOKIE["cookiebar"])) {
            # Check if remember search is set
            if (isset($_POST["remember"])) {
                # saved type search
                setcookie("type", $wildCardtype, time() + (86400 * 30), '/');
                # set remember search tickbox to true
                $remember = 'checked="checked"';
                # Check if remember search tickbox is unticked
            } else if (!isset($_POST["remember"])) {
                # if true remove cookie
                setcookie("type", "", time() - 3600, '/');
                # set remember search tickbox to false
                $remember = "";
            }
        }
    }
    # Check if empty
    if ($wildCard != "") {
        # Store user input into an array
        $wildCardArray = explode(" ", $wildCard);
        # Check if not empty
        if ($wildCardbedroom || $wildCardtype != "") {
            # if not empty concatenate sql query
            $sql1 = $sql1 . " AND ";
            $countSQL = $countSQL . " AND ";
        }
        # Check array size
        if (count($wildCardArray) > 1) {
            $i = 0;
            # if true for loop through the array
            foreach ($wildCardArray as $value) {
                $i++;
                # set first loop query
                if ($i == 1) {
                    # concatenate first loop sql query
                    $sql1 = $sql1 . " CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $value . "%'";
                    $countSQL = $countSQL . " CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $value . "%'";
                } else {
                    # Concatenate after first loop sql query
                    $sql1 = $sql1 . " AND CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $value . "%'";
                    $countSQL = $countSQL . " AND CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $value . "%'";
                }
            }
        } else {
            # concatenate sql query if array less than 1
            $sql1 = $sql1 . " CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $wildCard . "%'";
            $countSQL = $countSQL . " CONCAT_WS('', title, location, typeProperty, price, bedroom) LIKE '%" . $wildCard . "%'";
        }
        # Check if cookie is set
        if (isset($_COOKIE["cookiebar"])) {
            # Check if remember search is set
            if (isset($_POST["remember"])) {
                # saved wildCard search           
                setcookie("wildCard", $wildCard, time() + (86400 * 30), '/');
                # set remember search tickbox to true
                $remember = 'checked="checked"';
                # Check if remember search tickbox is unticked
            } else if (!isset($_POST["remember"])) {
                # if true remove cookie
                setcookie("wildCard", "", time() - 3600, '/');
                # set remember search tickbox to false
                $remember = "";
            }
        }
    }
    # Check if not empty
    if ($wildCardLocation != "") {
        # Check if not empty
        if ($wildCardbedroom || $wildCardtype || $wildCard != "") {
            # if not true concatenate sql query
            $sql1 = $sql1 . " AND ";
            $countSQL = $countSQL . " AND ";
        }
        # concatenate $wildCardLocation to sql query
        $sql1 = $sql1 . " location LIKE '%" . $wildCardLocation . "%'";
        $countSQL = $countSQL . " location LIKE '%" . $wildCardLocation . "%'";
        # Check if cookie is set
        if (isset($_COOKIE["cookiebar"])) {
            # Check if remember search is set
            if (isset($_POST["remember"])) {
                # set remember wildCardLocation tickbox to true
                setcookie("wildCardLocation", $wildCardLocation, time() + (86400 * 30), '/');
                $remember = 'checked="checked"';
                # Check if remember search tickbox is unticked
            } else if (!isset($_POST["remember"])) {
                # if true remove cookie
                setcookie("wildCardLocation", "", time() - 3600, '/');
                # set remember search tickbox to false
                $remember = "";
            }
        }
    }
}
#
# Pagination Section
#
# Excute SQL query
$result1 = $conn->query($sql1);
# Fetch number of items
$total = $db->query($countSQL)->fetchColumn();
# Number of items per page
$limit = 5;
# Number of pages
$pages = ceil($total / $limit);
# What page are we currently on?
$page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
    'options' => array(
        'default' => 1,
        'min_range' => 1,
    ),
        )));
# Calculate the offset for the query 
$offset = ($page - 1) * $limit;
# If offset is negative set it positive 
if ($offset < 0) {
    $offset = 10;
}
# Start & End value 
$start = $offset + 1;
$end = min(($offset + $limit), $total);
# The "back" link 
$prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
# The "forward" link
$nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
# Order items by title and set limits
$sql1 = $sql1 . ' ORDER BY title LIMIT ' . $limit . ' OFFSET ' . $offset;
# Prepare SQL query
$stmt = $db->prepare($sql1);
# Excute statement
$stmt->execute();
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
        <?php
        # Cookie consent
        cookieConset();
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
        <!--Search Form-->
        <!--The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">       
            <table cellpadding = '0' class="center" >
                <tbody>
                    <tr>                  
                        <!--search input for wildCard-->
                        <td axis="search" colspan="2">
                            <input style="width: 100%;" type="text" name="wildCard" value="<?php echo $wildCard; ?>"/>
                        </td>
                        <!--remember me function-->
                        <td axis="remember">
                            Remember search: <input type="checkbox" name="remember" <?php echo $remember; ?>/>
                            <input name="search" type="submit" value="Search"/>
                            <input name="reset" type="submit" value="Reset Search"/>
                        </td>
                    </tr>
                    <tr>
                        <td axis="filters">
                            <!--search input for $wildCardbedroom-->
                            Bedroom: <input type="text" name="wildCardBedroom" value="<?php echo $wildCardbedroom; ?>"/>
                        </td>
                        <td>
                            <!--search input for $wildCardtype-->
                            Type: <input type="text" name="wildCardType" value="<?php echo $wildCardtype; ?>"/>
                        </td>
                        <td>
                            <!--search input for $wildCardLocation-->
                            Location: <input type="text" name="wildCardLocation" value="<?php echo $wildCardLocation; ?>"/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <?php
        # Print pagination pages
        echo '<div id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div>';
        # Check content of SQL 
        if ($stmt->rowCount() > 0) {
            # Define how we want to fetch the results
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $iterator = new IteratorIterator($stmt);
            # Display results
            ?>
            <table border = '0' cellpadding = '1' width = '100%'>
                <thead>
                    <tr>
                        <!--print headers-->
                        <td axis="price">Weekly Price</td>
                        <td axis="type">Type</td>
                        <td axis="title">Title</td>
                        <td axis="bedroom">Bedroom</td>
                        <td axis="location">Location</td>
                        <td axis="image">Image</td>
                        <td axis="content">Additional Content</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    # Loop through SQL results
                    foreach ($iterator as $row) {
                        ?>
                        <tr>
                            <?php
                            # Print results 
                            echo '<td axis="price">£' . $row['price'] . '</td>';
                            echo '<td axis="type">' . $row['typeProperty'] . '</td>';
                            echo '<td axis="title">' . $row['title'] . '</td>';
                            echo '<td axis="bedroom">' . $row['bedroom'] . '</td>';
                            echo '<td axis="bedroom">' . $row['location'] . '</td>';
                            echo '<td axis="image">';
                            # SQL query to get images details
                            $sql2 = "SELECT imageId, imageData, imageName FROM images where propertyId=" . $row["propertyId"];
                            # Excute query
                            $result2 = $conn->query($sql2);
                            # Fetch results
                            $row2 = $result2->fetch_assoc();
                            # Get image | Set image name | Set title | textAlt test if Image Name is empty, set default to "No Image Uploaded
                            echo '<img src="getImage.php?id=' . $row2['imageId'] . '" alt="' . testAlt($row2['imageName']) . '" title="' . testAlt($row2['imageName']) . '" height="150" width="150"/>';
                            echo'</td>';
                            # Show more content
                            echo '<td><a href="showMore.php?property_id=' . $row['propertyId'] . '">Show More</a></td>';
                            ?> 
                        </tr>
                        <?php
                    }
                } else {
                    # No results from search
                    echo '<p>No results could be displayed.</p>';
                }
                ?>
            </tbody>
        </table>
        <?php
        # Close connection
        $conn->close();
        ?>
        <p>
            <a href="http://validator.w3.org/check?uri=referer">
                <img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" class="validate"/>
            </a>        
            <a href="http://jigsaw.w3.org/css-validator/check/referer">
                <img src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valid CSS!" class="validate"/>
            </a>
        </p>
    </body>
</html>