<!DOCTYPE html>
<html>
    <head>
        <?php
        session_start();
        if (isset($_SESSION['authenticated']))
        {
            if ($_SESSION['authenticated'] === TRUE)
            {
                header('location:mainWallPage.php');
            }
        }
        $passRecomendations = "Password reccomendations is to have at least:\\n---------------------------------------------------\\n - 8 characters long\\n - One uppercase letter (A - Z)\\n - One lowercase letter (a - z)\\n - One digit (0 - 9)\\n - One special character (Example: $ or #)";
        ?>
        <meta charset="UTF-8">  
        <title>Registration Page</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <script src="scripts/js/semantic.min.js"></script>
        <style>
            span {
                font-size: 12px;
            }
            .ui.container {
                width: 500px;
            }
            
        </style>
        <script>
            $(document).ready(function () {
                $("#passwordRec").click(function () {
                    $('.ui.modal')
                            .modal('show');
                });
                $("#goToLogin").click(function () {
                    window.location.href = "loginPage.php";
                });
            });

            //These functions are javascript and are used to dynamically check
            //password and to validate the email
            function checkPass() {
                //Obtain a reference to the needed variables
                var pass1 = document.getElementById('password1');
                var pass2 = document.getElementById('password2');
                var button1 = document.getElementById('button1');
                var confirmMessage = document.getElementById('confirmMessage');

                //Set the colors we will be using ...
                var greenColor = "#66cc66";
                var yellowColor = "#fcaf44";
                var redColor = "#ff6666";
                //Compare the values
                if (pass1.value === pass2.value) {
                    //The passwords match. 
                    //Set the color to the good color and inform
                    //the user that they have entered the correct password
                    button1.disabled = false;

                    //Regular expression for password strength
                    var re = /^(?=.*[A-Z])(?=.*[!"#$%&/()=?])(?=.*[0-9])(?=.*[a-z]).{8,}$/;
                    if (re.test(pass1.value)) {
                        confirmMessage.style.color = greenColor;
                        pass2.style.backgroundColor = greenColor;
                        confirmMessage.innerHTML = "Passwords match and it's a strong one!.";
                    } else {
                        confirmMessage.innerHTML = "Passwords match but the chosen password is very weak.";
                        pass2.style.backgroundColor = yellowColor;
                        confirmMessage.style.color = yellowColor;
                    }
                } else {
                    //The passwords do not match.
                    //Set the color to the bad color and
                    //notify the user.
                    button1.disabled = true;
                    pass2.style.backgroundColor = redColor;
                    confirmMessage.style.color = redColor;
                    confirmMessage.innerHTML = "Passwords Do Not Match!"
                }
            }
            function validateEmail() {
                //Obtain a reference to the needed variables
                var email1 = document.getElementById('emailAddress');
                var message = document.getElementById('correctEmailMessage');

                var badColor = "#ff6666";

                //Regular expression for an email
                var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

                //Keeps a reference to the password element in order to disable/enable them
                var pass1 = document.getElementById('password1');
                var pass2 = document.getElementById('password2');
                if (re.test(email1.value)) {
                    pass1.disabled = false;
                    pass2.disabled = false;
                    message.innerHTML = "";
                } else {
                    message.style.color = badColor;
                    message.innerHTML = " Wrong email format";
                    pass1.disabled = true;
                    pass2.disabled = true;
                }
            }
            function confirmPasswordStrength() {
                var password = document.getElementById('password1');

                //Regular expression for password strength
                var re = /^(?=.*[A-Z])(?=.*[!"#$%&/()=?])(?=.*[0-9])(?=.*[a-z]).{8,}$/;
                if (re.test(password.value)) {
                } else {
                    return confirm('The password you have did not pass our security check, Are you sure you want to use this password?');
                }
            }
            function validateUsername() {
                var username = document.getElementById('username');
                var message = document.getElementById('usernameConfirmMessage');
                var badColor = "#ff6666";
                //Regular expression for username length
                var re = /^[a-zA-Z0-9_-]{8,30}$/;
                if (re.test(username.value)) {
                    message.innerHTML = "";
                } else {
                    message.style.color = badColor;
                    message.innerHTML = " Must be 8 to 30 characters long and contain only normal characters and numbers.";
                }
            }
        </script>
    </head>
    <body>
        <br><br>
        <div class="ui container">
            <form class="ui form" action="src/registerAccount.php" method="post">
                <h2>&nbsp;Registration Page</h2>
                <fieldset>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" id="username" placeholder="Username" name="Username" pattern=".{8,30}" maxlength="30" required="required" onkeyup="validateUsername();" oninput="validateUsername();"/><span id="usernameConfirmMessage"></span><br/>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" id="emailAddress" placeholder="Email" name="Email" required="required" onkeyup="validateEmail();" oninput="validateEmail();"/><span id="correctEmailMessage"></span><br/>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" placeholder="Password" id="password1" required="required" disabled="true" name="Password1" onkeyup="checkPass();return false;"/>
                    </div>
                    <div class="field">
                        <input type="password" placeholder="Confirm Password" id="password2" required="required" disabled="true" name="Password2" onkeyup="checkPass();return false;"/><span id="confirmMessage"></span><br/>
                    </div>  
                    <a style="font-size: 10px;cursor: pointer" tabindex="-1" id="passwordRec">
                        Click here for information about choosing a secure password.
                    </a><br><a href="loginPage.php" style="font-size: 10px">Already have an account? Then click here!</a><br/><br/>
                    <div class="field">
                        <?php
                        require_once dirname(__FILE__) . '/securimage/securimage.php';
                        echo Securimage::getCaptchaHtml();
                        ?>
                    </div>
                    <div class="sixteen wide column">
                        <button class="ui fluid primary button"type="submit" id="button1" disabled="true" onclick="return confirmPasswordStrength();">Register Account</button>
                    </div>

                </fieldset>
            </form>
        </div>
        <div class="ui modal">
            <div class="header">
                Password recommendation
            </div>
            <div class="image content">
                <div class="description">
                    <div class="ui header">It is recommended that your password contains at least:</div>
                    <p> - 8 characters</p>
                    <p> - One uppercase letter (A - Z)</p>
                    <p> - One lowercase letter (a - z)</p>
                    <p> - One digit (0 - 9)</p>
                    <p> - One special character (Example: $ or #)</p>
                </div>
            </div>
            <div class="actions"> 
                <div class="ui positive right">
                    <button class="ui green button">Ok</button>
                </div>
            </div>
        </div>
    </body>
</html>
