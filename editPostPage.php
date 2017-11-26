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
        else
        {
            if (!isset($_POST['post_id']))
            {
                header('location:mainWallPage.php');
            }
            else
            {
                $postId = $_POST['post_id'];
                $userId = $_SESSION['user_id'];
                require_once dirname(__FILE__) . '../classes/PostHandler.php';
                $postHandler = new PostHandler();
                $post = $postHandler->getPost($postId);
                if (!$post->ownerId === $userId)
                {
                    header('location:mainWallPage.php');
                }
            }
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Here you can edit your post. Just change the contents of the fields to your liking and press submit.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form class="ui form" action="src/editPost.php" method="post">
                        <div class="field">
                            <label>Title</label>
                            <input name="Title" type="text" placeholder="Title" value="<?php echo $post->title; ?>"/>
                        </div>
                        <div class="field">
                            <label>Post content</label>
                            <textarea name="Content" placeholder="Content" ><?php echo $post->content; ?></textarea>
                        </div>
                        <input type="hidden" name="post_id" value="<?php echo $post->id ?>">
                        <button class="ui fluid primary button" type="submit">Submit changes</button>
                    </form>
                </div>
            </div>
        </main> 
    </body>
</html>
