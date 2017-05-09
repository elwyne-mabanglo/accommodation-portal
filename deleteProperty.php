<?php
# deleteProperty - Use to delete selected property
#
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# check if active session is not set, used to prevent unauthorised access
if (!isset($_SESSION['active'])) {
    # if not set redirect user
    header("Location: login.php");
    die("Redirecting to: login.php");
    # if it is set check value 
} else if (isset($_SESSION['active'])) {
    if ($_SESSION['active'] == "no") {
        # if value is false redirect user
        header("Location: login.php");
        die("Redirecting to: login.php");
    }
}
# get property Id from URL
$property_id = $_GET['property_id'];
# setup database connection
$db = mysqli_connect("mysql.cms.gre.ac.uk", "me324", "12El05ma90", "me324"); 
# sql query to delete property
$sql = "DELETE FROM property WHERE propertyId=" . $property_id;
# excute query
$sth = $db->query($sql);
# redirect to viewProperties
header("Location: viewProperties.php");
# close connection
$conn->close();
