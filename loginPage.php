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
        ?>
        <meta charset="UTF-8">
        <title>Login Page</title>
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
    </head>
    <body>
        <br><br>
        <div class="ui container">
            <form class="ui form" action="src/loginAccount.php" method="post">
                <h2>&nbsp;Login Page</h2>
                <fieldset>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" id="username" placeholder="Username" name="Username" required="required" minlength="5" maxlength="30"<br/><br/>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" placeholder="Password" id="password" required="required" name="Password"/>
                    </div>
                    <a href="registrationPage.php" style="font-size: 10px">Don't have an account? Register here!</a><br/><br/>
                    <button class="ui fluid primary button"type="submit">Login</button>
                </fieldset>
            </form>
        </div>
    </body>
</html>