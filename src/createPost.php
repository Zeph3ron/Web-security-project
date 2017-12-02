<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/ValidationHandler.php';
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    $userId = $_SESSION['user_id'];

    $valErrors = array();
    $title = $_POST["Title"];
    $content = $_POST["Content"];

    $valHandler = new ValidationHandler();
    $valHandler->validatePostTitle($title, $valErrors);
    $valHandler->validatePostContent($content, $valErrors);

    if (count($valErrors) > 0)
    {
        echo '<h3>The following errors occured whilst trying to create your post!</h3>';
        foreach ($valErrors as $error)
        {
            echo "&nbsp;&nbsp;- " . $error . "<br/>";
        }
        echo '<form action="../createPostPage.php" method="post">'
        . '<br/>'
        . '<button class="ui fluid primary button"type="submit">Back to create post</button>'
        . '</form>';
    }
    else
    {
        $postHandler = new PostHandler();
        $postHandler->createPost($userId, $title, $content);
        header('location: ../mainWallPage.php');
    }
}

