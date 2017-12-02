<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Post page</title>
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
            $userId = $_SESSION['user_id'];
            if (!isset($_GET['post_id']) || $_GET['post_id'] === '')
            {
                header('location:mainWallPage.php');
            }
            else
            {
                require_once dirname(__FILE__) . '/classes/PostHandler.php';
                require_once dirname(__FILE__) . '/classes/UserHandler.php';
                $postId = $_GET['post_id'];
                $postHandler = new PostHandler();
                $userHandler = new UserHandler();

                $post = $postHandler->getPost($postId);
                if ($post === null)
                {
                    header('location:mainWallPage.php');
                }
                else
                {
                    $userHandler->getUserById($post->ownerId, $postOwner);
                    $nrOfPosts = $postHandler->getNrOfPosts($postOwner->id);
                }
            }
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui cards">
                        <div class="card">
                            <div class="content">
                                <img class="right floated mini ui image" src="resources/images/bill-small.png">
                                <div class="header">
                                    Created by 
                                    <?php
                                    if (strlen(trim($postOwner->displayName)) == 0)
                                    {
                                        echo htmlentities($postOwner->userName);
                                    }
                                    else
                                    {
                                        echo htmlentities($postOwner->displayName);
                                    }
                                    ?>
                                </div>
                                <div class="description">
                                    <?php echo htmlentities($postOwner->userDescription) ?>
                                </div>
                                <div class="meta">
                                    <br/>
                                    This user has <?php echo htmlentities($nrOfPosts) ?> posts. 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <h1 class="ui header"><?php echo htmlentities($post->title) ?></h1>
                        <p><?php echo htmlentities($post->content) ?>.</p>
                    </div>
                </div>
            </div>
            <?php
            if ($userId == $postOwner->id)
            {
                echo '<div><form action="editPostPage.php" method="post">'
                . '<br/>'
                . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                . '<button class="ui fluid primary button"type="submit">Edit post</button>'
                . '</form></div>';
                echo '<div><form action="src/deletePost.php" method="post">'
                . '<br/>'
                . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                . '<button class="ui fluid primary button"type="submit">Delete post</button>'
                . '</form></div>';
            }
            ?>
            <div>
                <br/>
                <button class="ui fluid primary button"type="submit" onclick="window.location = 'mainWallPage.php';" >Back to posts</button>
            </div>
        </main>
    </body>
</html>
