<?php
# Register - Allows user to register a new account with Brighton & Hove
# 
# Reference Material:
# - SQL Injection Prvention: http://www.w3schools.com/sql/sql_injection.asp
# - preg_match: http://regexr.com/
# - password_hash: http://php.net/manual/en/function.password-hash.php
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# - Captcha Generator: http://stuweb.cms.gre.ac.uk/~ha07/web/PHP/graphics.html
# - Randomly select a character string: http://stackoverflow.com/questions/4356289/php-random-string-generator
# - Password Strength Checker: https://www.youtube.com/watch?v=Zxs6Bq7vM-s
# 
# Connection & Session setup
require("common.php");
# Common functions  
require("functions.php");
# Define variables and set to empty values and false
$username = $email = $password = $captchaErr = $passwordErr = $emailErr = $usernameErr = "";
$verifyCaptcha = $verifyPassword = $verifyemail = $verifyUsername = false;
# Check cookie consent has been accepted, set cookie on and refresh page
if (isset($_POST['accept'])) {
    # Set cookie value = cookieon
    setcookie("cookiebar", "cookieon", time() + (86400 * 30), '/');
    # Redirect to home page
    header("Location: register.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: register.php");
}
# Reset form
if (isset($_POST['reset'])) {
    # Redirect to register page
    header("Location: register.php");
    # Prevent unexpected behaviour after redirection
    die("Redirecting to: register.php");
}
# Refresh captcha code
if (isset($_POST['refresh'])) {
    # Save username & email input
    $username = test_input($_POST["username"]);
    $email = test_input($_POST["email"]);
}
# Check submit button has been pressed
if (isset($_POST['submit'])) {
    # Check if its empty
    if (empty($_POST["username"])) {
        # If empty, error message and set verify to false
        $usernameErr = "Username is required";
        $verifyUsername = false;
        # Check characters inputed 
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\._<>])/", $_POST["username"])) {
        # If defined characters detected, error message and set verify to false
        $usernameErr = "Invalid characters";
        $verifyUsername = false;
    } else {
        # Store cleansed user input
        $username = test_input($_POST["username"]);
        # SQL Query
        $query = "SELECT username FROM users WHERE username = :username";
        # Bind variable to query
        $query_params = array(':username' => $username);
        try {
            # Prepare statement
            $stmt = $db->prepare($query);
            # Excute query
            $result = $stmt->execute($query_params);
            # Catch exceptions
        } catch (PDOException $ex) {
            # Prevent unexpected behaviour after redirection
            die("Failed to run query: " . $ex->getMessage());
        }
        # Fetch query results
        $row = $stmt->fetch();
        # Query results true
        if ($row["username"]) {
            # If true error message and set verify to false 
            $username = test_input($_POST["username"]);
            $usernameErr = "This username is already in use";
            $verifyUsername = false;
        } else {
            # Store cleansed user input 
            $username = test_input($_POST["username"]);
            $verifyUsername = true;
        }
    }
    # Check if its empty
    if (empty($_POST["email"])) {
        # If empty error message and set verify to false 
        $emailErr = "Email is required";
        $verifyemail = false;
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            # If false error message and set verify to false 
            $emailErr = "Invalid email format";
            $verifyemail = false;
        } else {
            # If true set to true
            $verifyemail = true;
        }
    }
    # Check if its empty
    if (empty($_POST["password"])) {
        # If empty, error message and set verify to false
        $passwordErr = "Password is required";
        $verifyPassword = false;
        # Check characters inputed 
    } else if (preg_match("/([\\\+\*\?\^\$\[\]\{\}\(\)\|\/\._<>])/", $_POST["password"])) {
        # If defined characters detected, error message and set verify to false
        $passwordErr = "Invalid characters";
        $verifyPassword = false;
    } else {
        $password = test_input($_POST["password"]);
        $verifyPassword = true;
    }
    # Check if its empty
    if (empty($_POST["captcha"])) {
        # If empty error message and set verify to false
        $captchaErr = "Captcha is required ";
        $verifyCaptcha = false;
    } else {
        if (strcasecmp($_SESSION['code'], $_POST['captcha']) != 0) {
            $captchaErr = "Invalid Code";
            $verifyCaptcha = false;
        } else {
            $verifyCaptcha = true;
        }
    }
    # Check fields are all validated
    if ($verifyUsername && $verifyemail && $verifyPassword && $verifyCaptcha == true) {
        # Generate activate code
        $activeCode = generateRandomString();
        # Hash password & passcode to store in database
        $activeHashCode = password_hash($activeCode, PASSWORD_DEFAULT, ['cost' => 5]);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 5]);
        # SQL query to insert new account
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                email,
                activeCode
            ) VALUES ( 
                :username, 
                :password, 
                :email,
                :activeCode
            ) 
        ";
        # Bind the query params
        $query_params = array(
            ':username' => test_input($_POST['username']),
            ':password' => test_input($password),
            ':email' => test_input($_POST['email']),
            ':activeCode' => $activeHashCode
        );
        try {
            # Prepare statement
            $stmt = $db->prepare($query);
            # Excute query
            $result = $stmt->execute($query_params);
            # Catch exceptions
        } catch (PDOException $ex) {         
            # Prevent unexpected behaviour after redirection
            die("Failed to run query: " . $ex->getMessage());
        }
        # Store cleansed email input
        $email = test_input($_POST['email']);
        # Send Email
        sendMail($email, $username, $activeCode);
        # Remove code from session
        unset($_SESSION['code']);
        # Set username and active sessions
        $_SESSION['username'] = $username;
        $_SESSION['active'] = "no";
        # Redirect to activate page page
        header("Location: activatePage.php");
        # Prevent unexpected behaviour after redirection
        die("Redirecting to: activatePage.php");
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
        <!--Meta defines which character set is used, page description, keywords, author, and other metadata-->
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
            <li><a href="login.php">Login</a></li>
            <li><a class="active" href="register.php">Register</a></li>
        </ul>
        <h1>Registration</h1>
        <!--The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities-->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <table cellpadding="0" cellspacing="0" class="center">
                <tbody>    
                    <tr><!--Error message for username -->
                        <td colspan="3"><span class="error"> <?php echo $usernameErr; ?></span></td>
                    </tr>                             
                    <tr>
                        <td>Username:</td> <!--Input for username -->
                        <td><input type="text" name="username" value ="<?php echo $username; ?>"/></td>
                        <td><span class="error">*</span></td>
                    </tr>
                  
                    <tr ><!-- Error message for email -->
                        <td colspan="3"><span id="errorEmail" class="error"> <?php echo $emailErr; ?></span></td>
                    </tr>         
                    <tr>
                        <td>E-mail: </td> <!--Input for email -->
                        <td><input type="text" name="email" id="email" value ="<?php echo $email; ?>" onkeyup="checkEmail()"/></td>
                        <td><span class="error">*</span></td>
                    </tr>
                    <tr ><!--Error message for password -->
                        <td colspan="3"><span id="errorPassword" class="error"> <?php echo $passwordErr; ?></span></td>
                    </tr>
                    <tr>
                        <td>Password: </td> <!--Input for password -->
                        <td><input type="password" name="password" id="password" value ="<?php echo $password; ?>" onkeyup="checkPasswordStrenght()"/></td><!--Password strength checker -->
                        <td><span class="error">*</span></td>
                    </tr>
                    <tr ><!--Error message for Captcha -->
                        <td colspan="3"><span class="error"> <?php echo $captchaErr; ?></span></td>
                    </tr>
                    <tr>
                        <td>Captcha: </td> 
                        <td><!--Input for Captcha -->
                            <input class="button-link" type="submit" name="refresh" alt="refreshCaptcha" value="Refresh &#x21ba;"/><!--Refresh Captcha -->
                            <p><img src="captcha.php" alt="captcha"/></p>
                            <input type="text" name="captcha"/>                        
                        </td>
                        <td><span class="error">*</span></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <!--Submit & Reset buttons--> 
                            <input type="submit" name="submit" alt="Submit" value="Submit"/>
                            <input type="submit" name="reset" alt="Reset" value="Reset"/>
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
        </p>
    </body>   
</html>