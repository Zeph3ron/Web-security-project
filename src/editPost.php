<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/ValidationHandler.php';
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    $userId = $_SESSION['user_id'];
    if (!isset($_POST['post_id']))
    {
        header('location:mainWallPage.php');
    }
    else
    {
        $postId = $_POST['post_id'];

        $valErrors = array();
        $title = $_POST["Title"];
        $content = $_POST["Content"];

        $valHandler = new ValidationHandler();
        $valHandler->validatePostTitle($title, $valErrors);
        $valHandler->validatePostContent($content, $valErrors);

        if (count($valErrors) > 0)
        {
            echo '<h3>The following errors occured whilst trying to edit your post!</h3>';
            foreach ($valErrors as $error)
            {
                echo "&nbsp;&nbsp;- " . $error . "<br/>";
            }
            echo '<form action="../editPostPage.php" method="post">'
            . '<br/>'
            . '<input type="hidden" name="post_id" value="' . $postId . '"/>'
            . '<button class="ui fluid primary button"type="submit">Back to edit post</button>'
            . '</form>';
        }
        else
        {
            $postHandler = new PostHandler();
            $post = $postHandler->getPost($postId);
            if ($post->ownerId === $userId)
            {
                $postHandler->updatePost($postId, $title, $content);
                header('location: ../mainWallPage.php');
            }
            else
            {
                echo 'Nice try...';
                echo '<form action="../editPostPage.php" method="post">'
                . '<br/>'
                . '<input type="hidden" name="post_id" value="' . $postId . '"/>'
                . '<button class="ui fluid primary button"type="submit">Back to edit post</button>'
                . '</form>';
            }
        }
    }
}

