<?php
# edit_account - Users are able to update email and password
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
;
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
    header("Location: index.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: index.php");
}
# check if back button has been pressed
if (isset($_POST['back'])) {
    # redirect user to home page
    header("Location: index.php");
}
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: edit_account.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: edit_account.php");
}
# sql query to retrieve user details
$query = "SELECT * FROM users WHERE username = :username";

# bind parameters to query
$query_params = array(
    ':username' => $_SESSION['username']
);
try {
    # [prepare query
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
# Define variables and set to empty values and false
$email = $passwordErr = $emailErr = "";
$verifyemail = false;
# check if the back button has been prssed
if (isset($_POST['back'])) {
    # redirect user to home page
    header("Location: index.php");
    # check if the update button has been pressed
} else if (isset($_POST['update'])) {
    # check if the email entry is empty
    if (empty($_POST["email"])) {
        # output error if it is empty
        $emailErr = "Email is required";
        $verifyemail = false;
    } else {
        # test user input
        $email = test_input($_POST["email"]);
        # validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            # if format not valid error message
            $emailErr = "Invalid email format";
            $verifyemail = false;
        } else {
            $verifyemail = true;
        }
    }
    # check if password entry is not empty
    if (!empty($_POST['password'])) {
        # if not empty hash password
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 5]);
    } else {
        # if empty dont do anything
        $password = null;
    }
    # bind parameters to query
    $query_params = array(
        ':email' => $_POST['email'],
        ':username' => $_SESSION['username'],
    );
    # if password is not null, bind query
    if ($password !== null) {
        $query_params[':password'] = $password;
    }
    # query to update user details
    $query = " 
            UPDATE users 
            SET 
                email = :email 
        ";
    # if password is not blank concatenate sql
    if ($password !== null) {
        $query .= " 
                , password = :password 
            ";
    }
    # check if email is valid
    if ($verifyemail == true) {
        # concatenate sql query
        $query .= " 
            WHERE 
                username = :username 
        ";
        try {
            # prepare statement
            $stmt = $db->prepare($query);
            # excute query
            $result = $stmt->execute($query_params);
            # catch exceptions
        } catch (PDOException $ex) {
            # Prevent unexpected behaviour after redirection
            die("Failed to run query: " . $ex->getMessage());
        }
        # redirect user to home page
        header("Location: index.php");
        # Prevent unexpected behaviour after redirection
        die("Redirecting to index.php");
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
        <!-- Metal defines which character set is used, page description, keywords, author, and other metadata -->
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"></meta>
        <!--CSS describes how HTML elements should be displayed-->
        <link rel="stylesheet" type="text/css" href="mystyle.css"/>             
    </head>
    <body>
        <!--JavaScript files-->
        <script src="myScript.js" type="text/javascript" charset="utf-8"></script>
         <!--Cookie consent-->
        <?php cookieConset(); ?>
        <!--Navigation bar-->
        <ul>
            <li><a href="index.php" class="w3-margin-left"><b>BH</b> Brighton and Hove</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a class="active" href="edit_account.php">Account</a></li>
            <li><a href="viewProperties.php">Your Properties</a></li>
            <li><a href="addProperty.php">Add New Property</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="">Welcome to Brighton and Hove <?php echo $_SESSION['username']; ?>!</a></li>
        </ul>
        <h1>Account</h1>
        <!--The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities-->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <table cellpadding="0" cellspacing="0" class="center">
                <tbody>
                    <tr>
                        <td axis="emailError" colspan="3"><span class="error"><?php echo $emailErr; ?></span></td>
                    </tr>
                    <!--email input-->
                    <tr> 
                        <td axis="email">Email:</td>
                        <td axis="email"><input type="text" name="email" value="<?php echo $row["email"]; ?>" /></td>
                        <td axis="emailRequired"><span class="error">*</span></td>
                    </tr>
                    <tr>
                        <td axis="passwordError" colspan="3"><span id="errorPassword" class="error"><?php echo $passwordErr; ?></span></td>
                    </tr>
                    <!--password input-->
                    <tr>
                        <td axis="password">Password:</td>
                        <td axis="password"><input type="password" name="password" id="password" onkeyup="checkPasswordStrenght()"/></td>
                        <td axis="passwordRequired"><span class="error">*</span></td>
                    </tr>
                    <tr>
                        <td colspan="3"><p><i>(leave blank if you do not want to change your password)</i></p><input type="submit" name="update" value="Update Account" /> <input type="submit" name="back" value="Back" /></td>  
                    </tr>
                </tbody>
            </table>
        </form>
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