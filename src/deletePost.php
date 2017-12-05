<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    require_once dirname(__FILE__) . '../../classes/UserHandler.php';
    
    if (!isset($_POST['post_id']))
    {
        echo 'No post has been selected.';
    }
    else
    {
        $postHandler = new PostHandler();
        $userHandler = new UserHandler();
        $postId = $_POST['post_id'];
        $userId = $_SESSION['user_id'];
        $post = $postHandler->getPost($postId);
        $userHandler ->getUserById($userId, $loggedInUser);
        if ($post->ownerId === $userId || $loggedInUser -> isAdmin)
        {
            $postHandler->deletePost($postId);
            header('location: ../mainWallPage.php');
        }
        else
        {
            echo 'Nice try...trying to delete someone elses post, you should be ashamed.';
            echo '<form action="../mainWallPage.php">'
            . '<br/>'
            . '<button class="ui fluid primary button"type="submit">Back to main post page</button>'
            . '</form>';
        }
    }
}

