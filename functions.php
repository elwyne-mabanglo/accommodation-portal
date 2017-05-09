<?php
# Functions - Common fucntions used throughout the system
#
# Reference Material:
# - Data Validation: http://www.w3schools.com/php/php_form_validation.asp
# - Randomly select a character string: http://stackoverflow.com/questions/4356289/php-random-string-generator
#
# Standard query 
function query($sql) {
    # database details
    $servername = "mysql.cms.gre.ac.uk";
    $username = "me324";
    $password = "12El05ma90";
    $dbname = "me324";
    # setup connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}
# activate account 
function activateAccount($sql) {
    $servername = "mysql.cms.gre.ac.uk";
    $username = "me324";
    $password = "12El05ma90";
    $dbname = "me324";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    $conn->close();
}
# Data Validation 
function test_input($data) {
    # Removes white spaces before and after
    $data = trim($data); 
    # Removes '/'
    $data = stripslashes($data); 
    # Convert speacial characters
    $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
# Randomly select a character string 
function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
# Test alt image name is not empty
function testAlt($data) {
    if (empty($data)) {
        $data = "No Picture Uploaded";
        return $data;
    } else {
        return $data;
    }
}
# Cookie consent
function cookieConset() {
    # If cookie consent not accepted display cookie consent
    if (!isset($_COOKIE["cookiebar"])) {
        ?>
        <!--Cookie form-->
        <!--The use of htmlentities prevents XSS attacks, htmlentities � Convert all applicable characters to HTML entities-->
        <form id="cookieTable" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!--Cookie Message-->
            <p>This website makes use of cookies to store username and last search term when the remember me/search is ticked.
                <!--Accept button-->
                <input type="submit" value="Accept" name="accept"></input> 
                <!--Hyperlink to how to enable cookies on browser-->
                Cookies Disabled? <a href="http://www.wikihow.com/Enable-Cookies-in-Your-Internet-Web-Browser">Find out more</a></p>              
        </form>
        <?php
    };
}
# Send Email 
function sendMail($email, $username, $activeCode) {
    # subject message
    $subject = "Brighton and Hove's Homes - Account Verification";
    # email message
    $message = "
        <html>
        <head>
        <title>Brighton and Hove's Homes - Account Verification</title>
        </head>
        <body>
        <p>Welcome to Brighton and Hove's Homes $username! Please find your Verification Code below</p>      
        <p>Verification Code $activeCode</p>
        <p>http://stuweb.cms.gre.ac.uk/~me324/BrightonHove/login.php</p>
        </body>
        </html>
        ";
    # email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <me324@greenwich.ac.uk>' . "\r\n";
    $headers .= 'Cc: me324@greenwich.ac.uk' . "\r\n";
    # send email
    mail($email, $subject, $message, $headers);
}
