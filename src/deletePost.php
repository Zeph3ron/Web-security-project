<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    $userId = $_SESSION['user_id'];
    if (!isset($_POST['post_id']))
    {
        echo 'Not Set';
    }
    else
    {
        $postId = $_POST['post_id'];
        $postHandler = new PostHandler();
        $post = $postHandler->getPost($postId);
        if ($post->ownerId === $userId)
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

