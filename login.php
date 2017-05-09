<?php
# Login - Allows returning verified members to authenticate with the site using their username and password
# 
# Reference Material:
# - password_hash: http://php.net/manual/en/function.password-hash.php 
# - SQL Injection Prvention: http://www.w3schools.com/sql/sql_injection.asp
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# - preg_match: http://regexr.com/
#
# Connection & Session setup
require("common.php");
# Common functions
require("functions.php");
# Check if username is set, used to prevent unauthorised access
if (isset($_SESSION['username'])) {
    # Check if active section is set
    if (isset($_SESSION['active'])) {
        # if set check if value is false
        if ($_SESSION['active'] == "no") {
            # redirect to home page
            header("Location: index.php");
            # Prevent unexpected behaviour after redirection
            die("Redirecting to: index.php");
        } else {
            # If false redirect to home page
            header("Location: index.php");
            # Prevent unexpected behaviour after redirection
            die("Redirecting to: index.php");
        }
    }
}
# Define variables and set to empty values and false
$usernameErr = $passwordErr = $submitted_username = $username = $email = $password = "";
$verifyPassword = $verifyUsername = false;
# Check if cookie user is set
if (isset($_COOKIE["user"])) {
    # if set output results to variable
    $submitted_username = $_COOKIE["user"];
}
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: login.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: login.php");
}
// This if statement checks to determine whether the login form has been submitted
// If it has, then the login code is run, otherwise the form is displayed
if (!empty($_POST)) {
    $submitted_username = $_POST["username"];
    $password = $_POST["password"];
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
        $verifyUsername = false;
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\._<>-])/", $_POST["username"])) {
        $usernameErr = "Invalid username";
        $verifyUsername = false;
    } else {
        $verifyUsername = true;
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $verifyPassword = false;
    } else {
        $verifyPassword = true;
    }
    if ($verifyUsername && $verifyPassword == true) {
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        $login_ok = false;
        $query = "SELECT username,password,activeUser FROM users WHERE username = :username";
        $query_params = array(
            ':username' => $username,
        );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        } catch (PDOException $ex) {
            die("Failed to run query: " . $ex->getMessage());
        }
        $row = $stmt->fetch();
        if ($row) {
            if (password_verify($password, $row['password']) == true) {
                if ($row['activeUser'] == "no") {
                    $_SESSION['username'] = $username;
                    $_SESSION['active'] = $row["activeUser"];
                    header("Location: activatePage.php");
                    die("Redirecting to: activatePage.php");
                    print("Login Failed.");
                } else {
                    $login_ok = true;
                }
            }
        }
        if ($login_ok) {
            $_SESSION['username'] = $username;
            $_SESSION['active'] = $row["activeUser"];
            if (isset($_COOKIE["cookiebar"])) {
                if (isset($_POST["remember"])) {
                    setcookie("user", $username, time() + (86400 * 30), '/');
                    setcookie("remember", "1", time() + (86400 * 30), '/');
                } else if (!isset($_POST["remember"])) {
                    setcookie("remember", "0", time() + (86400 * 30), '/');
                }
            }
            # Redirect to home page
            header("Location: index.php");
            # Prevent unexpected behaviour after redirection
            die("Redirecting to: index.php");
        } else {
            # Login failure, print error message
            $usernameErr = "The username or password is incorrect";
            # Show them their username again so all they have to do is enter a new password.  
            # The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
        }
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
            <li><a href="index.php"><b>BH</b> Brighton and Hove</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a class="active" href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
        <h1>Login</h1>
        <!--Input form-->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <table cellpadding="0" cellspacing="0" class="center">
                <tbody>
                    <tr>
                        <td axis="usernameError" colspan="3"><span class="error"><?php echo $usernameErr; ?></span></td>
                    </tr>
                    <tr>
                        <td axis="username">Username:</td>
                        <td axis="username"><input type="text" name="username" value="<?php echo $submitted_username; ?>" /></td>
                        <td axis="usernameRequired"><span class="error">*</span></td>
                    </tr>


                    <tr>
                        <td axis="passwordError" colspan="3"><span class="error"><?php echo $passwordErr; ?></span></td>
                    </tr>
                    <tr>
                        <td axis="password">Password:</td>
                        <td axis="password"><input type="password" name="password" value="<?php echo $password; ?>" /></td>
                        <td axis="passwordRequired"><span class="error">*</span></td>
                    </tr>
                    <tr>
                        <td axis="rememberMe" colspan="3">Remember me <input type="checkbox" name="remember" <?php
                            if (isset($_COOKIE['user'])) {
                                echo 'checked="checked"';
                            }
                            ?>/>
                            <input type="submit" name="submit" value="Login"/>
                        </td>  
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
            <a href="http://www.w3.org/WAI/WCAG1AA-Conformance">
                <img src="http://www.w3.org/WAI/wcag1AA" alt="Level Double-A conformance icon,W3C-WAI Web Content Accessibility Guidelines 1.0" class="validate"/>
            </a>
        </p>

    </body>
</html>