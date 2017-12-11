<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Edit profile</title>
    </head>
    <body>
        <?php
        session_start();
        if (!$_SESSION['authenticated'] === true)
        {
            header('location:loginPage.php');
        }
        else
        {
            require_once dirname(__FILE__) . '/classes/UserHandler.php';
            $userHandler = new UserHandler();
            $user_id = $_SESSION['user_id'];
            $userHandler->getUserById($user_id, $user);
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Here you can edit your profile. Just change the contents of the fields to your liking and press submit.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form class="ui form" action="src/editProfile.php" method="post">
                        <div class="field">
                            <label>Username</label>
                            <p><?php echo $user->userName;?></p>
                        </div>
                        <div class="field">
                            <label>Display name - (Optional)</label>
                            <input name="Display_name" type="text" placeholder="Display name" value="<?php echo $user->displayName; ?>" pattern=".{5,30}" maxlength="30" title="Should be between 5 and 30 characters.">
                        </div>
                        <div class="field">
                            <label>Description</label>
                            <textarea name="User_description" placeholder="Write a short description of yourself" minlength="10" maxlength="200"><?php echo $user->userDescription; ?></textarea>
                        </div>
                        <button class="ui fluid primary button" type="submit">Submit changes</button>
                    </form>
                    <div>
                        <br/>
                        <button class="ui fluid primary button"type="submit" onclick="window.location = 'uploadImagePage.php';">Upload a profile image</button>
                    </div>
                    <div>
                        <br/>
                        <button class="ui fluid primary button"type="submit" onclick="window.location = 'mainWallPage.php';">Back to posts</button>
                    </div>
                </div>
            </div>
        </main> 
    </body>
</html>
