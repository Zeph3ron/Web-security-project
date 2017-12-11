<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Create/edit post</title>
    </head>
    <body>
        <?php
        session_start();
        if (!$_SESSION['authenticated'] === true)
        {
            header('location:loginPage.php');
        }
        if (function_exists('mcrypt_create_iv'))
        {
            $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        }
        else
        {
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
        $token = $_SESSION['token'];
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Here you can create your own post. Just enter a title and the posts content and press submit.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form class="ui form" action="src/createPost.php" method="post">
                        <div class="field">
                            <label>Title</label>
                            <input name="Title" type="text" placeholder="Title" pattern=".{5,30}" required="required" title="Should be between 5 and 30 characters."/>
                        </div>
                        <div class="field">
                            <label>Post content</label>
                            <textarea name="Content" placeholder="Content" required="required" minlength="5" maxlength="1600"></textarea>
                        </div>
                        <button class="ui fluid primary button" type="submit">Submit</button>
                        <input type="hidden" name="token" value="<?php echo $token ?>"/>
                    </form>
                    <div>
                        <br/>
                        <button class="ui fluid primary button"type="submit" onclick="window.location = 'mainWallPage.php';">Back to posts</button>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
