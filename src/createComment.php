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
    $content = $_POST["content"];
    $postId = $_POST['post_id'];
    $validationHandler = new ValidationHandler();
    $validationHandler->validateComment($content, $valErrors);
    $validationHandler->validateToken($token, $valErrors);

    if (count($valErrors) > 0)
    {
        echo '<h3>The following errors occured whilst trying to add your comment!</h3>';
        foreach ($valErrors as $error)
        {
            echo "&nbsp;&nbsp;- " . $error . "<br/>";
        }
        echo '<form action="../displayPostPage.php?post_id='.$postId.'" method="post">'
        . '<br/>'
        . '<button class="ui fluid primary button"type="submit">Back to post</button>'
        . '</form>';
    }
    else
    {
        $postHandler = new PostHandler();
        $postHandler->commentOnPost($userId, $postId, $content);
        sleep(1);
        header('location: ../displayPostPage.php?post_id='.$postId);
    }
}

