<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Main page</title>
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
            require_once dirname(__FILE__) . '/classes/PostHandler.php';
            require_once dirname(__FILE__) . '/classes/UserHandler.php';
            $userHandler = new UserHandler();
            $postHandler = new PostHandler();

            $user_id = $_SESSION['user_id'];
            $userHandler->getUserById($user_id, $user);
            $posts = $postHandler->getAllPosts();
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <h1 class="ui header">Hello there <?php echo htmlentities($user->userName) ?>! </h1>
                        <p>This is your landing page. Here you can get an overview of all post created by you and other users.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="ui middle aligned divided list column">
                    <?php
                    for ($i = 0; $i < count($posts); $i++)
                    {
                        echo $postHandler->getPostHtml($posts[$i]);
                    }
                    ?>
                    <form action="createPostPage.php" method="post">
                        <br/>   
                        <button class="ui fluid primary button"type="submit">Create post</button>
                    </form>

                    <form action="src/logoutAccount.php">
                        <br/>   
                        <button class="ui fluid primary button"type="submit">Log out</button>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
