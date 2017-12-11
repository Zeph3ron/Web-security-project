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
                    $userHandler->getUserById($userId, $loggedInUser);

                    $nrOfPosts = $postHandler->getNrOfPosts($postOwner->id);
                    $nrOfVotes = $postHandler->getVotesForPost($postId);
                    $comments = $postHandler->getCommentsForPost($postId);
                }
            }
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
                    <div class="ui cards">
                        <div class="card">
                            <div class="content">
                                <img class="right floated mini ui image" src="src/getProfileImage.php?image=<?php echo $post->profileImagePath; ?>">
                                <div class="header">
                                    Created by 
                                    <?php
                                    $adminConcat = "";
                                    if ($postOwner->isAdmin)
                                    {
                                        $adminConcat .= " (admin)";
                                    }
                                    echo $postOwner->nameToShow . $adminConcat;
                                    ?>
                                </div>
                                <div class="description">
                                    <?php echo $postOwner->userDescription?>
                                </div>
                                <div class="meta">
                                    <br/>
                                    This user has <?php echo $nrOfPosts ?> posts.
                                    <?php
                                    if ($loggedInUser->isAdmin)
                                    {
                                        echo '<br/><br/>'
                                        . '<form action="src/banUser.php" method="post">'
                                        . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                                        . '<button class="ui fluid negative ui button"type="submit">Ban user</button>'
                                        . '</form>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <h1 class="ui header"><?php echo $post->title ?></h1>
                        <p><?php echo $post->content ?>.</p>
                        <?php echo '<br/><i>This post has received ' . $nrOfVotes . ' votes.</i>' ?>
                    </div>
                </div>
            </div>
            <?php
            if ($loggedInUser->id == $postOwner->id)
            {
                echo '<div><form action="editPostPage.php" method="post">'
                . '<br/>'
                . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                . '<button class="ui fluid primary button"type="submit">Edit post</button>'
                . '</form></div>';
            }
            else
            {
                echo '<div><form action="src/votePost.php" method="post">'
                . '<br/>'
                . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                . '<button class="positive ui button"><i class="thumbs up outline icon"></i>Cast vote!</button>'
                . '</form></div>';
            }
            if ($loggedInUser->id == $postOwner->id || $loggedInUser->isAdmin)
            {
                echo '<div><form action="src/deletePost.php" method="post">'
                . '<br/>'
                . '<input type="hidden" value="' . $post->id . '" name="post_id" />'
                . '<button class="ui fluid primary button"type="submit">Delete post</button>'
                . '</form></div>';
            }
            ?>
            <div>
                <br/>
                <button class="ui fluid primary button"type="submit" onclick="window.location = 'mainWallPage.php';">Back to posts</button>
            </div>
            <div class="row">
                <div class="column">
                    <br/>
                    <div class="ui comments">
                        <h3 class="ui dividing header">Comments</h3>
                        <?php
                        for ($i = 0; $i < count($comments); $i++)
                        {
                            echo $postHandler->getCommentHtml($comments[$i]);
                        }
                        ?>
                    </div>
                    <form class="ui reply form" action="src/createComment.php" method="post">
                        <input type="hidden" value="<?php echo $post->id ?>" name="post_id" />
                        <input type="hidden" name="token" value="<?php echo $token ?>"/>
                        <div class="field">
                            <textarea name="content" maxlength="200" required="required"></textarea>
                        </div>
                        <button class="ui blue labeled submit icon button">
                            <i class="icon edit"></i> Add Comment
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
