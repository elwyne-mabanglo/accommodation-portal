<?php
# activatPage - Allows user to activate account
# 
# Reference Material:
# - SQL Injection Prvention: http://www.w3schools.com/sql/sql_injection.asp
# - password_hash: http://php.net/manual/en/function.password-hash.php
#
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# Check 
if (empty($_SESSION['username'])) {
    header("Location: index.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to index.php");
}
# retrieve username from session
$username = $_SESSION['username'];
# variable to hold error message
$error = "";
# check if submit button has been pressed
if (!empty($_POST)) {
    # sql to get user details
    $query = "SELECT * FROM users WHERE username = :username";
    # retrieve user input code
    $code = $_POST['code'];
    # bind parameters to query
    $query_params = array(
        ':username' => $username
    );
    try {
        # prepare query
        $stmt = $db->prepare($query);
        # excute query
        $result = $stmt->execute($query_params);
        # catch exceptions
    } catch (PDOException $ex) {
        # Prevent unexpected behaviour after redirection
        die("Failed to run query: " . $ex->getMessage());
    }
    # fetch results
    $row = $stmt->fetch();
    # get user id
    $userId = $row["id"];
    # check compare database code and user input
    if (password_verify(test_input($code), $row['activeCode']) == true) {
        # if true activate user account
        activateAccount("UPDATE users SET activeUser='yes' WHERE id=" . $userId);
        # store active session value as yes
        $_SESSION['active'] = "yes";
        # redirect user to home page
        header("Location: index.php");
        # Prevent unexpected behaviour after redirection
        die("Redirecting to: index.php");
    } else {
        # if not true errors message
        $error = "Please check you have entered the correct pass code";
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
        <!--Navigation bar-->
        <ul>
            <li><a href="index.php"><b>BH</b> Brighton and Hove</a></li>
            <li><a class="active" href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>! (Account not verified)</a></li>
        </ul>
        <h1>Activation Page</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <p>Code: 
                <input required type="password" name="code" value =""/>
                <input type="submit" name="submit" alt="Submit" value="Submit"/></p>
            <span class="error"><?php echo $error; ?></span>
        </form>
    </body>
</html>
`