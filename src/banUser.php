<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    if (!isset($_POST['post_id']))
    {
        header('location:../mainWallPage.php');
    }
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    require_once dirname(__FILE__) . '../../classes/UserHandler.php';

    $userId = $_SESSION['user_id'];
    $postId = $_POST['post_id'];
    $userHandler = new UserHandler();
    $userHandler->getUserById($userId, $loggedInUser);
    if ($loggedInUser->isAdmin)
    {
        $postHandler = new PostHandler();
        $post = $postHandler->getPost($postId);
        $userHandler->banUser($post->ownerId);
        echo 'This user has now been banned and cannot login. All posts belonging to the user have been removed.';
        echo '<br/><br/>'
        . '<button class="ui fluid primary button"type="submit" onclick="window.location = \'../mainWallPage.php\'";>Back to posts.</button>';
    }
}
