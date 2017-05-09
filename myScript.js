//<![CDATA[
// myScripts - Common scripts used thoughout the system
// 
// Reference Material:
// - Password Strenght: https://www.youtube.com/watch?v=Zxs6Bq7vM-s
//
// check password strength
function checkPasswordStrenght() {
    // get password element
    var passwordTextBox = document.getElementById("password");
    // get value
    var password = passwordTextBox.value;
    // set score
    var passwordScore = 0;
    // lower cast words detected increase score
    if (/[a-z]/.test(password)) {
        passwordScore += 20;
    }
    // uppper case detected increase score
    if (/[A-Z]/.test(password)) {
        passwordScore += 20;
    }
    // numbers deteted increase score
    if (/[\d]/.test(password)) {
        passwordScore += 20;
    }
    // password length greater than 8 increase score
    if (password.length >= 8) {
        passwordScore += 20;
    }
    // variable to store strenght message
    var strenght = "";
    // variable to store background colour
    var backgroundColor = "";
    // depending on the score will change the message and background colour
    if (passwordScore >= 50) {
        strenght = "Strong Password";
        backgroundColor = "green";
    } else if (passwordScore >= 40) {
        strenght = "Medium Password";
        backgroundColor = "gray";
    } else if (passwordScore >= 30) {
        strenght = "Weak Password";
        backgroundColor = "maroon";
    } else {
        strenght = "Very Weak Password";
        backgroundColor = "red";
    }
    document.getElementById("errorPassword").innerHTML = strenght;
    passwordTextBox.style.color = "white";
    passwordTextBox.style.backgroundColor = backgroundColor;
}
// Check email format
function checkEmail() {
    // get email element
    var passwordTextBox = document.getElementById("email");
    // retrieve value
    var password = passwordTextBox.value;
    // check atpos position
    var atpos = password.indexOf("@");
    // check fullstop position
    var dotpos = password.lastIndexOf(".");
    // check if password is greater than 0
    if (password.length > 0) {
        // validate email
        if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= password.length) {
            // error message if missing characters are not present
            document.getElementById("errorEmail").innerHTML = "Invalid email format";
        } else {
            // if not true don't do anything
            document.getElementById("errorEmail").innerHTML = "";
        }
    } else {
        // if email less than 0 don't do anything
        document.getElementById("errorEmail").innerHTML = "";
    }
}
//]]>   